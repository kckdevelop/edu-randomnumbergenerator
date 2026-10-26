<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameSetting;
use App\Models\SpinLog;
use App\Models\DepositRequest;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    /**
     * Tampilan antarmuka slot untuk Player (Siswa).
     */
    public function index()
    {
        $user = Auth::user();
        $recentLogs = $user->spinLogs()->latest()->limit(10)->get();
        $recentDeposits = $user->depositRequests()->latest()->limit(10)->get();
        return view('player.slot', compact('user', 'recentLogs', 'recentDeposits'));
    }

    /**
     * Endpoint API POST /api/deposit
     */
    public function requestDeposit(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => ['required', 'numeric', 'min:10000', 'max:500000'],
        ]);

        $amount = (float) $request->amount;

        $deposit = DepositRequest::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Permintaan deposit sebesar Rp " . number_format($amount, 0, ',', '.') . " berhasil dikirim! Menunggu ACC (persetujuan) Admin.",
            'deposit' => [
                'id' => $deposit->id,
                'amount_formatted' => number_format($deposit->amount, 0, ',', '.'),
                'status' => $deposit->status,
                'created_at' => $deposit->created_at->format('H:i:s - d M Y'),
            ],
        ]);
    }

    public function userDeposits()
    {
        $user = Auth::user();
        $deposits = $user->depositRequests()->latest()->limit(10)->get()->map(function ($d) {
            return [
                'id' => $d->id,
                'amount_formatted' => number_format($d->amount, 0, ',', '.'),
                'status' => $d->status,
                'created_at' => $d->created_at->format('H:i:s - d M Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'deposits' => $deposits,
        ]);
    }

    /**
     * Endpoint API POST /api/spin
     *
     * KODE INI ADALAH BUKTI UTAMA MANIPULASI BANDAR:
     * Hasil putaran slot TIDAK DIPENGARUHI KEBERUNTUNGAN ACAK (RNG),
     * melainkan DI-DIKTE 100% oleh kolom `next_spin_result` di database yang diatur oleh Admin.
     *
     * Mode Spin Gratis: Jika `is_free_spin = true`, saldo TIDAK dipotong untuk taruhan.
     * Tidak ada kuota—mode ini hanya toggle dari sisi player untuk simulasi edukasi.
     */
    public function spin(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi Nilai Bet & Mode Spin Gratis
        // Mode Spin Gratis: saldo TIDAK dipotong, tidak ada kuota (hanya toggle edukasi)
        $isFreeSpin = $request->boolean('is_free_spin');

        $request->validate([
            'bet_amount' => ['required', 'numeric', 'min:1000'],
        ]);

        $betAmount = (float) $request->bet_amount;

        $grid = [];
        $multiplier = 0.0;
        $winAmount = 0.0;
        $statusLabel = '';
        $message = '';
        $winningLines = [];
        $nearMiss = false;
        $nearMissDetail = null;

        if ($isFreeSpin) {
            // === MODE SPIN GRATIS: ABAIKAN KEMENANGAN & TARUHAN ===
            // Spin gratis murni simulasi acak: saldo tidak dipotong, kemenangan diabaikan (win = 0, multiplier = 0),
            // dan tidak ada payline kemenangan yang diaktifkan.
            $effectiveBetCost = 0.0;
            $multiplier = 0.0;
            $winAmount = 0.0;
            $winningLines = [];
            $nearMiss = false;
            $nearMissDetail = null;
            $manipulationSetting = 'free_spin';
            $statusLabel = '[SPIN GRATIS] Simulasi Acak (Kemenangan Diabaikan)';
            $message = 'Mode Spin Gratis: Putaran simulasi acak tanpa taruhan dan kemenangan diabaikan.';

            // Acak 15 simbol visual murni untuk simulasi
            $pool = ['💎 PERMATA', '💚 GIOK', '💙 SAFIR', '🍷 CAWAN', '💍 CINCIN', '⏳ JAM PASIR', '👑 MAHKOTA', '⚡ ZEUS'];
            for ($i = 0; $i < 15; $i++) {
                $grid[] = $pool[array_rand($pool)];
            }
        } else {
            $effectiveBetCost = (float) $request->bet_amount;

            // Cek apakah saldo mencukupi
            if ($user->balance < $effectiveBetCost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saldo virtual Anda tidak mencukupi untuk melakukan bet ini!',
                ], 400);
            }

            // 2. CEK SETTING MANIPULASI DARI ADMIN (BANDAR)
            $setting = GameSetting::firstOrCreate(
                ['user_id' => $user->id],
                ['next_spin_result' => 'lose'] // Default sistem: Siswa dibuat KKN / Rungkad
            );

            $manipulationSetting = $setting->next_spin_result;

            // 3. DIKTE HASIL PUTARAN BERDASARKAN KEPUTUSAN BANDAR
            if ($manipulationSetting === 'lose') {
                // === MODUS 1: PASTI RUNGKAD / KALAH (ZONK) DENGAN PSIKOLOGI NEAR-MISS ===
                // Bandar membuat grid visual TANPA kombinasi menang di 10 payline,
                // tetapi sengaja memasang 2 scatter petir / 2 mahkota berurutan untuk sensasi 'hampir menang'
                $gridData = $this->generateLosingGridWithNearMiss();
                $grid = $gridData['grid'];
                $nearMiss = $gridData['near_miss'];
                $nearMissDetail = $gridData['near_miss_detail'];
                $multiplier = 0.0;
                $winAmount = 0.0;
                $statusLabel = 'Pasti Rungkad / Zonk (Dikontrol Backend Bandar)';
                $message = 'AKUN ANDA SEDANG DI-SETTING RUNGKAD OLEH BANDAR! ' . ($nearMiss ? 'Perhatikan bagaimana bandar memberi 2 scatter (hampir menang) untuk memancing Anda terus bermain!' : 'Saldo bet Anda lunas diambil sistem.');

            } elseif ($manipulationSetting === 'win') {
                // === MODUS 2: PASTI MENANG JACKPOT (KOMBINASI PAYLINE + PERKALIAN ZEUS) ===
                $multiplier = (float) rand(50, 500); // Perkalian jackpot 50x - 500x
                $winAmount = $betAmount * $multiplier;
                $gridData = $this->generateJackpotGrid($multiplier, $betAmount);
                $grid = $gridData['grid'];
                $winningLines = $gridData['winning_lines'];
                $statusLabel = 'Pasti Menang Jackpot (Dikontrol Backend Bandar)';
                $message = "SELAMAT! Anda mendapatkan Jackpot x{$multiplier}! (Hasil manipulasi Admin)";

            } elseif ($manipulationSetting === 'random_low_win') {
                // === MODUS 3: PANCINGAN / MENANG KECIL (1-2 PAYLINE RENDAH) ===
                $multiplier = (float) (rand(120, 250) / 100); // 1.2x - 2.5x
                $winAmount = $betAmount * $multiplier;
                $gridData = $this->generateLowWinGridWithPaylines($multiplier, $betAmount);
                $grid = $gridData['grid'];
                $winningLines = $gridData['winning_lines'];
                $statusLabel = 'Menang Kecil / Pancingan (Dikontrol Backend Bandar)';
                $message = "Anda Menang Kecil x{$multiplier}. Ini teknik pancingan psikologis judi online agar Anda terus mencoba!";
            }
        }

        // 4. KELOLA SALDO SISWA (PEMOTONGAN BET JIKA BUKAN FREE SPIN & PENAMBAHAN KEMENANGAN)
        $user->balance = $user->balance - $effectiveBetCost + $winAmount;
        $user->save();

        // 5. CATAT LOG AUDIT MANIPULASI
        SpinLog::create([
            'user_id'           => $user->id,
            'bet_amount'        => $effectiveBetCost,
            'result_amount'     => $winAmount,
            'status_manipulasi' => $statusLabel,
            'multiplier'        => $multiplier,
            'is_free_spin'      => $isFreeSpin,
            'grid_pattern'      => $grid,
        ]);

        // 6. RETURN RESPONS API UNTUK KONTROL VISUAL SLOT FRONTEND
        return response()->json([
            'success'           => true,
            'is_free_spin'      => $isFreeSpin,
            'bet_amount'        => $betAmount,
            'effective_bet_cost'=> $effectiveBetCost,
            'win_amount'        => $winAmount,
            'multiplier'        => $multiplier,
            'new_balance'       => (float) $user->balance,
            'status_manipulasi' => $manipulationSetting,
            'status_label'      => $statusLabel,
            'message'           => $message,
            'grid'              => $grid, // 15 simbol visual untuk slot grid 5x3
            'winning_lines'     => $winningLines, // Garis payline yang menang
            'near_miss'         => $nearMiss,
            'near_miss_detail'  => $nearMissDetail,
            'paylines_count'    => 10,
        ]);
    }

    /**
     * Definisi 10 Payline Standar Mesin Slot 5x3:
     * Kolom (Reel): 0 s/d 4 | Baris: 0 (atas), 1 (tengah), 2 (bawah)
     * Index = (baris * 5) + kolom
     */
    public const PAYLINES = [
        1 => ['name' => 'Line 1 (Tengah)',     'indices' => [5, 6, 7, 8, 9]],
        2 => ['name' => 'Line 2 (Atas)',       'indices' => [0, 1, 2, 3, 4]],
        3 => ['name' => 'Line 3 (Bawah)',      'indices' => [10, 11, 12, 13, 14]],
        4 => ['name' => 'Line 4 (V-Shape)',    'indices' => [0, 6, 12, 8, 4]],
        5 => ['name' => 'Line 5 (Piramida)',   'indices' => [10, 6, 2, 8, 14]],
        6 => ['name' => 'Line 6 (Top-Zigzag)', 'indices' => [0, 1, 7, 3, 4]],
        7 => ['name' => 'Line 7 (Bot-Zigzag)', 'indices' => [10, 11, 7, 13, 14]],
        8 => ['name' => 'Line 8 (Puncak)',     'indices' => [5, 1, 2, 3, 9]],
        9 => ['name' => 'Line 9 (Lembah)',     'indices' => [5, 11, 12, 13, 9]],
        10=> ['name' => 'Line 10 (Diagonal)',  'indices' => [0, 6, 7, 8, 14]],
    ];

    /**
     * Membuat kombinasi 15 simbol slot Zonk (Kalah) dengan psikologi Near-Miss.
     * Tidak ada 3 simbol cocok di 10 payline dari Reel 1 (kiri).
     */
    private function generateLosingGridWithNearMiss(): array
    {
        // Koleksi simbol umum
        $pool = ['💎 PERMATA', '💚 GIOK', '💙 SAFIR', '🍷 CAWAN', '💍 CINCIN', '⏳ JAM PASIR'];
        
        // Buat grid dasar acak yang diatur agar kolom 0, 1, 2 tidak bersambung 3
        $grid = [
            '💎 PERMATA', '💚 GIOK',    '💙 SAFIR',   '💍 CINCIN',   '⏳ JAM PASIR',
            '👑 MAHKOTA', '👑 MAHKOTA', '🍷 CAWAN',   '💎 PERMATA',  '💚 GIOK', // Line 1 hampir menang (2 Mahkota lalu putus)
            '⚡ ZEUS',    '💙 SAFIR',   '⚡ ZEUS',    '💍 CINCIN',   '🍷 CAWAN', // 2 Scatter (Reel 1 & 3) memicu tensi
        ];

        // Variasi near-miss random agar tidak monoton
        $nearMissType = rand(1, 2);
        if ($nearMissType === 1) {
            $nearMiss = true;
            $nearMissDetail = 'Hampir dapat 3 Scatter Zeus! Simbol petir muncul di Reel 1 & 3 tapi meleset di Reel 4 & 5.';
        } else {
            $nearMiss = true;
            $nearMissDetail = 'Hampir tembus 5 Mahkota! 2 Mahkota berurutan di Reel 1 & 2 terputus oleh Cawan di Reel 3.';
            $grid[10] = '💍 CINCIN';
            $grid[12] = '💚 GIOK';
        }

        return [
            'grid' => $grid,
            'near_miss' => $nearMiss,
            'near_miss_detail' => $nearMissDetail,
        ];
    }

    /**
     * Membuat kombinasi Menang Kecil (Pancingan / 1-2 Payline Aktif)
     */
    private function generateLowWinGridWithPaylines(float $multiplier, float $betAmount): array
    {
        // Grid default
        $grid = [
            '💙 SAFIR',    '💍 CINCIN',   '💚 GIOK',    '⏳ JAM PASIR', '💎 PERMATA',
            '💎 PERMATA',  '💎 PERMATA',  '💎 PERMATA', '🍷 CAWAN',     '💙 SAFIR', // Line 1: 3x Permata = Pancingan
            '🍷 CAWAN',    '⏳ JAM PASIR','💍 CINCIN',  '💚 GIOK',      '👑 MAHKOTA',
        ];

        $winningLines = [
            [
                'line_number'  => 1,
                'name'         => 'Line 1 (Tengah)',
                'symbol'       => '💎',
                'label'        => 'PERMATA MERAH',
                'count'        => 3,
                'multiplier'   => $multiplier,
                'indices'      => [5, 6, 7],
                'win_amount'   => $betAmount * $multiplier,
            ]
        ];

        return [
            'grid' => $grid,
            'winning_lines' => $winningLines,
        ];
    }

    /**
     * Membuat kombinasi Menang Jackpot (Zeus Scatter + Multi Paylines)
     */
    private function generateJackpotGrid(float $multiplier, float $betAmount): array
    {
        // Grid spektakuler: Line 1 penuh 5x Mahkota, Line 4 4x Jam Pasir, Zeus Scatter
        $grid = [
            '⏳ JAM PASIR', '💍 CINCIN',   '⚡ ZEUS x' . (int)$multiplier, '💍 CINCIN',   '⏳ JAM PASIR',
            '👑 MAHKOTA',   '👑 MAHKOTA',  '👑 MAHKOTA',                   '👑 MAHKOTA',  '👑 MAHKOTA', // Line 1: 5x Mahkota!
            '⚡ ZEUS',      '⏳ JAM PASIR', '👑 MAHKOTA',                   '⏳ JAM PASIR', '⚡ ZEUS',
        ];

        $winningLines = [
            [
                'line_number'  => 1,
                'name'         => 'Line 1 (Tengah)',
                'symbol'       => '👑',
                'label'        => 'MAHKOTA OLYMPUS (5X MATCH)',
                'count'        => 5,
                'multiplier'   => $multiplier,
                'indices'      => [5, 6, 7, 8, 9],
                'win_amount'   => $betAmount * $multiplier,
            ],
            [
                'line_number'  => 4,
                'name'         => 'Line 4 (V-Shape)',
                'symbol'       => '⏳',
                'label'        => 'JAM PASIR EMAS (COMBO)',
                'count'        => 3,
                'multiplier'   => 5.0,
                'indices'      => [0, 6, 12],
                'win_amount'   => $betAmount * 5.0,
            ]
        ];

        return [
            'grid' => $grid,
            'winning_lines' => $winningLines,
        ];
    }

    /**
     * Fallback method lama untuk kompatibilitas
     */
    private function generateLosingGrid(): array
    {
        return $this->generateLosingGridWithNearMiss()['grid'];
    }

    private function generateWinningGrid(float $multiplier): array
    {
        return $this->generateJackpotGrid($multiplier, 10000)['grid'];
    }

    private function generateLowWinGrid(): array
    {
        return $this->generateLowWinGridWithPaylines(1.5, 10000)['grid'];
    }
}
