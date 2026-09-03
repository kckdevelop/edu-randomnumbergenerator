@extends('layouts.app')

@section('title', 'Gates of Olympus - Simulasi Slot Edukasi')

@push('styles')
<style>
/* =============================================
   GATES OF OLYMPUS - ADVANCED ANIMATION SYSTEM
   ============================================= */

/* Slot Cell Base */
.slot-cell-wrapper {
    position: relative;
    aspect-ratio: 1;
}

.slot-box {
    background: linear-gradient(180deg, #131224 0%, #0d0c18 100%);
    border: 2px solid #312E81;
    box-shadow: inset 0 0 20px rgba(0,0,0,0.8), 0 0 10px rgba(109, 40, 217, 0.2);
    transition: border-color 0.3s, box-shadow 0.3s;
    position: relative;
    overflow: visible;
    z-index: 1;
}

/* ---- SPINNING STATE ---- */
.slot-box.spinning .symbol-icon {
    animation: symbolBlur 0.12s infinite alternate;
    display: inline-block;
}

@keyframes symbolBlur {
    0%   { transform: translateY(-6px) scale(0.9); filter: blur(2px); opacity: 0.7; }
    100% { transform: translateY(6px) scale(1.1); filter: blur(3px); opacity: 1; }
}

.slot-box.spinning {
    border-color: #7C3AED;
    box-shadow: inset 0 0 25px rgba(0,0,0,0.9), 0 0 20px rgba(124, 62, 237, 0.5), 0 0 40px rgba(124, 62, 237, 0.2);
    animation: reelShake 0.1s infinite;
}

@keyframes reelShake {
    0%   { transform: translateY(0px); }
    25%  { transform: translateY(-1px); }
    75%  { transform: translateY(1px); }
    100% { transform: translateY(0px); }
}

/* ---- LAND ANIMATION (Symbol drops into place) ---- */
.slot-box.landing .symbol-icon {
    animation: symbolLand 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes symbolLand {
    0%   { transform: translateY(-30px) scale(1.4); opacity: 0; }
    60%  { transform: translateY(5px) scale(0.95); opacity: 1; }
    80%  { transform: translateY(-3px) scale(1.05); }
    100% { transform: translateY(0) scale(1); }
}

/* ---- MATCHED / WINNING SYMBOL ---- */
.slot-box.matched {
    border-color: #F59E0B !important;
    background: linear-gradient(180deg, #1a1500 0%, #0f0d00 100%) !important;
    box-shadow: 0 0 20px #F59E0B, 0 0 50px #F59E0B80, inset 0 0 20px rgba(245,158,11,0.3) !important;
    animation: matchedPulse 0.4s ease-in-out infinite alternate;
    z-index: 10;
}

@keyframes matchedPulse {
    0%   { box-shadow: 0 0 15px #F59E0B, 0 0 35px #F59E0B50, inset 0 0 15px rgba(245,158,11,0.2); }
    100% { box-shadow: 0 0 30px #F59E0B, 0 0 70px #F59E0B80, inset 0 0 30px rgba(245,158,11,0.4); }
}

.slot-box.matched .symbol-icon {
    animation: matchedSymbolBounce 0.5s ease-in-out infinite alternate;
    display: inline-block;
}

@keyframes matchedSymbolBounce {
    0%   { transform: scale(1); }
    100% { transform: scale(1.25); }
}

/* ---- SHATTER / BREAK ANIMATION ---- */
.slot-box.shattering {
    animation: shatterCell 0.5s ease-in forwards !important;
    border-color: #EF4444 !important;
}

@keyframes shatterCell {
    0%   { transform: scale(1); opacity: 1; }
    20%  { transform: scale(1.15) rotate(2deg); }
    40%  { transform: scale(0.9) rotate(-3deg); }
    60%  { transform: scale(1.05) rotate(1deg); }
    80%  { transform: scale(0.8) rotate(-2deg); opacity: 0.4; }
    100% { transform: scale(0) rotate(15deg); opacity: 0; }
}

/* ---- EXPLOSION PARTICLE SYSTEM ---- */
.particle {
    position: absolute;
    pointer-events: none;
    font-size: 1rem;
    z-index: 100;
    animation: particleFly 0.7s ease-out forwards;
}

@keyframes particleFly {
    0%   { transform: translate(0,0) scale(1); opacity: 1; }
    100% { transform: translate(var(--tx), var(--ty)) scale(0); opacity: 0; }
}

/* ---- LIGHTNING BOLT OVERLAY ---- */
.lightning-bolt {
    position: absolute;
    pointer-events: none;
    z-index: 200;
    font-size: 2rem;
    animation: lightningStrike 0.5s ease-out forwards;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

@keyframes lightningStrike {
    0%   { opacity: 0; transform: translate(-50%, -50%) scale(0.3) rotate(-20deg); filter: brightness(3); }
    20%  { opacity: 1; transform: translate(-50%, -50%) scale(2) rotate(5deg); filter: brightness(5); }
    50%  { opacity: 1; transform: translate(-50%, -50%) scale(1.5) rotate(-5deg); filter: brightness(3); }
    100% { opacity: 0; transform: translate(-50%, -50%) scale(0.5) rotate(10deg); }
}

/* ---- SCREEN-WIDE ZEUS LIGHTNING FLASH ---- */
#zeus-flash-overlay {
    position: fixed;
    inset: 0;
    pointer-events: none;
    z-index: 9999;
    background: linear-gradient(135deg, #fbbf2430 0%, transparent 50%, #a78bfa30 100%);
    opacity: 0;
}

#zeus-flash-overlay.active {
    animation: zeusScreenFlash 0.8s ease-out forwards;
}

@keyframes zeusScreenFlash {
    0%   { opacity: 0; }
    10%  { opacity: 1; }
    30%  { opacity: 0.4; }
    50%  { opacity: 0.9; }
    70%  { opacity: 0.2; }
    100% { opacity: 0; }
}

/* ---- SCREEN SHAKE ON WIN ---- */
#slot-grid-container.screen-shake {
    animation: screenShake 0.5s cubic-bezier(.36,.07,.19,.97) both;
}

@keyframes screenShake {
    10%, 90%  { transform: translateX(-2px) rotate(-0.5deg); }
    20%, 80%  { transform: translateX(4px) rotate(0.5deg); }
    30%, 50%, 70% { transform: translateX(-6px) rotate(-0.5deg); }
    40%, 60%  { transform: translateX(6px) rotate(0.5deg); }
}

/* ---- LOSE DARK SCREEN ---- */
#zeus-flash-overlay.lose-flash {
    animation: loseFlash 0.6s ease-out forwards;
    background: radial-gradient(circle, #ff000020 0%, transparent 70%);
}

@keyframes loseFlash {
    0%   { opacity: 0; }
    20%  { opacity: 1; }
    100% { opacity: 0; }
}

/* ---- MULTIPLIER POP-UP ---- */
.multiplier-popup {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0);
    background: linear-gradient(135deg, #F59E0B, #92400E);
    color: #fff;
    font-family: 'Outfit', sans-serif;
    font-weight: 900;
    font-size: 1rem;
    padding: 4px 10px;
    border-radius: 8px;
    z-index: 300;
    pointer-events: none;
    white-space: nowrap;
    border: 2px solid #FDE68A;
    animation: multiplierPopup 1.2s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

@keyframes multiplierPopup {
    0%   { transform: translate(-50%, -50%) scale(0); opacity: 0; }
    30%  { transform: translate(-50%, -120%) scale(1.2); opacity: 1; }
    60%  { transform: translate(-50%, -130%) scale(1); opacity: 1; }
    90%  { transform: translate(-50%, -150%) scale(0.9); opacity: 0.7; }
    100% { transform: translate(-50%, -160%) scale(0); opacity: 0; }
}

/* ---- CASCADING FALL-IN ANIMATION ---- */
.slot-box.cascade-in .symbol-icon {
    animation: cascadeFall 0.4s cubic-bezier(0.23, 1, 0.32, 1) forwards;
    display: inline-block;
}

@keyframes cascadeFall {
    0%   { transform: translateY(-40px); opacity: 0; filter: blur(4px); }
    60%  { transform: translateY(5px); opacity: 1; filter: blur(0); }
    80%  { transform: translateY(-3px); }
    100% { transform: translateY(0); opacity: 1; }
}

/* ---- ZEUS JACKPOT HEADER ANIMATION ---- */
#jackpot-banner {
    display: none;
    position: absolute;
    top: 0; left: 0; right: 0;
    background: linear-gradient(90deg, #78350f, #F59E0B, #78350f);
    text-align: center;
    padding: 8px;
    font-family: 'Outfit', sans-serif;
    font-weight: 900;
    font-size: 1.1rem;
    color: #fff;
    letter-spacing: 4px;
    z-index: 50;
    border-radius: 24px 24px 0 0;
    animation: jackpotBanner 0.5s ease forwards;
    text-shadow: 0 0 10px #F59E0B, 0 0 20px #F59E0B;
    box-shadow: 0 4px 30px rgba(245,158,11,0.5);
}

@keyframes jackpotBanner {
    0%   { transform: translateY(-100%); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
}

/* ---- FLOATING COIN RAIN ---- */
.coin-rain {
    position: fixed;
    top: -50px;
    pointer-events: none;
    z-index: 9000;
    font-size: 1.5rem;
    animation: coinFall linear forwards;
}

@keyframes coinFall {
    0%   { transform: translateY(0) rotate(0deg); opacity: 1; }
    100% { transform: translateY(110vh) rotate(720deg); opacity: 0; }
}

/* =============================================
   WIN POPUP MODAL SYSTEM
   ============================================= */

/* --- Backdrop --- */
#win-popup-backdrop {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: rgba(0, 0, 0, 0.88);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    z-index: 99999 !important;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.35s ease;
    /* Reset any inherited transforms */
    transform: none !important;
    padding: 20px;
    box-sizing: border-box;
}

#win-popup-backdrop.open {
    opacity: 1;
    pointer-events: all;
}

/* --- Modal Box --- */
#win-popup-modal {
    position: relative;
    max-width: 490px;
    width: 100%;
    max-height: 92vh;
    overflow-y: auto;
    border-radius: 28px;
    transform: scale(0.55) translateY(80px);
    opacity: 0;
    transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275),
                opacity 0.4s ease;
    /* Ensure modal is truly centered */
    margin: auto;
    flex-shrink: 0;
}

#win-popup-backdrop.open #win-popup-modal {
    transform: scale(1) translateY(0);
    opacity: 1;
}

/* Jackpot variant */
#win-popup-modal.jackpot {
    border: 2px solid #F59E0B;
    box-shadow: 0 0 60px #F59E0B80, 0 0 120px #F59E0B30, 0 30px 80px rgba(0,0,0,0.8);
    background: linear-gradient(160deg, #1a1200 0%, #0f0d00 40%, #120820 100%);
}

/* Low-win variant */
#win-popup-modal.lowwin {
    border: 2px solid #F59E0B80;
    box-shadow: 0 0 30px #F59E0B40, 0 20px 60px rgba(0,0,0,0.7);
    background: linear-gradient(160deg, #140e00 0%, #0e0d1a 100%);
}

/* Header glow bar */
.popup-header-bar {
    padding: 28px 24px 20px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.popup-header-bar::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(245,158,11,0.15) 0%, transparent 100%);
    pointer-events: none;
}

