<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GameSetting;
use App\Models\SpinLog;
use App\Models\DepositRequest;

class AdminController extends Controller
{
    public function index()
    {
        $players = User::where('role', 'player')
            ->with(['gameSetting', 'spinLogs' => function ($q) {
                $q->latest()->limit(5);
            }])
            ->get();

        $logs = SpinLog::with('user')
            ->latest()
            ->limit(50)
            ->get();

        $pendingDeposits = DepositRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.dashboard', compact('players', 'logs', 'pendingDeposits'));
    }

    public function updateSetting(Request $request, $userId)
    {
        $request->validate([
            'next_spin_result' => ['required', 'in:win,lose,random_low_win'],
        ]);

        $user = User::where('role', 'player')->findOrFail($userId);

        $setting = GameSetting::updateOrCreate(
            ['user_id' => $user->id],
            ['next_spin_result' => $request->next_spin_result]
        );

        $statusLabels = [
            'lose' => 'Pasti Rungkad / Kalah (Zonk)',
            'win' => 'Pasti Menang (Jackpot)',
            'random_low_win' => 'Menang Kecil (Pancingan)',
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Setting untuk {$user->name} berhasil diubah menjadi: " . $statusLabels[$setting->next_spin_result],
                'setting' => $setting,
            ]);
        }

        return back()->with('success', "Setting hasil putaran untuk {$user->name} berhasil diubah menjadi: " . $statusLabels[$setting->next_spin_result]);
    }

    public function resetBalance(Request $request, $userId)
    {
        $user = User::where('role', 'player')->findOrFail($userId);
        $user->balance = 100000.00;
        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Saldo {$user->name} berhasil di-reset menjadi Rp 100.000",
                'new_balance' => $user->balance,
            ]);
        }

        return back()->with('success', "Saldo {$user->name} berhasil di-reset menjadi Rp 100.000");
    }

    public function deletePlayer(Request $request, $userId)
    {
        $user = User::where('role', 'player')->findOrFail($userId);
        $userName = $user->name;

        // Hapus relasi game setting dan spin logs
        $user->gameSetting()->delete();
        $user->spinLogs()->delete();
        $user->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Pemain {$userName} berhasil dihapus dari sistem.",
            ]);
        }

        return back()->with('success', "Pemain {$userName} berhasil dihapus dari sistem.");
    }

    public function bulkDeletePlayers(Request $request)
    {
        $request->validate([
            'player_ids' => ['required', 'array'],
            'player_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $playerIds = $request->player_ids;
        $players = User::where('role', 'player')->whereIn('id', $playerIds)->get();
        $count = $players->count();

        foreach ($players as $player) {
            $player->gameSetting()->delete();
            $player->spinLogs()->delete();
            $player->delete();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$count} pemain terpilih dari sistem.",
            ]);
        }

        return back()->with('success', "Berhasil menghapus {$count} pemain terpilih dari sistem.");
    }

    public function liveLogs()
    {
        $logs = SpinLog::with('user')
            ->latest()
            ->limit(30)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_name' => $log->user ? $log->user->name : 'Siswa',
                    'bet_amount' => number_format($log->bet_amount, 0, ',', '.'),
                    'result_amount' => number_format($log->result_amount, 0, ',', '.'),
                    'multiplier' => $log->multiplier,
                    'status_manipulasi' => $log->status_manipulasi,
                    'created_at' => $log->created_at->format('H:i:s - d M Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'logs' => $logs,
        ]);
    }

    public function approveDeposit(Request $request, $id)
    {
        $deposit = DepositRequest::with('user')->findOrFail($id);

        if ($deposit->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan deposit ini sudah diproses sebelumnya.',
            ], 400);
        }

        $deposit->status = 'approved';
        $deposit->save();

        $user = $deposit->user;
        $user->balance += $deposit->amount;
        $user->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Deposit Rp " . number_format($deposit->amount, 0, ',', '.') . " untuk {$user->name} berhasil di-ACC (Disetujui)! Saldo telah ditambahkan.",
            ]);
        }

        return back()->with('success', "Deposit Rp " . number_format($deposit->amount, 0, ',', '.') . " untuk {$user->name} berhasil di-ACC (Disetujui)!");
    }

    public function rejectDeposit(Request $request, $id)
    {
        $deposit = DepositRequest::with('user')->findOrFail($id);

        if ($deposit->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan deposit ini sudah diproses sebelumnya.',
            ], 400);
        }

        $deposit->status = 'rejected';
        $deposit->save();

        $userName = $deposit->user ? $deposit->user->name : 'Siswa';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Deposit Rp " . number_format($deposit->amount, 0, ',', '.') . " untuk {$userName} telah Ditolak.",
            ]);
        }

        return back()->with('success', "Deposit Rp " . number_format($deposit->amount, 0, ',', '.') . " untuk {$userName} telah Ditolak.");
    }

    public function realtimeData()
    {
        $players = User::where('role', 'player')
            ->with('gameSetting')
            ->get()
            ->map(function ($player) {
                return [
                    'id' => $player->id,
                    'name' => $player->name,
                    'email' => $player->email,
                    'balance' => (float) $player->balance,
                    'balance_formatted' => number_format($player->balance, 0, ',', '.'),
                    'setting' => $player->gameSetting ? $player->gameSetting->next_spin_result : 'lose',
                ];
            });

        $totalRevenue = (float) SpinLog::sum('bet_amount');
        $totalPayout = (float) SpinLog::sum('result_amount');
        $netProfit = $totalRevenue - $totalPayout;
        $payoutRatio = $totalRevenue > 0 ? round(($totalPayout / $totalRevenue) * 100, 1) : 0;

        $logs = SpinLog::with('user')
            ->latest()
            ->limit(30)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'user_name' => $log->user ? $log->user->name : 'Siswa',
                    'bet_amount' => number_format($log->bet_amount, 0, ',', '.'),
                    'result_amount' => number_format($log->result_amount, 0, ',', '.'),
                    'multiplier' => $log->multiplier,
                    'status_manipulasi' => $log->status_manipulasi,
                    'created_at' => $log->created_at->format('H:i:s - d M Y'),
                ];
            });

        $pendingDeposits = DepositRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get()
            ->map(function ($d) {
                return [
                    'id' => $d->id,
                    'user_name' => $d->user ? $d->user->name : 'Siswa',
                    'user_email' => $d->user ? $d->user->email : '',
                    'amount' => (float) $d->amount,
                    'amount_formatted' => number_format($d->amount, 0, ',', '.'),
                    'status' => $d->status,
                    'created_at' => $d->created_at->format('H:i:s - d M Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'stats' => [
                'total_players' => $players->count(),
                'total_spins' => SpinLog::count(),
                'total_revenue' => number_format($totalRevenue, 0, ',', '.'),
                'total_payout' => number_format($totalPayout, 0, ',', '.'),
                'net_profit' => number_format($netProfit, 0, ',', '.'),
                'payout_ratio' => $payoutRatio,
                'pending_deposits_count' => $pendingDeposits->count(),
            ],
            'players' => $players,
            'logs' => $logs,
            'pending_deposits' => $pendingDeposits,
        ]);
    }
}
