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

        // Hitung Pendapatan Admin & Batas Maksimal Kemenangan Pemain (Max 60% dari Total Pendapatan Admin)
        $totalAdminIncome = (float) SpinLog::sum('bet_amount') + $betAmount;
        $totalPlayerPayouts = (float) SpinLog::sum('result_amount');
        $maxPayoutAllowedTotal = 0.60 * $totalAdminIncome;
        $maxWinAllowedThisSpin = max(0.0, $maxPayoutAllowedTotal - $totalPlayerPayouts);

        // 3. DIKTE HASIL PUTARAN BERDASARKAN KEPUTUSAN BANDAR & BATAS 60% REVENUE
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
            $proposedMultiplier = (float) rand(50, 500); // Perkalian jackpot 50x - 500x
            $proposedWinAmount = $betAmount * $proposedMultiplier;

            if ($proposedWinAmount > $maxWinAllowedThisSpin) {
                $maxPossibleMultiplier = (float) floor($maxWinAllowedThisSpin / $betAmount);
                if ($maxPossibleMultiplier < 1.0) {
                    // Jika memberikan kemenangan 1x saja sudah melebihi 60% total pendapatan admin, paksa Rungkad
                    $grid = $this->generateLosingGrid();
                    $multiplier = 0;
                    $winAmount = 0;
                    $statusLabel = 'Dibatasi Admin Profit Guard (Max 60% Revenue)';
                    $message = 'Sistem membatasi kemenangan! Total payout pemain telah mencapai batas 60% dari total pendapatan admin.';
                } else {
                    // Capped ke batas multiplier tertinggi yang diperbolehkan
                    $multiplier = min($proposedMultiplier, $maxPossibleMultiplier);
                    $winAmount = $betAmount * $multiplier;
                    $grid = $this->generateWinningGrid($multiplier);
                    $statusLabel = 'Pasti Menang Jackpot (Dibatasi 60% Revenue Admin)';
                    $message = "SELAMAT! Anda mendapatkan Jackpot x{$multiplier}! (Dibatasi max 60% total pendapatan admin)";
                }
            } else {
                $multiplier = $proposedMultiplier;
                $winAmount = $proposedWinAmount;
                $grid = $this->generateWinningGrid($multiplier);
                $statusLabel = 'Pasti Menang Jackpot (Dikontrol Backend Bandar)';
                $message = "SELAMAT! Anda mendapatkan Jackpot x{$multiplier}! (Hasil manipulasi Admin)";
            }

        } elseif ($manipulationSetting === 'random_low_win') {
            // === MODUS 3: PANCINGAN / MENANG KECIL ===
            $proposedMultiplier = (float) (rand(120, 250) / 100); // 1.2x - 2.5x
            $proposedWinAmount = $betAmount * $proposedMultiplier;

            if ($proposedWinAmount > $maxWinAllowedThisSpin) {
                $maxPossibleMultiplier = (float) ($maxWinAllowedThisSpin / $betAmount);
                if ($maxPossibleMultiplier < 1.0) {
                    $grid = $this->generateLosingGrid();
                    $multiplier = 0;
                    $winAmount = 0;
                    $statusLabel = 'Dibatasi Admin Profit Guard (Max 60% Revenue)';
                    $message = 'Kemenangan pancingan dibatasi karena total payout pemain telah mencapai 60% pendapatan admin!';
                } else {
                    $multiplier = round($maxPossibleMultiplier, 2);
                    $winAmount = $betAmount * $multiplier;
                    $grid = $this->generateLowWinGrid();
                    $statusLabel = 'Menang Kecil / Pancingan (Dibatasi 60% Revenue)';
                    $message = "Anda Menang Kecil x{$multiplier} (Dibatasi max 60% total pendapatan admin).";
                }
            } else {
                $multiplier = $proposedMultiplier;
                $winAmount = $proposedWinAmount;
                $grid = $this->generateLowWinGrid();
                $statusLabel = 'Menang Kecil / Pancingan (Dikontrol Backend Bandar)';
                $message = "Anda Menang Kecil x{$multiplier}. Ini teknik pancingan psikologis judi online agar Anda terus mencoba!";
            }
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
