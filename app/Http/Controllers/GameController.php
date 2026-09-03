<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameSetting;
use App\Models\SpinLog;
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
        return view('player.slot', compact('user', 'recentLogs'));
    }

    /**
     * Endpoint API POST /api/spin
     * 
     * KODE INI ADALAH BUKTI UTAMA MANIPULASI BANDAR:
     * Hasil putaran slot TIDAK DIPENGARUHI KEBERUNTUNGAN ACAK (RNG),
     * melainkan DI-DIKTE 100% oleh kolom `next_spin_result` di database yang diatur oleh Admin.
     */
    public function spin(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi Nilai Bet
        $request->validate([
            'bet_amount' => ['required', 'numeric', 'min:1000', "max:{$user->balance}"],
        ]);

        $betAmount = (float) $request->bet_amount;

        // Cek apakah saldo mencukupi
        if ($user->balance < $betAmount) {
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
        $grid = [];
        $multiplier = 0.0;
        $winAmount = 0.0;
        $statusLabel = '';
        $message = '';

        if ($manipulationSetting === 'lose') {
            // === MODUS 1: PASTI RUNGKAD / KALAH (ZONK) ===
            // Bandar membuat grid visual acak TANPA ADA kombinasi simbol yang cocok atau scatter petir.
            $grid = $this->generateLosingGrid();
            $multiplier = 0;
            $winAmount = 0;
            $statusLabel = 'Pasti Rungkad / Zonk (Dikontrol Backend Bandar)';
            $message = 'AKUN ANDA SEDANG DI-SETTING RUNGKAD OLEH BANDAR! Saldo bet Anda lunas diambil sistem.';

        } elseif ($manipulationSetting === 'win') {
            // === MODUS 2: PASTI MENANG JACKPOT ===
            // Bandar mengirimkan petir perkalian tinggi (Zeus Lightning) dan Mahkota Emas.
            $multiplier = (float) rand(50, 500); // Perkalian jackpot 50x - 500x
            $grid = $this->generateWinningGrid($multiplier);
            $winAmount = $betAmount * $multiplier;
            $statusLabel = 'Pasti Menang Jackpot (Dikontrol Backend Bandar)';
            $message = "SELAMAT! Anda mendapatkan Jackpot x{$multiplier}! (Hasil manipulasi Admin)";

        } elseif ($manipulationSetting === 'random_low_win') {
            // === MODUS 3: PANCINGAN / MENANG KECIL ===
            // Bandar memberikan kemenangan kecil (1.2x - 2.5x) untuk memancing emosi siswa agar terus bermain.
            $multiplier = (float) (rand(120, 250) / 100); // 1.2x - 2.5x
            $grid = $this->generateLowWinGrid();
            $winAmount = $betAmount * $multiplier;
            $statusLabel = 'Menang Kecil / Pancingan (Dikontrol Backend Bandar)';
            $message = "Anda Menang Kecil x{$multiplier}. Ini teknik pancingan psikologis judi online agar Anda terus mencoba!";
        }

        // 4. KELOLA SALDO SISWA (PEMOTONGAN BET & PENAMBAHAN KEMENANGAN)
        $user->balance = $user->balance - $betAmount + $winAmount;
        $user->save();

        // 5. CATAT LOG AUDIT MANIPULASI
        SpinLog::create([
            'user_id' => $user->id,
            'bet_amount' => $betAmount,
            'result_amount' => $winAmount,
            'status_manipulasi' => $statusLabel,
            'multiplier' => $multiplier,
            'grid_pattern' => $grid,
        ]);

        // 6. RETURN RESPONS API UNTUK KONTROL VISUAL SLOT FRONTEND
        return response()->json([
            'success' => true,
            'bet_amount' => $betAmount,
            'win_amount' => $winAmount,
            'multiplier' => $multiplier,
            'new_balance' => (float) $user->balance,
            'status_manipulasi' => $manipulationSetting,
            'status_label' => $statusLabel,
            'message' => $message,
            'grid' => $grid, // 15 simbol visual untuk slot grid 5x3
        ]);
    }

    /**
     * Membuat kombinasi 15 simbol slot Zonk (Kalah)
     */
    private function generateLosingGrid(): array
    {
        $symbols = ['💎', '💚', '💙', '⏳', '💍'];
        $grid = [];
        for ($i = 0; $i < 15; $i++) {
            $grid[] = $symbols[$i % count($symbols)];
        }
        shuffle($grid);
        return $grid;
    }

    /**
     * Membuat kombinasi 15 simbol slot Menang Jackpot (Zeus & Mahkota)
     */
    private function generateWinningGrid(float $multiplier): array
    {
        $grid = [];
        $grid[] = '⚡ ZEUS x' . (int)$multiplier;
        for ($i = 0; $i < 7; $i++) {
            $grid[] = '👑 MAHKOTA';
        }
        for ($i = 0; $i < 7; $i++) {
            $grid[] = '💍 CINCIN';
        }
        shuffle($grid);
        return $grid;
    }

    /**
     * Membuat kombinasi 15 simbol slot Menang Kecil (Pancingan)
     */
    private function generateLowWinGrid(): array
    {
        $grid = [];
        for ($i = 0; $i < 4; $i++) {
            $grid[] = '⏳ JAM PASIR';
        }
        $otherSymbols = ['💎', '💚', '💙', '💍'];
        for ($i = 0; $i < 11; $i++) {
            $grid[] = $otherSymbols[rand(0, count($otherSymbols) - 1)];
        }
        shuffle($grid);
        return $grid;
    }
}
