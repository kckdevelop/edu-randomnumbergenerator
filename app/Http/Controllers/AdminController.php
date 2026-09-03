<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GameSetting;
use App\Models\SpinLog;

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

        return view('admin.dashboard', compact('players', 'logs'));
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
}
