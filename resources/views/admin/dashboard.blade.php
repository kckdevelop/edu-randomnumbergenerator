@extends('layouts.app')

@section('title', 'Panel Kontrol Admin (Guru) - EduSlot Manipulasi')

@section('content')
<div class="space-y-6">

    <!-- Header Panel Admin -->
    <div class="glass-card p-6 rounded-2xl gold-border flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-900/60 border border-purple-500/40 text-purple-200 text-xs font-semibold mb-2">
                <i class="fa-solid fa-user-gear text-purple-400"></i> Halaman Pengendali Bandar (Guru)
            </div>
            <h2 class="font-display font-black text-2xl md:text-3xl text-white">PANEL KONTROL PROBABILITAS SISWA</h2>
            <p class="text-indigo-300 text-sm mt-1">Ubah hasil putaran slot siswa secara instan untuk mendemonstrasikan sistem judi yang di-rigged.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="fetchLiveLogs()" class="px-4 py-2 bg-indigo-900/60 hover:bg-indigo-800 border border-indigo-700 text-indigo-200 text-xs font-bold rounded-xl flex items-center gap-2 transition">
                <i class="fa-solid fa-rotate text-amber-400" id="refresh-icon"></i> Refresh Audit Log
            </button>
        </div>
    </div>

    <!-- Summary Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="glass-card p-5 rounded-xl border border-indigo-800/60 flex items-center justify-between">
            <div>
                <p class="text-xs text-indigo-300 font-semibold uppercase">Total Siswa Terdaftar</p>
                <p class="font-display font-black text-2xl text-white mt-1">{{ $players->count() }} Orang</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-indigo-900/60 border border-indigo-700/60 flex items-center justify-center text-indigo-300 text-xl">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="glass-card p-5 rounded-xl border border-amber-500/30 flex items-center justify-between">
            <div>
                <p class="text-xs text-amber-300/80 font-semibold uppercase">Status Sistem Manipulasi</p>
                <p class="font-display font-black text-2xl text-amber-400 mt-1">100% DIKONTROL</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl">
                <i class="fa-solid fa-sliders"></i>
            </div>
        </div>

        <div class="glass-card p-5 rounded-xl border border-purple-800/60 flex items-center justify-between">
            <div>
                <p class="text-xs text-purple-300 font-semibold uppercase">Total Riwayat Spin</p>
                <p class="font-display font-black text-2xl text-purple-300 mt-1">{{ $logs->count() }} Spin</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-900/60 border border-purple-700/60 flex items-center justify-center text-purple-300 text-xl">
                <i class="fa-solid fa-list-check"></i>
            </div>
        </div>
    </div>

    <!-- Student Control Table Card -->
    <div class="glass-card rounded-2xl border border-indigo-800/60 overflow-hidden shadow-xl">
        <div class="p-5 border-b border-indigo-900/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 bg-indigo-950/40">
            <div>
                <h3 class="font-display font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-amber-400"></i>
                    Daftar Siswa & Pengaturan Kemenangan (Backend Manipulator)
                </h3>
                <p class="text-xs text-indigo-300 mt-0.5">Pilih tombol aksi di bawah untuk menentukan takdir putaran siswa selanjutnya.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-indigo-200">
                <thead class="bg-indigo-950/80 uppercase text-[11px] font-bold text-indigo-300 border-b border-indigo-900/80">
                    <tr>
                        <th class="py-3.5 px-4">Siswa (Player)</th>
                        <th class="py-3.5 px-4">Saldo Virtual</th>
                        <th class="py-3.5 px-4">Status Manipulasi Aktif</th>
                        <th class="py-3.5 px-4 text-center">Aksi Manipulasi Instan (Bandar)</th>
                        <th class="py-3.5 px-4 text-right">Reset Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-indigo-900/40">
                    @foreach($players as $player)
                    @php
                        $setting = $player->gameSetting ? $player->gameSetting->next_spin_result : 'lose';
                    @endphp
                    <tr class="hover:bg-indigo-900/20 transition">
                        <td class="py-4 px-4">
                            <div class="font-bold text-white text-base">{{ $player->name }}</div>
                            <div class="text-xs text-indigo-400">{{ $player->email }}</div>
                        </td>
                        <td class="py-4 px-4 font-display font-bold text-amber-400">
                            Rp {{ number_format($player->balance, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-4">
                            @if($setting === 'lose')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-950 border border-red-500/50 text-red-300">
                                    <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                                    PASTI RUNGKAD (KALAH)
                                </span>
                            @elseif($setting === 'win')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-950 border border-emerald-500/50 text-emerald-300">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    PASTI MENANG (JACKPOT)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-950 border border-amber-500/50 text-amber-300">
                                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                    MENANG KECIL (PANCINGAN)
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center justify-center gap-2">
                                <!-- Form Pasti Rungkad -->
                                <form action="{{ route('admin.update-setting', $player->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="next_spin_result" value="lose">
                                    <button type="submit" 
                                        class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1 {{ $setting === 'lose' ? 'bg-red-600 text-white shadow-lg shadow-red-600/30 ring-2 ring-red-400' : 'bg-red-950/60 border border-red-800 text-red-300 hover:bg-red-900/80' }}">
                                        <i class="fa-solid fa-skull"></i> Set Rungkad
                                    </button>
                                </form>

                                <!-- Form Pasti Menang -->
                                <form action="{{ route('admin.update-setting', $player->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="next_spin_result" value="win">
                                    <button type="submit" 
                                        class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1 {{ $setting === 'win' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 ring-2 ring-emerald-400' : 'bg-emerald-950/60 border border-emerald-800 text-emerald-300 hover:bg-emerald-900/80' }}">
                                        <i class="fa-solid fa-trophy"></i> Set Menang
                                    </button>
                                </form>

                                <!-- Form Pancingan -->
                                <form action="{{ route('admin.update-setting', $player->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="next_spin_result" value="random_low_win">
                                    <button type="submit" 
                                        class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1 {{ $setting === 'random_low_win' ? 'bg-amber-600 text-white shadow-lg shadow-amber-600/30 ring-2 ring-amber-400' : 'bg-amber-950/60 border border-amber-800 text-amber-300 hover:bg-amber-900/80' }}">
                                        <i class="fa-solid fa-fish"></i> Set Pancingan
                                    </button>
                                </form>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <form action="{{ route('admin.reset-balance', $player->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1.5 bg-indigo-900/60 hover:bg-indigo-800 border border-indigo-700 text-indigo-300 rounded-lg text-xs font-semibold transition" title="Isi ulang saldo ke Rp 100.000">
                                    <i class="fa-solid fa-rotate-left"></i> Reset Rp 100k
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Live Audit Trail Log Section -->
    <div class="glass-card rounded-2xl border border-indigo-800/60 overflow-hidden shadow-xl">
        <div class="p-5 border-b border-indigo-900/60 flex items-center justify-between bg-indigo-950/40">
            <div>
                <h3 class="font-display font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-amber-400"></i>
                    Audit Trail Live Spin Logs (Pemantauan Real-Time)
                </h3>
                <p class="text-xs text-indigo-300 mt-0.5">Catatan transparan saat siswa melakukan spin di antarmuka mereka.</p>
            </div>
            <span class="text-xs bg-indigo-900/80 border border-indigo-700 text-indigo-300 px-3 py-1 rounded-full font-semibold">
                Auto-Sync System
            </span>
        </div>

        <div class="max-h-96 overflow-y-auto">
            <table class="w-full text-left text-sm text-indigo-200">
                <thead class="bg-indigo-950/90 sticky top-0 uppercase text-[11px] font-bold text-indigo-300 border-b border-indigo-900/80">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Nama Siswa</th>
                        <th class="py-3 px-4">Nilai Bet</th>
                        <th class="py-3 px-4">Hasil Payout</th>
                        <th class="py-3 px-4">Perkalian</th>
                        <th class="py-3 px-4">Status Manipulasi Bandar</th>
                    </tr>
                </thead>
                <tbody id="logs-table-body" class="divide-y divide-indigo-900/40">
                    @forelse($logs as $log)
                    <tr class="hover:bg-indigo-900/20 transition text-xs">
                        <td class="py-3 px-4 font-mono text-indigo-400">{{ $log->created_at->format('H:i:s - d M Y') }}</td>
                        <td class="py-3 px-4 font-bold text-white">{{ $log->user ? $log->user->name : 'Siswa' }}</td>
                        <td class="py-3 px-4 text-amber-300">Rp {{ number_format($log->bet_amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-4 font-bold {{ $log->result_amount > 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            Rp {{ number_format($log->result_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 font-bold text-purple-300">{{ $log->multiplier }}x</td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ str_contains($log->status_manipulasi, 'Rungkad') ? 'bg-red-950 border border-red-500/50 text-red-300' : (str_contains($log->status_manipulasi, 'Jackpot') ? 'bg-emerald-950 border border-emerald-500/50 text-emerald-300' : 'bg-amber-950 border border-amber-500/50 text-amber-300') }}">
                                {{ $log->status_manipulasi }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-indigo-400 text-sm">Belum ada aktivitas spin dari siswa.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Educational Code Explanation Card -->
    <div class="glass-card rounded-2xl border border-purple-800/60 p-6 gold-border">
        <h3 class="font-display font-bold text-lg text-amber-400 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-code text-purple-400"></i>
            Bukti Manipulasi Backend (Kode Controller yang dapat ditunjukkan ke Siswa)
        </h3>
        <p class="text-xs text-indigo-200 mb-4 leading-relaxed">
            Dalam sistem judi online nyata, tombol spin pada frontend hanyalah visualizer sederhana. Logika kemenangan ditentukan 100% di server backend sebelum animasi spin berhenti:
        </p>

        <div class="bg-gray-950 border border-purple-900/80 p-4 rounded-xl font-mono text-xs text-purple-200 overflow-x-auto leading-relaxed">
            <pre><code>// Potongan dari app/Http/Controllers/GameController.php

$setting = GameSetting::where('user_id', $user->id)->first();

if ($setting->next_spin_result === 'lose') {
    // SYSTEM DENGAN SENGJA MENGEMBALIKAN SIMBOL KALAH (ZONK)
    $winAmount = 0;
    $grid = $this->generateLosingGrid();
} elseif ($setting->next_spin_result === 'win') {
    // SYSTEM DI-SETTING UNTUK MEMBERIKAN JACKPOT PERKALIAN TINGGI
    $winAmount = $betAmount * $multiplier;
    $grid = $this->generateWinningGrid($multiplier);
}</code></pre>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fetchLiveLogs() {
        const icon = document.getElementById('refresh-icon');
        icon.classList.add('fa-spin');

        fetch("{{ route('admin.live-logs') }}")
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('logs-table-body');
                    if (data.logs.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-indigo-400 text-sm">Belum ada aktivitas spin dari siswa.</td></tr>';
                    } else {
                        let html = '';
                        data.logs.forEach(log => {
                            const isRungkad = log.status_manipulasi.includes('Rungkad');
                            const isJackpot = log.status_manipulasi.includes('Jackpot');
                            const badgeClass = isRungkad ? 'bg-red-950 border border-red-500/50 text-red-300' : (isJackpot ? 'bg-emerald-950 border border-emerald-500/50 text-emerald-300' : 'bg-amber-950 border border-amber-500/50 text-amber-300');
                            const resultClass = parseFloat(log.result_amount) > 0 ? 'text-emerald-400' : 'text-red-400';

                            html += `
                                <tr class="hover:bg-indigo-900/20 transition text-xs">
                                    <td class="py-3 px-4 font-mono text-indigo-400">${log.created_at}</td>
                                    <td class="py-3 px-4 font-bold text-white">${log.user_name}</td>
                                    <td class="py-3 px-4 text-amber-300">Rp ${log.bet_amount}</td>
                                    <td class="py-3 px-4 font-bold ${resultClass}">Rp ${log.result_amount}</td>
                                    <td class="py-3 px-4 font-bold text-purple-300">${log.multiplier}x</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold ${badgeClass}">
                                            ${log.status_manipulasi}
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });
                        tbody.innerHTML = html;
                    }
                }
            })
            .catch(err => console.error("Error fetching live logs:", err))
            .finally(() => {
                setTimeout(() => icon.classList.remove('fa-spin'), 500);
            });
    }

    // Auto refresh logs every 5 seconds
    setInterval(fetchLiveLogs, 5000);
</script>
@endpush
