@extends('layouts.app')

@section('title', 'Panel Kontrol Admin (Guru) - EduSlot Manipulasi')

@section('content')
<div class="space-y-6">

    <!-- Header Panel Admin -->
    <div class="glass-card p-6 rounded-2xl gold-border flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-900/60 border border-purple-500/40 text-purple-200 text-xs font-semibold">
                    <i class="fa-solid fa-user-gear text-purple-400"></i> Halaman Pengendali Bandar (Guru)
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-950/80 border border-emerald-500/50 text-emerald-300 text-xs font-bold shadow-lg shadow-emerald-950/50">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    REAL-TIME LIVE SYNC
                </span>
            </div>
            <h2 class="font-display font-black text-2xl md:text-3xl text-white">PANEL KONTROL PROBABILITAS SISWA</h2>
            <p class="text-indigo-300 text-sm mt-1">Ubah hasil putaran slot siswa secara instan untuk mendemonstrasikan sistem judi yang di-rigged.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button onclick="fetchRealtimeData()" class="px-4 py-2 bg-indigo-900/60 hover:bg-indigo-800 border border-indigo-700 text-indigo-200 text-xs font-bold rounded-xl flex items-center gap-2 transition shadow-lg">
                <i class="fa-solid fa-rotate text-amber-400" id="refresh-icon"></i> Refresh Data Live
            </button>
        </div>
    </div>

    <!-- Summary Statistics Grid -->
    @php
        $totalRevenue = (float) \App\Models\SpinLog::sum('bet_amount');
        $totalPayout = (float) \App\Models\SpinLog::sum('result_amount');
        $netProfit = $totalRevenue - $totalPayout;
        $payoutRatio = $totalRevenue > 0 ? round(($totalPayout / $totalRevenue) * 100, 1) : 0;
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="glass-card p-4 rounded-xl border border-indigo-800/60 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-indigo-300 font-semibold uppercase tracking-wider">Siswa Terdaftar</p>
                <p id="stat-total-players" class="font-display font-black text-2xl text-white mt-0.5">{{ $players->count() }} Orang</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-900/60 border border-indigo-700/60 flex items-center justify-center text-indigo-300 text-lg">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="glass-card p-4 rounded-xl border border-purple-800/60 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-purple-300 font-semibold uppercase tracking-wider">Total Spin Logs</p>
                <p id="stat-total-spins" class="font-display font-black text-2xl text-purple-300 mt-0.5">{{ $logs->count() }} Spin</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-purple-900/60 border border-purple-700/60 flex items-center justify-center text-purple-300 text-lg">
                <i class="fa-solid fa-list-check"></i>
            </div>
        </div>

        <div class="glass-card p-4 rounded-xl border border-amber-500/30 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-amber-300/80 font-semibold uppercase tracking-wider">Pendapatan Bet Admin</p>
                <p id="stat-total-revenue" class="font-display font-black text-xl text-amber-400 mt-0.5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 text-lg">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
        </div>

        <div class="glass-card p-4 rounded-xl border border-red-500/30 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-red-300/80 font-semibold uppercase tracking-wider">Total Payout Pemain</p>
                <p id="stat-total-payout" class="font-display font-black text-xl text-red-400 mt-0.5">Rp {{ number_format($totalPayout, 0, ',', '.') }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/30 flex items-center justify-center text-red-400 text-lg">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
        </div>

        <div class="glass-card p-4 rounded-xl border border-emerald-500/30 flex items-center justify-between">
            <div>
                <p class="text-[11px] text-emerald-300/80 font-semibold uppercase tracking-wider">Net Profit Admin</p>
                <p id="stat-net-profit" class="font-display font-black text-xl text-emerald-400 mt-0.5">Rp {{ number_format($netProfit, 0, ',', '.') }}</p>
                <p class="text-[10px] text-indigo-300 mt-0.5">Payout Ratio: <span id="stat-payout-ratio" class="font-bold text-amber-300">{{ $payoutRatio }}%</span> (Max 60%)</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-lg">
                <i class="fa-solid fa-vault"></i>
            </div>
        </div>
    </div>

    <!-- Pending Deposit Requests (ACC Deposit Admin) Card -->
    <div class="glass-card rounded-2xl border border-emerald-500/40 overflow-hidden shadow-xl">
        <div class="p-5 border-b border-emerald-500/30 flex items-center justify-between bg-emerald-950/30">
            <div>
                <h3 class="font-display font-bold text-lg text-white flex items-center gap-2">
                    <i class="fa-solid fa-money-bill-transfer text-emerald-400"></i>
                    Persetujuan Deposit Siswa (ACC Deposit)
                </h3>
                <p class="text-xs text-indigo-300 mt-0.5">Konfirmasi pengajuan deposit virtual siswa untuk menambahkan saldo mereka.</p>
            </div>
            <span class="text-xs bg-emerald-900/80 border border-emerald-600/50 text-emerald-200 px-3 py-1 rounded-full font-bold shadow flex items-center gap-1.5">
                <i class="fa-solid fa-clock"></i>
                <span id="stat-pending-deposits-badge">{{ $pendingDeposits->count() }} Menunggu ACC</span>
            </span>
        </div>

        <div class="max-h-64 overflow-y-auto">
            <table class="w-full text-left text-sm text-indigo-200">
                <thead class="bg-indigo-950/90 sticky top-0 uppercase text-[11px] font-bold text-indigo-300 border-b border-indigo-900/80">
                    <tr>
                        <th class="py-3 px-4">Waktu Pengajuan</th>
                        <th class="py-3 px-4">Siswa (Player)</th>
                        <th class="py-3 px-4">Nominal Deposit</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi Persetujuan (ACC / Tolak)</th>
                    </tr>
                </thead>
                <tbody id="pending-deposits-tbody" class="divide-y divide-indigo-900/40">
                    @forelse($pendingDeposits as $dep)
                    <tr class="hover:bg-indigo-900/20 transition text-xs">
                        <td class="py-3.5 px-4 font-mono text-indigo-400">{{ $dep->created_at->format('H:i:s - d M Y') }}</td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-white text-sm">{{ $dep->user ? $dep->user->name : 'Siswa' }}</div>
                            <div class="text-[10px] text-indigo-400">{{ $dep->user ? $dep->user->email : '' }}</div>
                        </td>
                        <td class="py-3.5 px-4 font-display font-black text-amber-400 text-base">
                            Rp {{ number_format($dep->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-950 border border-amber-500/50 text-amber-300 flex items-center gap-1 w-max">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                                MENUNGGU ACC
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" onclick="approveDeposit({{ $dep->id }})"
                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> ACC / Setujui
                                </button>
                                <button type="button" onclick="rejectDeposit({{ $dep->id }})"
                                    class="px-3 py-1.5 rounded-lg bg-red-950/60 border border-red-800 hover:bg-red-900 text-red-300 font-semibold text-xs transition flex items-center gap-1">
                                    <i class="fa-solid fa-ban"></i> Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-indigo-400 text-xs font-semibold">Tidak ada permintaan deposit yang menunggu ACC.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
                <p class="text-xs text-indigo-300 mt-0.5">Pilih tombol aksi di bawah untuk menentukan takdir putaran siswa secara instan tanpa reload.</p>
            </div>

            <div class="flex items-center gap-2">
                <button id="bulk-delete-btn" onclick="executeBulkDelete()" disabled
                    class="px-3.5 py-2 bg-red-600/40 border border-red-500/40 text-red-200 text-xs font-bold rounded-xl flex items-center gap-2 transition cursor-not-allowed opacity-50 shadow">
                    <i class="fa-solid fa-trash-can"></i>
                    <span>Hapus Masal (<span id="selected-count">0</span>)</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-indigo-200">
                <thead class="bg-indigo-950/80 uppercase text-[11px] font-bold text-indigo-300 border-b border-indigo-900/80">
                    <tr>
                        <th class="py-3.5 px-4 w-10 text-center">
                            <input type="checkbox" id="select-all-checkbox" onclick="toggleSelectAllPlayers(this)"
                                class="rounded border-indigo-800 bg-indigo-950 text-purple-600 focus:ring-purple-500 w-4 h-4 cursor-pointer">
                        </th>
                        <th class="py-3.5 px-4">Siswa (Player)</th>
                        <th class="py-3.5 px-4">Saldo Virtual</th>
                        <th class="py-3.5 px-4">Status Manipulasi Aktif</th>
                        <th class="py-3.5 px-4 text-center">Aksi Manipulasi Instan (Bandar)</th>
                        <th class="py-3.5 px-4 text-right">Kelola Pemain</th>
                    </tr>
                </thead>
                <tbody id="players-table-body" class="divide-y divide-indigo-900/40">
                    @forelse($players as $player)
                    @php
                        $setting = $player->gameSetting ? $player->gameSetting->next_spin_result : 'lose';
                    @endphp
                    <tr id="player-row-{{ $player->id }}" class="hover:bg-indigo-900/20 transition">
                        <td class="py-4 px-4 text-center">
                            <input type="checkbox" class="player-select-checkbox rounded border-indigo-800 bg-indigo-950 text-purple-600 focus:ring-purple-500 w-4 h-4 cursor-pointer"
                                value="{{ $player->id }}" onchange="updateSelectedPlayersCount()">
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-bold text-white text-base">{{ $player->name }}</div>
                            <div class="text-xs text-indigo-400">{{ $player->email }}</div>
                        </td>
                        <td class="py-4 px-4 font-display font-bold text-amber-400" id="player-balance-{{ $player->id }}">
                            Rp {{ number_format($player->balance, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-4" id="player-badge-{{ $player->id }}">
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
                                <button type="button" onclick="updatePlayerSetting({{ $player->id }}, 'lose')"
                                    class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1 {{ $setting === 'lose' ? 'bg-red-600 text-white shadow-lg shadow-red-600/30 ring-2 ring-red-400' : 'bg-red-950/60 border border-red-800 text-red-300 hover:bg-red-900/80' }}"
                                    id="btn-setting-lose-{{ $player->id }}">
                                    <i class="fa-solid fa-skull"></i> Set Rungkad
                                </button>

                                <button type="button" onclick="updatePlayerSetting({{ $player->id }}, 'win')"
                                    class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1 {{ $setting === 'win' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 ring-2 ring-emerald-400' : 'bg-emerald-950/60 border border-emerald-800 text-emerald-300 hover:bg-emerald-900/80' }}"
                                    id="btn-setting-win-{{ $player->id }}">
                                    <i class="fa-solid fa-trophy"></i> Set Menang
                                </button>

                                <button type="button" onclick="updatePlayerSetting({{ $player->id }}, 'random_low_win')"
                                    class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1 {{ $setting === 'random_low_win' ? 'bg-amber-600 text-white shadow-lg shadow-amber-600/30 ring-2 ring-amber-400' : 'bg-amber-950/60 border border-amber-800 text-amber-300 hover:bg-amber-900/80' }}"
                                    id="btn-setting-pancingan-{{ $player->id }}">
                                    <i class="fa-solid fa-fish"></i> Set Pancingan
                                </button>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button type="button" onclick="resetPlayerBalance({{ $player->id }})" class="px-2.5 py-1.5 bg-indigo-900/60 hover:bg-indigo-800 border border-indigo-700 text-indigo-300 rounded-lg text-xs font-semibold transition" title="Isi ulang saldo ke Rp 100.000">
                                    <i class="fa-solid fa-rotate-left"></i> Reset Rp 100k
                                </button>
                                <button type="button" onclick="deletePlayer({{ $player->id }}, '{{ addslashes($player->name) }}')" class="px-2.5 py-1.5 bg-red-950/60 hover:bg-red-900 border border-red-800 text-red-300 rounded-lg text-xs font-semibold transition" title="Hapus Pemain">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-indigo-400 text-sm">Belum ada siswa terdaftar.</td>
                    </tr>
                    @endforelse
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
                    Audit Trail Live Spin Logs (Pemantauan Real-Time 2 Detik)
                </h3>
                <p class="text-xs text-indigo-300 mt-0.5">Catatan transparan saat siswa melakukan spin di antarmuka mereka.</p>
            </div>
            <span class="text-xs bg-indigo-900/80 border border-indigo-700 text-indigo-300 px-3 py-1 rounded-full font-semibold">
                Auto-Sync System (2s)
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

// Batas 60% Revenue Payout System Guard
$maxPayoutAllowedTotal = 0.60 * ($totalAdminIncome + $betAmount);
if ($proposedWinAmount > $maxWinAllowedThisSpin) {
    // Kemenangan dibatasi / dipaksa Rungkad jika melebihi 60% total pendapatan admin!
}</code></pre>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function getSelectedPlayerIds() {
        const checkboxes = document.querySelectorAll('.player-select-checkbox:checked');
        return Array.from(checkboxes).map(cb => parseInt(cb.value));
    }

    function updateSelectedPlayersCount() {
        const selectedIds = getSelectedPlayerIds();
        const countSpan = document.getElementById('selected-count');
        const bulkBtn = document.getElementById('bulk-delete-btn');
        const allCheckbox = document.getElementById('select-all-checkbox');
        const allCheckboxes = document.querySelectorAll('.player-select-checkbox');

        if (countSpan) countSpan.innerText = selectedIds.length;

        if (bulkBtn) {
            if (selectedIds.length > 0) {
                bulkBtn.disabled = false;
                bulkBtn.className = 'px-3.5 py-2 bg-red-600 hover:bg-red-500 border border-red-400 text-white text-xs font-bold rounded-xl flex items-center gap-2 transition cursor-pointer shadow-lg shadow-red-600/30';
            } else {
                bulkBtn.disabled = true;
                bulkBtn.className = 'px-3.5 py-2 bg-red-600/40 border border-red-500/40 text-red-200 text-xs font-bold rounded-xl flex items-center gap-2 transition cursor-not-allowed opacity-50 shadow';
            }
        }

        if (allCheckbox && allCheckboxes.length > 0) {
            allCheckbox.checked = selectedIds.length === allCheckboxes.length;
        }
    }

    function toggleSelectAllPlayers(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.player-select-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateSelectedPlayersCount();
    }

    function fetchRealtimeData() {
        const icon = document.getElementById('refresh-icon');
        if (icon) icon.classList.add('fa-spin');

        const currentlySelectedIds = getSelectedPlayerIds();

        fetch("{{ route('admin.realtime-data') }}", {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // 1. Update Stats
                document.getElementById('stat-total-players').innerText = data.stats.total_players + ' Orang';
                document.getElementById('stat-total-spins').innerText   = data.stats.total_spins + ' Spin';
                document.getElementById('stat-total-revenue').innerText = 'Rp ' + data.stats.total_revenue;
                document.getElementById('stat-total-payout').innerText  = 'Rp ' + data.stats.total_payout;
                document.getElementById('stat-net-profit').innerText    = 'Rp ' + data.stats.net_profit;
                document.getElementById('stat-payout-ratio').innerText  = data.stats.payout_ratio + '%';

                // Update Pending Deposits Badge
                const pendingBadge = document.getElementById('stat-pending-deposits-badge');
                if (pendingBadge) pendingBadge.innerText = data.stats.pending_deposits_count + ' Menunggu ACC';

                // 2. Re-render Pending Deposits Table
                const depTbody = document.getElementById('pending-deposits-tbody');
                if (depTbody) {
                    if (!data.pending_deposits || data.pending_deposits.length === 0) {
                        depTbody.innerHTML = '<tr><td colspan="5" class="py-6 text-center text-indigo-400 text-xs font-semibold">Tidak ada permintaan deposit yang menunggu ACC.</td></tr>';
                    } else {
                        let depHtml = '';
                        data.pending_deposits.forEach(d => {
                            depHtml += `
                                <tr class="hover:bg-indigo-900/20 transition text-xs">
                                    <td class="py-3.5 px-4 font-mono text-indigo-400">${d.created_at}</td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-white text-sm">${d.user_name}</div>
                                        <div class="text-[10px] text-indigo-400">${d.user_email}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-display font-black text-amber-400 text-base">
                                        Rp ${d.amount_formatted}
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-950 border border-amber-500/50 text-amber-300 flex items-center gap-1 w-max">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                                            MENUNGGU ACC
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" onclick="approveDeposit(${d.id})"
                                                class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-600/30 transition flex items-center gap-1">
                                                <i class="fa-solid fa-circle-check"></i> ACC / Setujui
                                            </button>
                                            <button type="button" onclick="rejectDeposit(${d.id})"
                                                class="px-3 py-1.5 rounded-lg bg-red-950/60 border border-red-800 hover:bg-red-900 text-red-300 font-semibold text-xs transition flex items-center gap-1">
                                                <i class="fa-solid fa-ban"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `;
                        });
                        depTbody.innerHTML = depHtml;
                    }
                }

                // 3. Re-render Player Table Dynamically
                const playersTbody = document.getElementById('players-table-body');
                if (data.players.length === 0) {
                    playersTbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-indigo-400 text-sm">Belum ada siswa terdaftar.</td></tr>';
                } else {
                    let playerHtml = '';
                    data.players.forEach(p => {
                        const isChecked = currentlySelectedIds.includes(p.id) ? 'checked' : '';
                        const setting = p.setting;
                        
                        let badgeHtml = '';
                        if (setting === 'lose') {
                            badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-950 border border-red-500/50 text-red-300">
                                <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                                PASTI RUNGKAD (KALAH)
                            </span>`;
                        } else if (setting === 'win') {
                            badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-950 border border-emerald-500/50 text-emerald-300">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                PASTI MENANG (JACKPOT)
                            </span>`;
                        } else {
                            badgeHtml = `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-950 border border-amber-500/50 text-amber-300">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                MENANG KECIL (PANCINGAN)
                            </span>`;
                        }

                        const btnLoseClass = setting === 'lose' ? 'bg-red-600 text-white shadow-lg shadow-red-600/30 ring-2 ring-red-400' : 'bg-red-950/60 border border-red-800 text-red-300 hover:bg-red-900/80';
                        const btnWinClass = setting === 'win' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 ring-2 ring-emerald-400' : 'bg-emerald-950/60 border border-emerald-800 text-emerald-300 hover:bg-emerald-900/80';
                        const btnPancinganClass = setting === 'random_low_win' ? 'bg-amber-600 text-white shadow-lg shadow-amber-600/30 ring-2 ring-amber-400' : 'bg-amber-950/60 border border-amber-800 text-amber-300 hover:bg-amber-900/80';

                        const safeName = p.name.replace(/'/g, "\\'");

                        playerHtml += `
                            <tr id="player-row-${p.id}" class="hover:bg-indigo-900/20 transition">
                                <td class="py-4 px-4 text-center">
                                    <input type="checkbox" class="player-select-checkbox rounded border-indigo-800 bg-indigo-950 text-purple-600 focus:ring-purple-500 w-4 h-4 cursor-pointer"
                                        value="${p.id}" ${isChecked} onchange="updateSelectedPlayersCount()">
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-white text-base">${p.name}</div>
                                    <div class="text-xs text-indigo-400">${p.email}</div>
                                </td>
                                <td class="py-4 px-4 font-display font-bold text-amber-400" id="player-balance-${p.id}">
                                    Rp ${p.balance_formatted}
                                </td>
                                <td class="py-4 px-4" id="player-badge-${p.id}">
                                    ${badgeHtml}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" onclick="updatePlayerSetting(${p.id}, 'lose')"
                                            class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1 ${btnLoseClass}"
                                            id="btn-setting-lose-${p.id}">
                                            <i class="fa-solid fa-skull"></i> Set Rungkad
                                        </button>

                                        <button type="button" onclick="updatePlayerSetting(${p.id}, 'win')"
                                            class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1 ${btnWinClass}"
                                            id="btn-setting-win-${p.id}">
                                            <i class="fa-solid fa-trophy"></i> Set Menang
                                        </button>

                                        <button type="button" onclick="updatePlayerSetting(${p.id}, 'random_low_win')"
                                            class="px-3 py-1.5 rounded-lg font-semibold text-xs transition flex items-center gap-1 ${btnPancinganClass}"
                                            id="btn-setting-pancingan-${p.id}">
                                            <i class="fa-solid fa-fish"></i> Set Pancingan
                                        </button>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" onclick="resetPlayerBalance(${p.id})" class="px-2.5 py-1.5 bg-indigo-900/60 hover:bg-indigo-800 border border-indigo-700 text-indigo-300 rounded-lg text-xs font-semibold transition" title="Isi ulang saldo ke Rp 100.000">
                                            <i class="fa-solid fa-rotate-left"></i> Reset Rp 100k
                                        </button>
                                        <button type="button" onclick="deletePlayer(${p.id}, '${safeName}')" class="px-2.5 py-1.5 bg-red-950/60 hover:bg-red-900 border border-red-800 text-red-300 rounded-lg text-xs font-semibold transition" title="Hapus Pemain">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                    playersTbody.innerHTML = playerHtml;
                }
                updateSelectedPlayersCount();

                // 4. Update Audit Trail Table
                const tbody = document.getElementById('logs-table-body');
                if (data.logs.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-indigo-400 text-sm">Belum ada aktivitas spin dari siswa.</td></tr>';
                } else {
                    let html = '';
                    data.logs.forEach(log => {
                        const isRungkad = log.status_manipulasi.includes('Rungkad') || log.status_manipulasi.includes('Dibatasi');
                        const isJackpot = log.status_manipulasi.includes('Jackpot');
                        const badgeClass = isRungkad ? 'bg-red-950 border border-red-500/50 text-red-300' : (isJackpot ? 'bg-emerald-950 border border-emerald-500/50 text-emerald-300' : 'bg-amber-950 border border-amber-500/50 text-amber-300');
                        const resultClass = parseFloat(log.result_amount.replace(/\./g, '').replace(',', '.')) > 0 ? 'text-emerald-400' : 'text-red-400';

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
        .catch(err => console.error("Error fetching realtime data:", err))
        .finally(() => {
            if (icon) setTimeout(() => icon.classList.remove('fa-spin'), 300);
        });
    }

    function approveDeposit(depositId) {
        fetch(`/admin/approve-deposit/${depositId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchRealtimeData();
            } else {
                alert(data.message || 'Gagal menyetujui deposit.');
            }
        })
        .catch(err => console.error("Error approving deposit:", err));
    }

    function rejectDeposit(depositId) {
        fetch(`/admin/reject-deposit/${depositId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchRealtimeData();
            } else {
                alert(data.message || 'Gagal menolak deposit.');
            }
        })
        .catch(err => console.error("Error rejecting deposit:", err));
    }

    function updatePlayerSetting(playerId, settingValue) {
        fetch(`/admin/update-setting/${playerId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ next_spin_result: settingValue })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchRealtimeData();
            }
        })
        .catch(err => console.error("Error updating player setting:", err));
    }

    function resetPlayerBalance(playerId) {
        fetch(`/admin/reset-balance/${playerId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchRealtimeData();
            }
        })
        .catch(err => console.error("Error resetting balance:", err));
    }

    function deletePlayer(playerId, playerName) {
        if (!confirm(`Apakah Anda yakin ingin menghapus akun siswa "${playerName}" beserta seluruh riwayat spin miliknya?`)) {
            return;
        }

        fetch(`/admin/delete-player/${playerId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchRealtimeData();
            }
        })
        .catch(err => console.error("Error deleting player:", err));
    }

    function executeBulkDelete() {
        const selectedIds = getSelectedPlayerIds();
        if (selectedIds.length === 0) return;

        if (!confirm(`Apakah Anda yakin ingin menghapus ${selectedIds.length} pemain terpilih beserta seluruh data riwayatnya?`)) {
            return;
        }

        fetch(`/admin/delete-players-bulk`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ player_ids: selectedIds })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchRealtimeData();
            }
        })
        .catch(err => console.error("Error bulk deleting players:", err));
    }

    // Auto refresh data every 2 seconds for true real-time dashboard
    setInterval(fetchRealtimeData, 2000);
</script>
@endpush