/* Zeus bolt icon pulsing */
.popup-zeus-icon {
    font-size: 4rem;
    display: inline-block;
    animation: popupZeusFloat 1.5s ease-in-out infinite alternate;
    filter: drop-shadow(0 0 20px #F59E0B) drop-shadow(0 0 50px #F59E0B80);
    line-height: 1;
}

.popup-zeus-icon.lowwin {
    font-size: 3rem;
    filter: drop-shadow(0 0 12px #F59E0B80);
}

@keyframes popupZeusFloat {
    0%   { transform: translateY(0) scale(1); }
    100% { transform: translateY(-8px) scale(1.1); }
}

/* Win amount counter */
#popup-win-amount {
    font-family: 'Outfit', sans-serif;
    font-weight: 900;
    font-size: 2.8rem;
    line-height: 1.1;
    background: linear-gradient(135deg, #FDE68A 0%, #F59E0B 50%, #D97706 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: none;
    display: block;
    animation: popupAmountIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.3s both;
}

#popup-win-amount.lowwin {
    font-size: 2rem;
}

@keyframes popupAmountIn {
    0%   { transform: scale(0.5); opacity: 0; }
    100% { transform: scale(1);   opacity: 1; }
}

/* Multiplier badge */
.popup-multiplier-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: linear-gradient(135deg, #7C3AED, #4C1D95);
    border: 1px solid #A78BFA60;
    color: #E9D5FF;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 0.9rem;
    padding: 4px 14px;
    border-radius: 999px;
    margin-top: 8px;
    box-shadow: 0 0 20px #7C3AED60;
    animation: popupBadgeIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) 0.5s both;
}

@keyframes popupBadgeIn {
    0%   { transform: scale(0); opacity: 0; }
    100% { transform: scale(1); opacity: 1; }
}

/* Stats row */
.popup-stats-row {
    display: flex;
    gap: 1px;
    background: rgba(255,255,255,0.05);
    border-top: 1px solid rgba(245,158,11,0.15);
    border-bottom: 1px solid rgba(245,158,11,0.15);
}

.popup-stat {
    flex: 1;
    padding: 14px 12px;
    text-align: center;
    background: rgba(10,9,24,0.6);
}

.popup-stat:not(:last-child) {
    border-right: 1px solid rgba(245,158,11,0.1);
}

.popup-stat-label {
    font-size: 0.62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6B7280;
    margin-bottom: 4px;
}

.popup-stat-value {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 1rem;
    color: #F3F4F6;
}

/* Close button */
.popup-close-btn {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
    color: #000;
    font-family: 'Outfit', sans-serif;
    font-weight: 900;
    font-size: 1.1rem;
    letter-spacing: 2px;
    border: none;
    cursor: pointer;
    transition: filter 0.2s, transform 0.1s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.popup-close-btn:hover  { filter: brightness(1.15); }
.popup-close-btn:active { transform: scale(0.98); }

/* Sparkle stars around modal */
.popup-sparkle {
    position: absolute;
    pointer-events: none;
    font-size: 1.4rem;
    z-index: 10001;
    animation: sparkleTwinkle 1.2s ease-in-out infinite alternate;
}

@keyframes sparkleTwinkle {
    0%   { transform: scale(0.8) rotate(0deg);   opacity: 0.6; }
    100% { transform: scale(1.3) rotate(30deg);  opacity: 1; }
}

/* ---- SMALL WIN TOAST ---- */
#small-win-toast {
    position: fixed;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%) translateY(120px);
    z-index: 9500;
    background: linear-gradient(135deg, #1a1400 0%, #0f0d00 100%);
    border: 1px solid #F59E0B80;
    box-shadow: 0 0 30px #F59E0B40, 0 10px 40px rgba(0,0,0,0.6);
    border-radius: 20px;
    padding: 16px 28px;
    text-align: center;
    min-width: 280px;
    pointer-events: none;
    transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s;
    opacity: 0;
}

#small-win-toast.show {
    transform: translateX(-50%) translateY(0);
    opacity: 1;
    pointer-events: all;
}

/* Popup confetti streamer */
.popup-confetti {
    position: fixed;
    pointer-events: none;
    z-index: 10002;
    font-size: 1.2rem;
    animation: confettiFall linear forwards;
    top: -20px;
}

@keyframes confettiFall {
    0%   { transform: translateY(0)   rotate(0deg)   scale(1);   opacity: 1; }
    80%  { opacity: 1; }
    100% { transform: translateY(105vh) rotate(540deg) scale(0.5); opacity: 0; }
}
</style>
@endpush

@section('content')

<!-- Zeus Flash Screen Overlay -->
<div id="zeus-flash-overlay"></div>

<!-- ============================================================
     DEPOSIT MODAL (Max Rp 500.000)
     ============================================================ -->
<div id="deposit-modal-backdrop" class="fixed inset-0 bg-black/85 backdrop-blur-md z-[99999] hidden items-center justify-center p-4">
    <div class="max-w-md w-full glass-card rounded-3xl gold-border overflow-hidden shadow-2xl relative">
        <!-- Header -->
        <div class="p-5 bg-gradient-to-r from-emerald-950 to-indigo-950 border-b border-emerald-500/30 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center text-emerald-400 text-xl">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <h3 class="font-display font-black text-lg text-white">DEPOSIT SALDO VIRTUAL</h3>
                    <p class="text-xs text-emerald-300 font-semibold">Maksimal Rp 500.000 per pengajuan</p>
                </div>
            </div>
            <button onclick="closeDepositModal()" class="text-indigo-400 hover:text-white text-xl">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="p-6 space-y-5">
            <!-- Preset Buttons -->
            <div>
                <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-2">Pilih Nominal Deposit Preset:</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach([50000, 100000, 200000, 500000] as $preset)
                    <button type="button" onclick="setDepositPreset({{ $preset }})"
                        class="py-2.5 px-3 rounded-xl border border-emerald-800/80 bg-emerald-950/40 hover:bg-emerald-900/60 text-emerald-200 text-xs font-bold transition flex items-center justify-between">
                        <span>Rp {{ number_format($preset, 0, ',', '.') }}</span>
                        @if($preset === 500000)
                            <span class="text-[10px] bg-amber-500/20 text-amber-300 border border-amber-500/40 px-1.5 py-0.5 rounded font-black">MAX</span>
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Nominal Input -->
            <div>
                <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-1.5">Nominal Pengajuan Deposit (Rp):</label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-bold text-amber-400 text-sm">Rp</span>
                    <input type="number" id="deposit-amount-input" value="100000" min="10000" max="500000" step="50000"
                        class="w-full bg-indigo-950 border border-indigo-800 text-amber-400 font-bold text-lg rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-amber-400">
                </div>
                <p class="text-[11px] text-indigo-400 mt-1">Minimal: Rp 10.000 | Maksimal: Rp 500.000</p>
            </div>

            <!-- Notice message -->
            <div class="bg-indigo-950/60 border border-indigo-800/60 p-3 rounded-xl flex items-start gap-2.5 text-xs text-indigo-200">
                <i class="fa-solid fa-circle-info text-amber-400 text-base shrink-0 mt-0.5"></i>
                <span>Pengajuan deposit memerlukan <b>ACC / Persetujuan Admin Bandar</b> sebelum saldo ditambahkan ke akun Anda.</span>
            </div>

            <!-- Submit Button -->
            <button onclick="submitDepositRequest()" id="btn-submit-deposit"
                class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-gray-950 font-display font-black text-base tracking-wider shadow-lg shadow-emerald-500/30 transition transform active:scale-95 flex items-center justify-center gap-2 border border-emerald-300">
                <i class="fa-solid fa-paper-plane" id="deposit-submit-icon"></i>
                <span id="deposit-submit-text">KIRIM PERMINTAAN DEPOSIT</span>
            </button>

            <!-- Recent Deposit History List -->
            <div class="pt-4 border-t border-indigo-900/60">
                <h4 class="text-xs font-bold text-indigo-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left text-amber-400"></i> Riwayat Deposit Anda
                </h4>
                <div class="max-h-36 overflow-y-auto space-y-1.5 text-xs" id="deposit-history-list">
                    @forelse($recentDeposits as $dep)
                    <div class="p-2 rounded-lg bg-indigo-950/80 border border-indigo-900 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-amber-400">Rp {{ number_format($dep->amount, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-indigo-400 ml-2 font-mono">{{ $dep->created_at->format('H:i:s - d M') }}</span>
                        </div>
                        @if($dep->status === 'pending')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-950 border border-amber-500/50 text-amber-300">MENUNGGU ACC</span>
                        @elseif($dep->status === 'approved')
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950 border border-emerald-500/50 text-emerald-300">DI-ACC (BERHASIL)</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-950 border border-red-500/50 text-red-300">DITOLAK</span>
                        @endif
                    </div>
                    @empty
                    <p class="text-[11px] text-indigo-400 text-center py-2">Belum ada riwayat deposit.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     WIN POPUP MODAL (Jackpot & Low-Win)
     ============================================================ -->
<div id="win-popup-backdrop">
    <div id="win-popup-modal" class="jackpot">

        <!-- Sparkle stars (positioned absolutely around modal) -->
        <span class="popup-sparkle" style="top:-18px; left:10%;  animation-delay:0s">✨</span>
        <span class="popup-sparkle" style="top:-18px; right:12%; animation-delay:0.3s">⭐</span>
        <span class="popup-sparkle" style="top:30%;  left:-22px; animation-delay:0.6s">💫</span>
        <span class="popup-sparkle" style="top:40%;  right:-22px;animation-delay:0.2s">✨</span>
        <span class="popup-sparkle" style="bottom:80px; left:5%; animation-delay:0.8s">⭐</span>
        <span class="popup-sparkle" style="bottom:80px; right:5%; animation-delay:0.4s">💫</span>

        <!-- Header section -->
        <div class="popup-header-bar">
            <div id="popup-zeus-icon" class="popup-zeus-icon">⚡</div>

            <p id="popup-subtitle" class="text-xs font-bold text-amber-400/70 uppercase tracking-widest mt-3 mb-1">ZEUS JACKPOT — DIKONTROL BANDAR</p>

            <span id="popup-win-amount" class="mt-2">Rp 0</span>

            <div id="popup-multiplier-badge" class="popup-multiplier-badge">
                <i class="fa-solid fa-bolt-lightning text-amber-300"></i>
                <span id="popup-multiplier-text">x500 Perkalian Zeus</span>
            </div>
        </div>

        <!-- Stats row -->
        <div class="popup-stats-row">
            <div class="popup-stat">
                <div class="popup-stat-label">Taruhan (Bet)</div>
                <div class="popup-stat-value" id="popup-stat-bet">—</div>
            </div>
            <div class="popup-stat">
                <div class="popup-stat-label">Kemenangan</div>
                <div class="popup-stat-value text-emerald-400" id="popup-stat-payout">—</div>
            </div>
            <div class="popup-stat">
                <div class="popup-stat-label">Saldo Baru</div>
                <div class="popup-stat-value text-amber-400" id="popup-stat-balance">—</div>
            </div>
        </div>

        <!-- Manipulation warning label -->
        <div class="px-6 py-3 bg-indigo-950/60 border-t border-indigo-900/60 text-center">
            <p class="text-[10px] text-indigo-300 font-semibold uppercase tracking-wider">
                <i class="fa-solid fa-code text-purple-400 mr-1"></i>
                <span id="popup-manipulation-label">Status manipulasi backend bandar</span>
            </p>
        </div>

        <!-- Close button -->
        <button class="popup-close-btn" onclick="closeWinPopup()" id="popup-close-btn">
            <i class="fa-solid fa-xmark"></i>
            <span>TUTUP &amp; PUTAR LAGI</span>
        </button>
    </div>
</div>

<!-- SMALL WIN TOAST (for random_low_win) -->
<div id="small-win-toast">
    <div class="text-2xl mb-1" id="toast-icon">⏳</div>
    <p class="text-xs font-bold text-amber-300 uppercase tracking-widest mb-1" id="toast-subtitle">MENANG KECIL — PANCINGAN BANDAR</p>
    <p class="font-display font-black text-xl text-white" id="toast-amount">Rp 0</p>
    <p class="text-[10px] text-amber-400/70 mt-1" id="toast-multiplier">x1.5 Perkalian</p>
    <p class="text-[10px] text-indigo-300 mt-2">Ini hanya umpan agar kamu terus bermain!</p>
</div>

<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Game Banner -->
    <div class="glass-card p-4 sm:p-6 rounded-2xl gold-border flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-amber-500 via-purple-600 to-indigo-700 flex items-center justify-center text-3xl shadow-xl zeus-lightning" id="zeus-icon">
                ⚡
            </div>
            <div>
                <h2 class="font-display font-black text-2xl sm:text-3xl gold-gradient-text">GATES OF OLYMPUS</h2>
                <p class="text-xs sm:text-sm text-indigo-300">Simulasi Mesin Slot Edukasi &amp; Demo Manipulasi Hasil</p>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-3 bg-indigo-950/80 border border-amber-500/30 px-5 py-2.5 rounded-xl shadow-lg">
                <div class="text-right">
                    <p class="text-[11px] font-semibold text-indigo-300 uppercase tracking-wider">Saldo Virtual Siswa</p>
                    <p id="player-balance" class="font-display font-black text-2xl text-amber-400">
                        Rp {{ number_format($user->balance, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-2xl text-amber-400">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>

            <button onclick="openDepositModal()" class="px-4 py-3 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-gray-950 font-display font-black text-xs tracking-wider shadow-lg shadow-emerald-500/20 transition transform active:scale-95 flex items-center gap-2 border border-emerald-300">
                <i class="fa-solid fa-plus-circle text-base"></i>
                <span>DEPOSIT</span>
            </button>
        </div>
    </div>

    <!-- Main Slot Machine Grid Container -->
    <div id="slot-grid-container" class="glass-card p-6 rounded-3xl gold-border shadow-2xl relative overflow-visible">

        <!-- Jackpot Banner (appears on win) -->
        <div id="jackpot-banner">⚡ ZEUS JACKPOT! ⚡</div>

        <!-- Slot Grid Title Header -->
        <div class="flex items-center justify-between mb-4 pb-3 border-t border-indigo-900/60 pt-3 mt-2">
            <span class="text-xs font-bold text-indigo-300 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-grip text-amber-400"></i> REEL GRID 5x3 (15 SYMBOLS)
            </span>
            <span id="game-status-badge" class="text-xs px-3 py-1 rounded-full font-bold bg-indigo-900/80 border border-indigo-700 text-indigo-300">
                READY TO SPIN
            </span>
        </div>

        <!-- 5x3 Reel Grid UI -->
        <div id="reel-grid" class="grid grid-cols-5 gap-2 sm:gap-3 my-4">
            @for($i = 0; $i < 15; $i++)
            <div class="slot-cell-wrapper">
                <div id="slot-cell-{{ $i }}" class="slot-box w-full h-full rounded-2xl flex flex-col items-center justify-center p-2 text-center select-none shadow-inner border border-indigo-800/80">
                    <span class="symbol-icon text-3xl sm:text-4xl">
                        @php
                            $defaultSymbols = ['⚡', '👑', '💍', '⏳', '💎', '💚', '💙'];
                            echo $defaultSymbols[$i % count($defaultSymbols)];
                        @endphp
                    </span>
                    <span class="symbol-label text-[10px] font-bold text-indigo-300 mt-1 uppercase tracking-tighter">
                        OLYMPUS
                    </span>
                </div>
            </div>
            @endfor
        </div>

        <!-- Bet & Spin Control Bar -->
        <div class="mt-8 pt-6 border-t border-indigo-900/60 flex flex-col md:flex-row items-center justify-between gap-6">

            <!-- Bet Amount Selection -->
            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-indigo-300 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-coins text-amber-400 mr-1"></i> Taruhan (Bet Amount)
                </label>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach([2000, 5000, 10000, 20000, 50000] as $presetBet)
                    <button type="button" onclick="setBet({{ $presetBet }})"
                        class="bet-preset-btn px-3 py-1.5 rounded-xl border border-indigo-800 bg-indigo-950/60 hover:bg-indigo-900 text-indigo-200 text-xs font-bold transition">
                        Rp {{ number_format($presetBet, 0, ',', '.') }}
                    </button>
                    @endforeach
                </div>
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-xs text-indigo-400 font-semibold">Nominal Bet:</span>
                    <input type="number" id="bet-input" value="10000" min="1000" step="1000"
                        class="bg-indigo-950 border border-indigo-800 text-amber-400 font-bold text-sm rounded-lg px-3 py-1.5 w-36 focus:outline-none focus:border-amber-400">
                </div>
            </div>

            <!-- Golden Spin & Auto Spin Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto justify-end">
                <button id="auto-spin-btn" onclick="toggleAutoSpin()"
                    class="w-full sm:w-48 py-4 px-4 rounded-2xl bg-gradient-to-r from-purple-700 via-purple-600 to-indigo-700 hover:from-purple-600 hover:to-indigo-600 text-white font-display font-bold text-base tracking-wider shadow-lg shadow-purple-900/40 transition transform active:scale-95 flex items-center justify-center gap-2 border border-purple-400/40">
                    <i class="fa-solid fa-repeat text-lg" id="auto-spin-icon"></i>
                    <span id="auto-spin-text">AUTO SPIN 10X</span>
                </button>
                <button id="spin-btn" onclick="executeSpin()"
                    class="w-full sm:w-56 py-4 px-6 rounded-2xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 hover:from-amber-300 hover:to-amber-500 text-gray-950 font-display font-black text-xl tracking-wider shadow-2xl shadow-amber-500/30 transition transform active:scale-95 flex items-center justify-center gap-3 border-2 border-amber-300">
                    <i class="fa-solid fa-rotate text-2xl" id="spin-icon"></i>
                    <span id="spin-text">PUTAR (SPIN)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Spin Result Card -->
    <div id="result-card" class="hidden glass-card rounded-2xl p-6 border transition-all duration-300 shadow-2xl">
        <div class="flex items-start gap-4">
            <div id="result-icon-bg" class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0">
                <i id="result-icon" class="fa-solid"></i>
            </div>
            <div class="flex-grow">
                <div class="flex items-center justify-between">
                    <h3 id="result-title" class="font-display font-black text-xl">STATUS HASIL SPIN</h3>
                    <span id="result-badge" class="px-3 py-1 rounded-full text-xs font-bold uppercase">MANIPULASI BACKEND</span>
                </div>
                <p id="result-message" class="text-sm mt-1 font-medium leading-relaxed"></p>
                <div class="mt-4 pt-3 border-t border-indigo-900/60 flex flex-wrap items-center justify-between text-xs">
                    <div>
                        <span class="text-indigo-300 font-semibold">Perkalian Multiplier:</span>
                        <span id="result-multiplier" class="font-bold text-purple-300 ml-1">0x</span>
                    </div>
                    <div>
                        <span class="text-indigo-300 font-semibold">Kemenangan (Payout):</span>
                        <span id="result-payout" class="font-bold text-emerald-400 ml-1">Rp 0</span>
                    </div>
                    <div>
                        <span class="text-indigo-300 font-semibold">Status Manipulasi Admin:</span>
                        <span id="result-status-label" class="font-bold text-amber-300 ml-1">Zonk</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Player Spin History Log -->
    <div class="glass-card rounded-2xl border border-indigo-800/60 p-6">
        <h3 class="font-display font-bold text-lg text-white mb-4 flex items-center gap-2">
            <i class="fa-solid fa-history text-amber-400"></i> Riwayat Putaran Anda
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-indigo-200">
                <thead class="bg-indigo-950 uppercase font-bold text-indigo-300 border-b border-indigo-900">
                    <tr>
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3">Taruhan (Bet)</th>
                        <th class="py-2.5 px-3">Hasil Payout</th>
                        <th class="py-2.5 px-3">Perkalian</th>
                        <th class="py-2.5 px-3">Status Kontrol Bandar</th>
                    </tr>
                </thead>
                <tbody id="player-history-body" class="divide-y divide-indigo-900/40">
                    @forelse($recentLogs as $log)
                    <tr>
                        <td class="py-2.5 px-3 font-mono text-indigo-400">{{ $log->created_at->format('H:i:s') }}</td>
                        <td class="py-2.5 px-3 text-amber-300">Rp {{ number_format($log->bet_amount, 0, ',', '.') }}</td>
                        <td class="py-2.5 px-3 font-bold {{ $log->result_amount > 0 ? 'text-emerald-400' : 'text-red-400' }}">
                            Rp {{ number_format($log->result_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-2.5 px-3 font-bold text-purple-300">{{ $log->multiplier }}x</td>
                        <td class="py-2.5 px-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ str_contains($log->status_manipulasi, 'Rungkad') ? 'bg-red-950 text-red-300' : 'bg-emerald-950 text-emerald-300' }}">
                                {{ $log->status_manipulasi }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr id="empty-history-row">
                        <td colspan="5" class="py-4 text-center text-indigo-400">Belum ada putaran slot. Tekan tombol Putar untuk mulai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let isSpinning = false;
    let isAutoSpinning = false;
    let stopAutoSpinRequested = false;
    let currentBalance = {{ (float) $user->balance }};
    const availableSymbols = ['⚡', '👑', '💍', '⏳', '💎', '💚', '💙'];
    const symbolLabels   = { '⚡': 'PETIR', '👑': 'MAHKOTA', '💍': 'CINCIN', '⏳': 'JAM PASIR', '💎': 'PERMATA', '💚': 'GIOK', '💙': 'SAFIR' };

    // ─── Teleport popup, toast & deposit modal to <body> so position:fixed is always relative to viewport ───
    document.addEventListener('DOMContentLoaded', () => {
        const backdrop = document.getElementById('win-popup-backdrop');
        const toast    = document.getElementById('small-win-toast');
        const depModal = document.getElementById('deposit-modal-backdrop');
        if (backdrop) document.body.appendChild(backdrop);
        if (toast)    document.body.appendChild(toast);
        if (depModal) document.body.appendChild(depModal);
    });

    // Close popup with ESC key
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeWinPopup();
            closeDepositModal();
        }
    });

    // ─── Deposit Modal Handlers ──────────────────────────────────────────────────

    function openDepositModal() {
        const modal = document.getElementById('deposit-modal-backdrop');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeDepositModal() {
        const modal = document.getElementById('deposit-modal-backdrop');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function setDepositPreset(amount) {
        document.getElementById('deposit-amount-input').value = amount;
    }

    function submitDepositRequest() {
        const amount = parseFloat(document.getElementById('deposit-amount-input').value);
        if (!amount || amount < 10000) {
            alert('Nominal deposit minimal adalah Rp 10.000!');
            return;
        }
        if (amount > 500000) {
            alert('Nominal deposit maksimal adalah Rp 500.000!');
            return;
        }

        const btn = document.getElementById('btn-submit-deposit');
        const icon = document.getElementById('deposit-submit-icon');
        const text = document.getElementById('deposit-submit-text');

        btn.disabled = true;
        icon.className = 'fa-solid fa-spinner fa-spin';
        text.innerText = 'MENGIRIM...';

        fetch("{{ route('player.deposit') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ amount: amount })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                fetchUserDeposits();
            } else {
                alert(data.message || 'Gagal mengirim deposit.');
            }
        })
        .catch(err => {
            alert(err.message || 'Terjadi kesalahan.');
        })
        .finally(() => {
            btn.disabled = false;
            icon.className = 'fa-solid fa-paper-plane';
            text.innerText = 'KIRIM PERMINTAAN DEPOSIT';
        });
    }

    function fetchUserDeposits() {
        fetch("{{ route('player.user-deposits') }}", {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const list = document.getElementById('deposit-history-list');
                if (data.deposits.length === 0) {
                    list.innerHTML = '<p class="text-[11px] text-indigo-400 text-center py-2">Belum ada riwayat deposit.</p>';
                } else {
                    let html = '';
                    data.deposits.forEach(d => {
                        let badge = '';
                        if (d.status === 'pending') {
                            badge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-950 border border-amber-500/50 text-amber-300">MENUNGGU ACC</span>';
                        } else if (d.status === 'approved') {
                            badge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-950 border border-emerald-500/50 text-emerald-300">DI-ACC (BERHASIL)</span>';
                        } else {
                            badge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-950 border border-red-500/50 text-red-300">DITOLAK</span>';
                        }
                        html += `
                            <div class="p-2 rounded-lg bg-indigo-950/80 border border-indigo-900 flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-amber-400">Rp ${d.amount_formatted}</span>
                                    <span class="text-[10px] text-indigo-400 ml-2 font-mono">${d.created_at}</span>
                                </div>
                                ${badge}
                            </div>
                        `;
                    });
                    list.innerHTML = html;
                }
            }
        });
    }

    // ─── Utility ────────────────────────────────────────────────────────────────

    function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

    function setBet(amount) {
        if (isSpinning || isAutoSpinning) return;
        document.getElementById('bet-input').value = amount;
    }

    // ─── Spin Entry Point ───────────────────────────────────────────────────────

    async function executeSpin(isAuto = false) {
        if (isSpinning) return false;

        const betAmount = parseFloat(document.getElementById('bet-input').value);
        if (!betAmount || betAmount < 1000) { alert('Nominal taruhan minimal adalah Rp 1.000!'); return false; }

        if (currentBalance < betAmount) {
            alert('Saldo virtual Anda tidak mencukupi untuk melakukan bet ini!');
            return false;
        }

        isSpinning = true;
        resetUI();

        // Phase 1: Start reel rolling
        const spinIntervals = startReelRolling();

        // Phase 2: Fetch backend result (already running in parallel)
        let data;
        try {
            data = await fetchSpin(betAmount);
        } catch(err) {
            stopReelRolling(spinIntervals);
            alert(err.message || err.error || 'Terjadi kesalahan pada server.');
            unlockUI();
            return false;
        }

        // Phase 3: Wait minimum 2s of rolling animation
        await sleep(2000);
        stopReelRolling(spinIntervals);

        // Phase 4: Land symbols column by column
        await landSymbols(data.grid);

        // Phase 5: Post-land effects based on result
        if (data.status_manipulasi === 'lose') {
            await playLoseSequence();
        } else if (data.status_manipulasi.includes('win') || data.status_manipulasi === 'win') {
            await playJackpotSequence(data);
        } else {
            await playLowWinSequence(data);
        }

        // Phase 6: Update balance & render result card
        updateBalance(data.new_balance);
        renderResultCard(data);
        addHistoryRow(data);

        // Jika auto spin dan menang jackpot, auto close popup setelah 1.5 detik agar loop berjalan terus
        if (isAuto && (data.status_manipulasi === 'win' || data.status_manipulasi.includes('win'))) {
            await sleep(1500);
            closeWinPopup();
        }

        unlockUI();
        return true;
    }

    // ─── Auto Spin 10x Handler ──────────────────────────────────────────────────

    async function toggleAutoSpin() {
        if (isAutoSpinning) {
            stopAutoSpinRequested = true;
            document.getElementById('auto-spin-text').innerText = 'MENGHENTIKAN...';
            return;
        }

        const betAmount = parseFloat(document.getElementById('bet-input').value);
        if (!betAmount || betAmount < 1000) {
            alert('Nominal taruhan minimal adalah Rp 1.000!');
            return;
        }

        if (currentBalance < betAmount) {
            alert('Saldo virtual Anda tidak mencukupi untuk melakukan Auto Spin!');
            return;
        }

        isAutoSpinning = true;
        stopAutoSpinRequested = false;

        const autoBtn = document.getElementById('auto-spin-btn');
        const autoIcon = document.getElementById('auto-spin-icon');
        const autoText = document.getElementById('auto-spin-text');

        autoBtn.className = 'w-full sm:w-48 py-4 px-4 rounded-2xl bg-gradient-to-r from-red-700 to-red-600 hover:from-red-600 hover:to-red-500 text-white font-display font-bold text-base tracking-wider shadow-lg shadow-red-900/40 transition transform active:scale-95 flex items-center justify-center gap-2 border border-red-400/40';
        autoIcon.className = 'fa-solid fa-square text-lg animate-pulse';

        for (let i = 1; i <= 10; i++) {
            if (stopAutoSpinRequested) {
                break;
            }

            if (currentBalance < betAmount) {
                alert('Auto Spin dihentikan! Saldo virtual Anda tidak mencukupi untuk melanjutkan taruhan.');
                break;
            }

            autoText.innerText = `STOP AUTO (${i}/10)`;

            const success = await executeSpin(true);
            if (!success) {
                break;
            }

            if (i < 10 && !stopAutoSpinRequested) {
                await sleep(500);
            }
        }

        // Reset auto spin UI state
        isAutoSpinning = false;
        stopAutoSpinRequested = false;
        autoBtn.className = 'w-full sm:w-48 py-4 px-4 rounded-2xl bg-gradient-to-r from-purple-700 via-purple-600 to-indigo-700 hover:from-purple-600 hover:to-indigo-600 text-white font-display font-bold text-base tracking-wider shadow-lg shadow-purple-900/40 transition transform active:scale-95 flex items-center justify-center gap-2 border border-purple-400/40';
        autoIcon.className = 'fa-solid fa-repeat text-lg';
        autoText.innerText = 'AUTO SPIN 10X';
    }

    // ─── API Call ────────────────────────────────────────────────────────────────

    function fetchSpin(betAmount) {
        return fetch("{{ route('api.spin') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ bet_amount: betAmount })
        }).then(res => {
            if (!res.ok) return res.json().then(e => { throw e; });
            return res.json();
        });
    }

    // ─── Phase 1: Rolling Animation ─────────────────────────────────────────────

    function startReelRolling() {
        const gameBadge = document.getElementById('game-status-badge');
        gameBadge.innerText = 'SPINNING...';
        gameBadge.className = 'text-xs px-3 py-1 rounded-full font-bold bg-amber-900/80 border border-amber-500 text-amber-300 animate-pulse';

        document.getElementById('result-card').classList.add('hidden');
        hideJackpotBanner();

        const intervals = [];
        for (let i = 0; i < 15; i++) {
            const cell = document.getElementById(`slot-cell-${i}`);
            cell.classList.add('spinning');
            cell.classList.remove('matched', 'shattering', 'cascade-in', 'landing');

            const col = i % 5;
            // Stagger start per column
            const delay = col * 60;

            let iv = null;
            setTimeout(() => {
                iv = setInterval(() => {
                    const s = availableSymbols[Math.floor(Math.random() * availableSymbols.length)];
                    cell.querySelector('.symbol-icon').innerText = s;
                    cell.querySelector('.symbol-label').innerText = symbolLabels[s] || 'OLYMPUS';
                }, 70 + col * 10);
                intervals.push(iv);
            }, delay);

            intervals.push(null); // placeholder so length matches
        }
        return intervals;
    }

    function stopReelRolling(intervals) {
        intervals.forEach(iv => { if (iv) clearInterval(iv); });
        for (let i = 0; i < 15; i++) {
            document.getElementById(`slot-cell-${i}`).classList.remove('spinning');
        }
    }

    // ─── Phase 4: Land Symbols Column by Column ──────────────────────────────────

    async function landSymbols(grid) {
        // Parse grid text → emoji+label
        const parsed = grid.map(text => {
            if (text.includes('ZEUS'))      return { icon: '⚡', label: text };
            if (text.includes('MAHKOTA'))   return { icon: '👑', label: 'MAHKOTA' };
            if (text.includes('CINCIN'))    return { icon: '💍', label: 'CINCIN' };
            if (text.includes('JAM PASIR')) return { icon: '⏳', label: 'JAM PASIR' };
            return { icon: text, label: symbolLabels[text] || 'OLYMPUS' };
        });

        // Land column by column (5 cols), rows sequentially
        for (let col = 0; col < 5; col++) {
            for (let row = 0; row < 3; row++) {
                const idx = row * 5 + col;
                const cell = document.getElementById(`slot-cell-${idx}`);
                cell.querySelector('.symbol-icon').innerText = parsed[idx].icon;
                cell.querySelector('.symbol-label').innerText = parsed[idx].label;
                cell.classList.remove('spinning');
                cell.classList.add('landing');
                setTimeout(() => cell.classList.remove('landing'), 400);
            }
            await sleep(90); // stagger between columns
        }
        await sleep(300);
    }

    // ─── Phase 5a: Jackpot Sequence (WIN) ───────────────────────────────────────

    async function playJackpotSequence(data) {
        showJackpotBanner();
        triggerScreenFlash('zeus');
        triggerScreenShake();

        // Step 1: Highlight all "winning" cells (ZEUS & MAHKOTA)
        const matchedIndices = [];
        for (let i = 0; i < 15; i++) {
            const icon = document.getElementById(`slot-cell-${i}`).querySelector('.symbol-icon').innerText;
            if (icon === '⚡' || icon === '👑') matchedIndices.push(i);
        }
        if (matchedIndices.length === 0) {
            // Fallback: mark all as matched
            for (let i = 0; i < 15; i++) matchedIndices.push(i);
        }

        matchedIndices.forEach(i => {
            const cell = document.getElementById(`slot-cell-${i}`);
            cell.classList.add('matched');
        });

        // Step 2: Strike each matched cell with a lightning bolt
        for (let k = 0; k < matchedIndices.length; k++) {
            await sleep(150);
            spawnLightningOnCell(matchedIndices[k]);
            spawnParticles(matchedIndices[k], ['⚡', '✨', '💫', '🌟', '⭐'], 8);
        }

        await sleep(600);

        // Step 3: Show multiplier popup on each matched cell
        matchedIndices.forEach(i => spawnMultiplierPopup(i, data.multiplier));

        await sleep(400);

        // Step 4: Shatter matched symbols
        for (let k = 0; k < matchedIndices.length; k++) {
            await sleep(60);
            shatterCell(matchedIndices[k]);
        }
        await sleep(600);

        // Step 5: Rain coins
        rainCoins(20);

        // Step 6: Cascade new random symbols into shattered cells
        await sleep(300);
        await cascadeNewSymbols(matchedIndices);

        // Step 7: Show jackpot win popup
        await sleep(400);
        showJackpotPopup(data);
    }

    // ─── Phase 5b: Low Win (Pancingan) ──────────────────────────────────────────

    async function playLowWinSequence(data) {
        // Highlight JAM PASIR (⏳) cells
        const matchedIndices = [];
        for (let i = 0; i < 15; i++) {
            const icon = document.getElementById(`slot-cell-${i}`).querySelector('.symbol-icon').innerText;
            if (icon === '⏳') matchedIndices.push(i);
        }

        matchedIndices.forEach(i => {
            const cell = document.getElementById(`slot-cell-${i}`);
            cell.classList.add('matched');
            cell.style.borderColor = '#F59E0B';
        });

        for (let k = 0; k < matchedIndices.length; k++) {
            await sleep(120);
            spawnParticles(matchedIndices[k], ['✨', '💰', '🌟'], 5);
            spawnMultiplierPopup(matchedIndices[k], data.multiplier);
        }

        await sleep(600);

        matchedIndices.forEach(i => shatterCell(i));
        await sleep(500);
        await cascadeNewSymbols(matchedIndices);

        // Show low-win toast notification
        await sleep(300);
        showLowWinToast(data);
    }

    // ─── Phase 5c: Lose (Rungkad) ───────────────────────────────────────────────

    async function playLoseSequence() {
        triggerScreenFlash('lose');

        // Shake all cells briefly then quiver to death
        for (let i = 0; i < 15; i++) {
            const cell = document.getElementById(`slot-cell-${i}`);
            cell.style.animation = 'reelShake 0.08s 5 alternate';
            setTimeout(() => { cell.style.animation = ''; }, 500);
        }

        await sleep(300);
        spawnParticles(7, ['💀', '😭', '🔥', '💸'], 10);
        await sleep(400);
    }

    // ─── WIN POPUP & TOAST ──────────────────────────────────────────────────────

    function showJackpotPopup(data) {
        const modal   = document.getElementById('win-popup-modal');
        const backdrop = document.getElementById('win-popup-backdrop');

        // Configure modal to jackpot style
        modal.className = 'jackpot';
        document.getElementById('popup-zeus-icon').className = 'popup-zeus-icon';
        document.getElementById('popup-zeus-icon').innerText = '⚡';
        document.getElementById('popup-subtitle').innerText  = 'ZEUS JACKPOT — DIKONTROL BANDAR';
        document.getElementById('popup-multiplier-text').innerText = `x${data.multiplier} Perkalian Zeus`;
        document.getElementById('popup-manipulation-label').innerText = data.status_label;
        document.getElementById('popup-close-btn').querySelector('span').innerText = 'TUTUP & PUTAR LAGI';

        // Stats row
        document.getElementById('popup-stat-bet').innerText     = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.bet_amount);
        document.getElementById('popup-stat-payout').innerText  = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.win_amount);
        document.getElementById('popup-stat-balance').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.new_balance);

        // Animated counter-up for win amount
        animateCountUp('popup-win-amount', 0, data.win_amount, 1200);

        // Open backdrop
        backdrop.classList.add('open');

        // Confetti burst
        launchPopupConfetti(30);
    }

    function showLowWinToast(data) {
        const toast = document.getElementById('small-win-toast');
        document.getElementById('toast-icon').innerText       = '⏳';
        document.getElementById('toast-amount').innerText     = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.win_amount);
        document.getElementById('toast-multiplier').innerText = `x${data.multiplier} Perkalian`;
        document.getElementById('toast-subtitle').innerText   = 'MENANG KECIL — PANCINGAN BANDAR';

        toast.classList.add('show');

        // Auto-dismiss after 4 seconds
        setTimeout(() => {
            toast.classList.remove('show');
        }, 4000);
    }

    function closeWinPopup() {
        const backdrop = document.getElementById('win-popup-backdrop');
        const modal    = document.getElementById('win-popup-modal');

        modal.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
        modal.style.transform  = 'scale(0.8) translateY(40px)';
        modal.style.opacity    = '0';
        backdrop.style.transition = 'opacity 0.3s ease';
        backdrop.style.opacity = '0';

        setTimeout(() => {
            backdrop.classList.remove('open');
            modal.style.transform  = '';
            modal.style.opacity    = '';
            backdrop.style.opacity = '';
        }, 350);
    }

    function animateCountUp(elementId, from, to, durationMs) {
        const el    = document.getElementById(elementId);
        const start = performance.now();
        const fmt   = v => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(v));

        function step(now) {
            const elapsed  = now - start;
            const progress = Math.min(elapsed / durationMs, 1);
            // Ease-out cubic
            const eased = 1 - Math.pow(1 - progress, 3);
            el.innerText = fmt(from + (to - from) * eased);
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    function launchPopupConfetti(count) {
        const emojis = ['⚡', '💎', '💰', '🌟', '✨', '🪙', '👑', '💫'];
        for (let k = 0; k < count; k++) {
            setTimeout(() => {
                const el = document.createElement('div');
                el.className = 'popup-confetti';
                el.innerText = emojis[Math.floor(Math.random() * emojis.length)];
                el.style.left     = (Math.random() * 100) + 'vw';
                el.style.fontSize = (0.9 + Math.random() * 1.4) + 'rem';
                const dur = 2 + Math.random() * 2;
                el.style.animationDuration = dur + 's';
                document.body.appendChild(el);
                setTimeout(() => el.remove(), dur * 1000 + 200);
            }, k * 80);
        }
    }

    // ─── Animation Helpers ───────────────────────────────────────────────────────

    function triggerScreenFlash(type) {
        const overlay = document.getElementById('zeus-flash-overlay');
        overlay.className = '';
        void overlay.offsetWidth; // reflow
        overlay.className = type === 'lose' ? 'lose-flash' : 'active';
    }

    function triggerScreenShake() {
        const container = document.getElementById('slot-grid-container');
        container.classList.remove('screen-shake');
        void container.offsetWidth;
        container.classList.add('screen-shake');
        setTimeout(() => container.classList.remove('screen-shake'), 600);
    }

    function showJackpotBanner() {
        const banner = document.getElementById('jackpot-banner');
        banner.style.display = 'block';
        banner.style.animation = '';
        void banner.offsetWidth;
        banner.style.animation = 'jackpotBanner 0.5s ease forwards';
    }

    function hideJackpotBanner() {
        document.getElementById('jackpot-banner').style.display = 'none';
    }

    function spawnLightningOnCell(idx) {
        const cell = document.getElementById(`slot-cell-${idx}`);
        const bolt = document.createElement('div');
        bolt.className = 'lightning-bolt';
        bolt.innerText = '⚡';
        cell.style.position = 'relative';
        cell.appendChild(bolt);
        setTimeout(() => bolt.remove(), 600);
    }

    function spawnParticles(idx, emojis, count) {
        const cell = document.getElementById(`slot-cell-${idx}`);
        const rect = cell.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;

        for (let k = 0; k < count; k++) {
            const p = document.createElement('div');
            p.className = 'particle';
            p.style.left = cx + 'px';
            p.style.top  = cy + 'px';
            p.style.position = 'fixed';

            const angle = (360 / count) * k + Math.random() * 20;
            const dist  = 50 + Math.random() * 80;
            const tx = Math.cos(angle * Math.PI / 180) * dist;
            const ty = Math.sin(angle * Math.PI / 180) * dist;
            p.style.setProperty('--tx', tx + 'px');
            p.style.setProperty('--ty', ty + 'px');
            p.style.animationDuration = (0.5 + Math.random() * 0.4) + 's';
            p.innerText = emojis[Math.floor(Math.random() * emojis.length)];

            document.body.appendChild(p);
            setTimeout(() => p.remove(), 1000);
        }
    }

    function spawnMultiplierPopup(idx, multiplier) {
        const cell = document.getElementById(`slot-cell-${idx}`);
        cell.style.position = 'relative';
        const pop = document.createElement('div');
        pop.className = 'multiplier-popup';
        pop.innerText = 'x' + multiplier;
        cell.appendChild(pop);
        setTimeout(() => pop.remove(), 1300);
    }

    function shatterCell(idx) {
        const cell = document.getElementById(`slot-cell-${idx}`);
        // Spawn 6 shard emoji flying off
        const icon = cell.querySelector('.symbol-icon').innerText;
        spawnParticles(idx, [icon, '💥', '✨'], 6);

        cell.classList.remove('matched');
        cell.classList.add('shattering');
        setTimeout(() => {
            cell.classList.remove('shattering');
            cell.style.opacity = '0';
            cell.querySelector('.symbol-icon').innerText = '　'; // blank
            cell.querySelector('.symbol-label').innerText = '';
        }, 450);
    }

    async function cascadeNewSymbols(indices) {
        await sleep(200);
        for (let k = 0; k < indices.length; k++) {
            const idx = indices[k];
            const cell = document.getElementById(`slot-cell-${idx}`);
            const s = availableSymbols[Math.floor(Math.random() * availableSymbols.length)];
            cell.style.opacity = '1';
            cell.classList.add('cascade-in');
            cell.querySelector('.symbol-icon').innerText = s;
            cell.querySelector('.symbol-label').innerText = symbolLabels[s] || 'OLYMPUS';
            await sleep(80);
            setTimeout(() => cell.classList.remove('cascade-in'), 500);
        }
    }

    function rainCoins(count) {
        for (let k = 0; k < count; k++) {
            setTimeout(() => {
                const coin = document.createElement('div');
                coin.className = 'coin-rain';
                coin.innerText = ['💰', '🪙', '💎', '⭐'][Math.floor(Math.random() * 4)];
                coin.style.left = (Math.random() * 100) + 'vw';
                coin.style.fontSize = (1 + Math.random() * 1.5) + 'rem';
                const dur = (1.5 + Math.random() * 1.5);
                coin.style.animationDuration = dur + 's';
                document.body.appendChild(coin);
                setTimeout(() => coin.remove(), dur * 1000 + 100);
            }, k * 100);
        }
    }

    // ─── UI State Helpers ────────────────────────────────────────────────────────

    function resetUI() {
        const spinBtn  = document.getElementById('spin-btn');
        const spinIcon = document.getElementById('spin-icon');
        const spinText = document.getElementById('spin-text');

        spinBtn.disabled = true;
        spinBtn.classList.add('opacity-75', 'cursor-not-allowed');
        spinIcon.classList.add('fa-spin');
        spinText.innerText = 'MEMUTAR REEL...';

        // Clear matched states
        for (let i = 0; i < 15; i++) {
            const c = document.getElementById(`slot-cell-${i}`);
            c.classList.remove('matched', 'shattering', 'cascade-in', 'landing');
            c.style.opacity = '1';
            c.style.animation = '';
            c.style.borderColor = '';
            c.style.boxShadow = '';
        }
    }

    function unlockUI() {
        isSpinning = false;
        const spinBtn  = document.getElementById('spin-btn');
        const spinIcon = document.getElementById('spin-icon');
        const spinText = document.getElementById('spin-text');
        const gameBadge = document.getElementById('game-status-badge');

        spinBtn.disabled = false;
        spinBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        spinIcon.classList.remove('fa-spin');
        spinText.innerText = 'PUTAR (SPIN)';
        gameBadge.innerText = 'READY TO SPIN';
        gameBadge.className = 'text-xs px-3 py-1 rounded-full font-bold bg-indigo-900/80 border border-indigo-700 text-indigo-300';
    }

    function updateBalance(newBalance) {
        currentBalance = parseFloat(newBalance);
        const fmt = 'Rp ' + new Intl.NumberFormat('id-ID').format(currentBalance);
        document.getElementById('player-balance').innerText = fmt;
        const nav = document.getElementById('nav-user-balance');
        if (nav) nav.innerText = fmt;
    }

    // ─── Result Card ─────────────────────────────────────────────────────────────

    function renderResultCard(data) {
        const card        = document.getElementById('result-card');
        const iconBg      = document.getElementById('result-icon-bg');
        const icon        = document.getElementById('result-icon');
        const title       = document.getElementById('result-title');
        const badge       = document.getElementById('result-badge');
        const message     = document.getElementById('result-message');
        const multiplier  = document.getElementById('result-multiplier');
        const payout      = document.getElementById('result-payout');
        const statusLabel = document.getElementById('result-status-label');

        card.classList.remove('hidden');
        multiplier.innerText  = `${data.multiplier}x`;
        payout.innerText      = `Rp ${new Intl.NumberFormat('id-ID').format(data.win_amount)}`;
        statusLabel.innerText = data.status_label;
        message.innerText     = data.message;

        if (data.status_manipulasi === 'lose') {
            card.className   = 'glass-card rounded-2xl p-6 border border-red-500/50 bg-red-950/40 shadow-2xl';
            iconBg.className = 'w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 bg-red-900/80 text-red-300 border border-red-500/40';
            icon.className   = 'fa-solid fa-skull-crossbones';
            title.className  = 'font-display font-black text-xl text-red-400';
            title.innerText  = 'RUNGKAD! (HASIL DI-SETTING BANDAR)';
            badge.className  = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-red-950 text-red-300 border border-red-500/50';
        } else if (data.status_manipulasi === 'win') {
            card.className   = 'glass-card rounded-2xl p-6 border border-emerald-500/50 bg-emerald-950/40 shadow-2xl zeus-lightning';
            iconBg.className = 'w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 bg-emerald-900/80 text-emerald-300 border border-emerald-500/40';
            icon.className   = 'fa-solid fa-bolt-lightning text-amber-400';
            title.className  = 'font-display font-black text-xl text-emerald-400';
            title.innerText  = 'JACKPOT! (HASIL DI-SETTING BANDAR)';
            badge.className  = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-950 text-emerald-300 border border-emerald-500/50';
        } else {
            card.className   = 'glass-card rounded-2xl p-6 border border-amber-500/50 bg-amber-950/40 shadow-2xl';
            iconBg.className = 'w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 bg-amber-900/80 text-amber-300 border border-amber-500/40';
            icon.className   = 'fa-solid fa-fish';
            title.className  = 'font-display font-black text-xl text-amber-400';
            title.innerText  = 'MENANG KECIL (PANCINGAN BANDAR)';
            badge.className  = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-amber-950 text-amber-300 border border-amber-500/50';
        }
    }

    // ─── History Row ─────────────────────────────────────────────────────────────

    function addHistoryRow(data) {
        const tbody   = document.getElementById('player-history-body');
        const emptyRow = document.getElementById('empty-history-row');
        if (emptyRow) emptyRow.remove();

        const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const isRungkad = data.status_manipulasi === 'lose';
        const badgeClass  = isRungkad ? 'bg-red-950 text-red-300' : 'bg-emerald-950 text-emerald-300';
        const payoutClass = data.win_amount > 0 ? 'text-emerald-400' : 'text-red-400';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="py-2.5 px-3 font-mono text-indigo-400">${now}</td>
            <td class="py-2.5 px-3 text-amber-300">Rp ${new Intl.NumberFormat('id-ID').format(data.bet_amount)}</td>
            <td class="py-2.5 px-3 font-bold ${payoutClass}">Rp ${new Intl.NumberFormat('id-ID').format(data.win_amount)}</td>
            <td class="py-2.5 px-3 font-bold text-purple-300">${data.multiplier}x</td>
            <td class="py-2.5 px-3">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${badgeClass}">${data.status_label}</span>
            </td>
        `;
        tbody.insertBefore(tr, tbody.firstChild);
    }
</script>
@endpush
