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

/* =============================================
   AUTHENTIC SLOT MACHINE STYLES & PAYLINE SYSTEM
   ============================================= */

/* Anticipation Tension State for Reels 4 & 5 */
.slot-box.anticipation {
    border-color: #F59E0B !important;
    box-shadow: 0 0 25px #F59E0B, inset 0 0 20px rgba(245, 158, 11, 0.5) !important;
    animation: anticipationPulse 0.15s infinite alternate !important;
}

@keyframes anticipationPulse {
    0%   { transform: scale(1); filter: brightness(1.2); }
    100% { transform: scale(1.04); filter: brightness(1.6); }
}

/* Winning Payline Cell Highlight */
.slot-box.payline-winner {
    border-color: #10B981 !important;
    box-shadow: 0 0 25px rgba(16, 185, 129, 0.9), inset 0 0 20px rgba(16, 185, 129, 0.4) !important;
    animation: winnerCellGlow 0.5s ease-in-out infinite alternate;
    z-index: 25 !important;
}

@keyframes winnerCellGlow {
    0%   { transform: scale(1); background: rgba(16, 185, 129, 0.1); }
    100% { transform: scale(1.06); background: rgba(16, 185, 129, 0.25); }
}

/* Payline Number Side Tags */
.payline-tag {
    font-family: 'Outfit', sans-serif;
    font-weight: 900;
    font-size: 0.65rem;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    background: #1e1b4b;
    border: 1px solid #4338ca;
    color: #a5b4fc;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    user-select: none;
    cursor: pointer;
}

.payline-tag:hover {
    border-color: #fbbf24;
    color: #fbbf24;
    transform: scale(1.1);
}

.payline-tag.active {
    background: linear-gradient(135deg, #10B981, #047857) !important;
    border-color: #34D399 !important;
    color: #FFFFFF !important;
    box-shadow: 0 0 12px #10B981;
    transform: scale(1.25);
    animation: tagActivePulse 0.4s infinite alternate;
}

@keyframes tagActivePulse {
    0%   { box-shadow: 0 0 8px #10B981; }
    100% { box-shadow: 0 0 20px #34D399; }
}

/* Slot Machine LCD / Digital Meters */
.slot-meter-box {
    background: linear-gradient(180deg, #090814 0%, #05040d 100%);
    border: 1px solid #312E81;
    border-radius: 12px;
    padding: 6px 12px;
    text-align: center;
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.8);
}

.slot-meter-label {
    font-size: 0.58rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #818CF8;
}

.slot-meter-val {
    font-family: 'Outfit', monospace;
    font-weight: 900;
    font-size: 1.05rem;
    letter-spacing: 0.04em;
}

/* Payline Ticker Ribbon */
#payline-ticker {
    display: none;
    background: linear-gradient(90deg, rgba(5, 150, 105, 0.9), rgba(16, 185, 129, 0.95), rgba(5, 150, 105, 0.9));
    border: 1px solid #34D399;
    color: #ffffff;
    font-family: 'Outfit', sans-serif;
    font-weight: 900;
    font-size: 0.85rem;
    letter-spacing: 0.05em;
    border-radius: 12px;
    padding: 8px 16px;
    text-align: center;
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
    animation: tickerIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

@keyframes tickerIn {
    0%   { transform: scale(0.9) translateY(-10px); opacity: 0; }
    100% { transform: scale(1) translateY(0); opacity: 1; }
}

/* Near-Miss Alert Banner */
#near-miss-banner {
    display: none;
    background: linear-gradient(90deg, rgba(88, 28, 135, 0.9), rgba(124, 58, 237, 0.95), rgba(88, 28, 135, 0.9));
    border: 1px solid #C084FC;
    color: #F5D0FE;
    font-size: 0.75rem;
    border-radius: 12px;
    padding: 8px 14px;
    animation: nearMissBounce 0.4s ease;
}

@keyframes nearMissBounce {
    0%   { transform: translateY(-8px); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
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
     PAYTABLE & RULES MODAL (10 PAYLINES & SYMBOL MULTIPLIERS)
     ============================================================ -->
<div id="paytable-modal-backdrop" class="fixed inset-0 bg-black/85 backdrop-blur-md z-[99999] hidden items-center justify-center p-4">
    <div class="max-w-3xl w-full glass-card rounded-3xl gold-border overflow-hidden shadow-2xl relative max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="p-5 bg-gradient-to-r from-indigo-950 via-purple-950 to-indigo-950 border-b border-indigo-700/60 flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 text-xl">
                    <i class="fa-solid fa-circle-info"></i>
                </div>
                <div>
                    <h3 class="font-display font-black text-lg text-white">ATURAN &amp; PAYTABLE RESMI</h3>
                    <p class="text-xs text-amber-300 font-semibold">Konsep Mesin Slot 5x3 • 10 Payline Aktif • Edukasi Manipulasi</p>
                </div>
            </div>
            <button onclick="closePaytableModal()" class="text-indigo-400 hover:text-white text-xl">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Scrollable Body -->
        <div class="p-6 overflow-y-auto space-y-6 text-indigo-200">
            
            <!-- Section 1: Symbol Multipliers -->
            <div>
                <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-gem"></i> Nilai Payout Simbol (Perkalian dari Nilai Bet)
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <!-- Mahkota -->
                    <div class="bg-indigo-950/80 border border-amber-500/40 rounded-xl p-3 text-center">
                        <div class="text-3xl mb-1">👑</div>
                        <p class="text-xs font-black text-amber-300">MAHKOTA</p>
                        <p class="text-[10px] text-indigo-400 font-semibold">Tingkat Tertinggi</p>
                        <div class="mt-2 text-[11px] space-y-0.5 font-mono">
                            <div class="text-emerald-400 font-bold">5x: 50.0x Bet</div>
                            <div class="text-indigo-300">4x: 15.0x Bet</div>
                            <div class="text-indigo-400">3x: 5.0x Bet</div>
                        </div>
                    </div>
                    <!-- Jam Pasir -->
                    <div class="bg-indigo-950/80 border border-indigo-700/60 rounded-xl p-3 text-center">
                        <div class="text-3xl mb-1">⏳</div>
                        <p class="text-xs font-black text-amber-200">JAM PASIR</p>
                        <p class="text-[10px] text-indigo-400 font-semibold">Tingkat Tinggi</p>
                        <div class="mt-2 text-[11px] space-y-0.5 font-mono">
                            <div class="text-emerald-400 font-bold">5x: 25.0x Bet</div>
                            <div class="text-indigo-300">4x: 8.0x Bet</div>
                            <div class="text-indigo-400">3x: 3.0x Bet</div>
                        </div>
                    </div>
                    <!-- Cincin -->
                    <div class="bg-indigo-950/80 border border-indigo-700/60 rounded-xl p-3 text-center">
                        <div class="text-3xl mb-1">💍</div>
                        <p class="text-xs font-black text-purple-300">CINCIN DELIMA</p>
                        <p class="text-[10px] text-indigo-400 font-semibold">Tingkat Menengah</p>
                        <div class="mt-2 text-[11px] space-y-0.5 font-mono">
                            <div class="text-emerald-400 font-bold">5x: 12.0x Bet</div>
                            <div class="text-indigo-300">4x: 5.0x Bet</div>
                            <div class="text-indigo-400">3x: 2.0x Bet</div>
                        </div>
                    </div>
                    <!-- Cawan -->
                    <div class="bg-indigo-950/80 border border-indigo-700/60 rounded-xl p-3 text-center">
                        <div class="text-3xl mb-1">🍷</div>
                        <p class="text-xs font-black text-red-300">CAWAN ANGGUR</p>
                        <p class="text-[10px] text-indigo-400 font-semibold">Tingkat Menengah</p>
                        <div class="mt-2 text-[11px] space-y-0.5 font-mono">
                            <div class="text-emerald-400 font-bold">5x: 8.0x Bet</div>
                            <div class="text-indigo-300">4x: 4.0x Bet</div>
                            <div class="text-indigo-400">3x: 1.5x Bet</div>
                        </div>
                    </div>
                    <!-- Permata Merah -->
                    <div class="bg-indigo-950/80 border border-indigo-800 rounded-xl p-3 text-center">
                        <div class="text-3xl mb-1">💎</div>
                        <p class="text-xs font-black text-pink-300">PERMATA MERAH</p>
                        <p class="text-[10px] text-indigo-400 font-semibold">Tingkat Rendah</p>
                        <div class="mt-2 text-[11px] space-y-0.5 font-mono">
                            <div class="text-emerald-400 font-bold">5x: 5.0x Bet</div>
                            <div class="text-indigo-300">4x: 2.5x Bet</div>
                            <div class="text-indigo-400">3x: 1.0x Bet</div>
                        </div>
                    </div>
                    <!-- Giok Hijau -->
                    <div class="bg-indigo-950/80 border border-indigo-800 rounded-xl p-3 text-center">
                        <div class="text-3xl mb-1">💚</div>
                        <p class="text-xs font-black text-emerald-300">GIOK HIJAU</p>
                        <p class="text-[10px] text-indigo-400 font-semibold">Tingkat Rendah</p>
                        <div class="mt-2 text-[11px] space-y-0.5 font-mono">
                            <div class="text-emerald-400 font-bold">5x: 4.0x Bet</div>
                            <div class="text-indigo-300">4x: 1.8x Bet</div>
                            <div class="text-indigo-400">3x: 0.6x Bet</div>
                        </div>
                    </div>
                    <!-- Safir Biru -->
                    <div class="bg-indigo-950/80 border border-indigo-800 rounded-xl p-3 text-center">
                        <div class="text-3xl mb-1">💙</div>
                        <p class="text-xs font-black text-blue-300">SAFIR BIRU</p>
                        <p class="text-[10px] text-indigo-400 font-semibold">Tingkat Rendah</p>
                        <div class="mt-2 text-[11px] space-y-0.5 font-mono">
                            <div class="text-emerald-400 font-bold">5x: 3.0x Bet</div>
                            <div class="text-indigo-300">4x: 1.2x Bet</div>
                            <div class="text-indigo-400">3x: 0.4x Bet</div>
                        </div>
                    </div>
                    <!-- Zeus Scatter -->
                    <div class="bg-gradient-to-b from-purple-950 to-indigo-950 border border-purple-500/50 rounded-xl p-3 text-center">
                        <div class="text-3xl mb-1">⚡</div>
                        <p class="text-xs font-black text-amber-300">ZEUS SCATTER</p>
                        <p class="text-[10px] text-purple-300 font-semibold">Simbol Spesial</p>
                        <div class="mt-2 text-[10px] text-indigo-200">
                            <b>2x:</b> Tensi Near-Miss<br>
                            <b>3x+:</b> Jackpot Perkalian Petir (50x - 500x)
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: 10 Paylines Diagram -->
            <div class="pt-4 border-t border-indigo-900/60">
                <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-lines-leaning"></i> 10 Pola Garis Pembayaran (Paylines)
                </h4>
                <p class="text-xs text-indigo-300 mb-3">Kombinasi simbol harus berurutan minimal 3 simbol dari reel 1 (paling kiri) ke kanan mengikuti pola garis berikut:</p>
                
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 text-xs">
                    <div class="bg-indigo-950/60 border border-indigo-800 p-2 rounded-lg text-center">
                        <span class="font-bold text-amber-300">Line 1:</span> Horizontal Tengah
                    </div>
                    <div class="bg-indigo-950/60 border border-indigo-800 p-2 rounded-lg text-center">
                        <span class="font-bold text-amber-300">Line 2:</span> Horizontal Atas
                    </div>
                    <div class="bg-indigo-950/60 border border-indigo-800 p-2 rounded-lg text-center">
                        <span class="font-bold text-amber-300">Line 3:</span> Horizontal Bawah
                    </div>
                    <div class="bg-indigo-950/60 border border-indigo-800 p-2 rounded-lg text-center">
                        <span class="font-bold text-amber-300">Line 4:</span> Bentuk V
                    </div>
                    <div class="bg-indigo-950/60 border border-indigo-800 p-2 rounded-lg text-center">
                        <span class="font-bold text-amber-300">Line 5:</span> Bentuk Piramida
                    </div>
                    <div class="bg-indigo-950/60 border border-indigo-800 p-2 rounded-lg text-center">
                        <span class="font-bold text-amber-300">Line 6:</span> Top Zigzag
                    </div>
                    <div class="bg-indigo-950/60 border border-indigo-800 p-2 rounded-lg text-center">
                        <span class="font-bold text-amber-300">Line 7:</span> Bottom Zigzag
                    </div>
                    <div class="bg-indigo-950/60 border border-indigo-800 p-2 rounded-lg text-center">
                        <span class="font-bold text-amber-300">Line 8:</span> Puncak (Peak)
                    </div>
                    <div class="bg-indigo-950/60 border border-indigo-800 p-2 rounded-lg text-center">
                        <span class="font-bold text-amber-300">Line 9:</span> Lembah (Valley)
                    </div>
                    <div class="bg-indigo-950/60 border border-indigo-800 p-2 rounded-lg text-center">
                        <span class="font-bold text-amber-300">Line 10:</span> Diagonal Turun
                    </div>
                </div>
            </div>

            <!-- Section 3: Edukasi Manipulasi Slot -->
            <div class="pt-4 border-t border-indigo-900/60 bg-red-950/30 border border-red-500/30 rounded-2xl p-4">
                <h4 class="text-xs font-black text-red-400 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-400"></i> Fakta Edukasi: Realita Mesin Slot vs Judi Online Ilegal
                </h4>
                <ul class="text-xs space-y-2 text-indigo-200 list-disc list-inside">
                    <li><b class="text-amber-300">Manipulasi 100% oleh Bandar:</b> Di judi online ilegal, kemenangan dan kekalahan TIDAK diacak murni oleh keberuntungan, melainkan diatur dari panel kendali bandar (seperti menu Admin di simulasi ini).</li>
                    <li><b class="text-amber-300">Trik Psikologis "Near-Miss" (Hampir Menang):</b> Bandar sengaja memunculkan 2 scatter atau 2 simbol mahkota yang 'hampir' tersambung untuk memicu dopamin dan ilusi bahwa keberuntungan Anda sudah dekat, padahal hasil sudah di-lock kalah.</li>
                    <li><b class="text-amber-300">Loss Disguised as Win (Kemenangan Umpan):</b> Ketika Anda bet Rp 20.000 dan menang Rp 15.000, mesin tetap merayakan dengan suara kemenangan meriah padahal Anda rugi Rp 5.000.</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="p-4 bg-indigo-950 border-t border-indigo-900/60 text-center shrink-0">
            <button onclick="closePaytableModal()" class="py-2.5 px-6 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-gray-950 font-display font-black text-xs uppercase tracking-wider shadow-lg transition">
                TUTUP PAYTABLE
            </button>
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

        <div class="flex flex-wrap items-center gap-3">
            <!-- Saldo Virtual Siswa -->
            <div class="flex items-center space-x-3 bg-indigo-950/80 border border-amber-500/30 px-4 py-2 rounded-xl shadow-lg">
                <div class="text-right">
                    <p class="text-[10px] font-semibold text-indigo-300 uppercase tracking-wider">Saldo Siswa</p>
                    <p id="player-balance" class="font-display font-black text-xl text-amber-400">
                        Rp {{ number_format($user->balance, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-xl text-amber-400">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>

            <!-- Tombol Deposit -->
            <button onclick="openDepositModal()" class="px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 via-emerald-600 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-gray-950 font-display font-black text-xs tracking-wider shadow-lg shadow-emerald-500/20 transition transform active:scale-95 flex items-center gap-1.5 border border-emerald-300">
                <i class="fa-solid fa-plus-circle text-sm"></i>
                <span>DEPOSIT</span>
            </button>
        </div>
    </div>

    <!-- Main Slot Machine Grid Container -->
    <div id="slot-grid-container" class="glass-card p-6 rounded-3xl gold-border shadow-2xl relative overflow-visible">

        <!-- Jackpot Banner (appears on win) -->
        <div id="jackpot-banner">⚡ ZEUS JACKPOT! ⚡</div>

        <!-- Slot Grid Title Header & Controls -->
        <div class="flex flex-wrap items-center justify-between mb-4 pb-3 border-t border-indigo-900/60 pt-3 mt-2 gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-indigo-300 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-grip text-amber-400"></i> REEL 5x3 • 10 PAYLINES
                </span>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Tombol Aturan & Paytable -->
                <button onclick="openPaytableModal()" type="button"
                    class="px-3 py-1.5 rounded-xl bg-indigo-950/80 hover:bg-indigo-900 border border-amber-500/40 text-amber-300 text-xs font-bold transition flex items-center gap-1.5 shadow">
                    <i class="fa-solid fa-circle-info text-amber-400"></i>
                    <span>PAYTABLE</span>
                </button>

                <!-- Tombol Audio Mute/Unmute -->
                <button onclick="toggleSound()" type="button" id="sound-btn"
                    class="px-3 py-1.5 rounded-xl bg-indigo-950/80 hover:bg-indigo-900 border border-indigo-700 text-indigo-300 text-xs font-bold transition flex items-center gap-1.5 shadow">
                    <i class="fa-solid fa-volume-high text-amber-400" id="sound-icon"></i>
                    <span id="sound-text">AUDIO ON</span>
                </button>

                <span id="game-status-badge" class="text-xs px-3 py-1 rounded-full font-bold bg-indigo-900/80 border border-indigo-700 text-indigo-300">
                    READY TO SPIN
                </span>
            </div>
        </div>

        <!-- Slot Machine LCD Digital Meters -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
            <div class="slot-meter-box">
                <div class="slot-meter-label"><i class="fa-solid fa-coins text-amber-400 mr-1"></i> Taruhan (Bet)</div>
                <div class="slot-meter-val text-amber-300" id="meter-bet">Rp 10.000</div>
            </div>
            <div class="slot-meter-box">
                <div class="slot-meter-label"><i class="fa-solid fa-lines-leaning text-indigo-400 mr-1"></i> Garis Aktif</div>
                <div class="slot-meter-val text-indigo-200">10 LINES</div>
            </div>
            <div class="slot-meter-box">
                <div class="slot-meter-label"><i class="fa-solid fa-trophy text-emerald-400 mr-1"></i> Menang (Win)</div>
                <div class="slot-meter-val text-emerald-400" id="meter-win">Rp 0</div>
            </div>
            <div class="slot-meter-box">
                <div class="slot-meter-label"><i class="fa-solid fa-wallet text-purple-400 mr-1"></i> Saldo Pemain</div>
                <div class="slot-meter-val text-purple-300" id="meter-balance">Rp {{ number_format($user->balance, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Winning Payline Ticker Ribbon -->
        <div id="payline-ticker" class="mb-3">
            <i class="fa-solid fa-sparkles mr-2 text-amber-300"></i>
            <span id="payline-ticker-text">SELAMAT! KEMENANGAN TERHUBUNG!</span>
        </div>

        <!-- Near-Miss Educational Alert Banner -->
        <div id="near-miss-banner" class="mb-3 flex items-start gap-2.5">
            <i class="fa-solid fa-eye text-amber-300 text-base shrink-0 mt-0.5"></i>
            <div>
                <p class="font-black text-amber-300 uppercase tracking-wider text-[11px]">TRIK PSIKOLOGI "NEAR-MISS" (HAMPIR MENANG) BANDAR</p>
                <p class="text-[11px] text-purple-200 mt-0.5" id="near-miss-text">
                    Bandar sengaja memunculkan 2 Scatter Zeus untuk memicu ilusi bahwa Anda "hampir jackpot", padahal sistem sudah memastikan Anda kalah 100%!
                </p>
            </div>
        </div>

        <!-- 5x3 Reel Grid UI Flanked with Payline Tags -->
        <div class="flex items-center gap-2 sm:gap-3 my-3">
            <!-- Left Payline Indicators (1, 2, 4, 6, 8) -->
            <div class="flex flex-col justify-around py-4 h-full gap-5 shrink-0 hidden sm:flex">
                <div class="payline-tag" id="payline-tag-1" onclick="previewPayline(1)" title="Line 1 (Tengah)">1</div>
                <div class="payline-tag" id="payline-tag-2" onclick="previewPayline(2)" title="Line 2 (Atas)">2</div>
                <div class="payline-tag" id="payline-tag-4" onclick="previewPayline(4)" title="Line 4 (V-Shape)">4</div>
                <div class="payline-tag" id="payline-tag-6" onclick="previewPayline(6)" title="Line 6 (Top Zigzag)">6</div>
                <div class="payline-tag" id="payline-tag-8" onclick="previewPayline(8)" title="Line 8 (Puncak)">8</div>
            </div>

            <!-- Main 5x3 Reel Grid -->
            <div id="reel-grid" class="grid grid-cols-5 gap-2 sm:gap-3 flex-grow">
                @for($i = 0; $i < 15; $i++)
                <div class="slot-cell-wrapper">
                    <div id="slot-cell-{{ $i }}" class="slot-box w-full h-full rounded-2xl flex flex-col items-center justify-center p-2 text-center select-none shadow-inner border border-indigo-800/80">
                        <span class="symbol-icon text-3xl sm:text-4xl">
                            @php
                                $defaultSymbols = ['⚡', '👑', '💍', '⏳', '💎', '💚', '💙', '🍷'];
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

            <!-- Right Payline Indicators (3, 5, 7, 9, 10) -->
            <div class="flex flex-col justify-around py-4 h-full gap-5 shrink-0 hidden sm:flex">
                <div class="payline-tag" id="payline-tag-3" onclick="previewPayline(3)" title="Line 3 (Bawah)">3</div>
                <div class="payline-tag" id="payline-tag-5" onclick="previewPayline(5)" title="Line 5 (Piramida)">5</div>
                <div class="payline-tag" id="payline-tag-7" onclick="previewPayline(7)" title="Line 7 (Bot Zigzag)">7</div>
                <div class="payline-tag" id="payline-tag-9" onclick="previewPayline(9)" title="Line 9 (Lembah)">9</div>
                <div class="payline-tag" id="payline-tag-10" onclick="previewPayline(10)" title="Line 10 (Diagonal)">10</div>
            </div>
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

            <!-- Spin Control Action Buttons: hanya 2 tombol -->
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto justify-end">
                <!-- Tombol Spin Gratis (langsung spin tanpa potong saldo) -->
                <button id="free-spin-btn" onclick="executeFreeSpin()"
                    class="w-full sm:w-52 py-4 px-6 rounded-2xl bg-gradient-to-r from-fuchsia-600 via-purple-600 to-indigo-700 hover:from-fuchsia-500 hover:to-purple-500 text-white font-display font-black text-lg tracking-wider shadow-2xl shadow-purple-900/40 transition transform active:scale-95 flex items-center justify-center gap-3 border-2 border-fuchsia-400">
                    <i class="fa-solid fa-gift text-xl text-amber-300" id="free-spin-icon"></i>
                    <span id="free-spin-text">SPIN GRATIS</span>
                </button>

                <!-- Tombol Spin Regular -->
                <button id="spin-btn" onclick="executeSpin()"
                    class="w-full sm:w-52 py-4 px-6 rounded-2xl bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 hover:from-amber-300 hover:to-amber-500 text-gray-950 font-display font-black text-lg tracking-wider shadow-2xl shadow-amber-500/30 transition transform active:scale-95 flex items-center justify-center gap-3 border-2 border-amber-300">
                    <i class="fa-solid fa-rotate text-xl" id="spin-icon"></i>
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
                        <td class="py-2.5 px-3">
                            @if($log->is_free_spin)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-950 border border-purple-500/50 text-purple-300">
                                    <i class="fa-solid fa-gift text-[9px] text-amber-300"></i> GRATIS (Rp 0)
                                </span>
                            @else
                                <span class="text-amber-300">Rp {{ number_format($log->bet_amount, 0, ',', '.') }}</span>
                            @endif
                        </td>
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
    let currentBalance = {{ (float) $user->balance }};
    const availableSymbols = ['⚡', '👑', '💍', '⏳', '💎', '💚', '💙', '🍷'];
    const symbolLabels   = { 
        '⚡': 'PETIR', 
        '👑': 'MAHKOTA', 
        '💍': 'CINCIN', 
        '⏳': 'JAM PASIR', 
        '🍷': 'CAWAN',
        '💎': 'PERMATA', 
        '💚': 'GIOK', 
        '💙': 'SAFIR' 
    };

    // ─── 10 Paylines Definition (0 to 14 cell indices) ───────────────────────────
    const paylinesMap = {
        1:  { name: 'Line 1 (Tengah)',     cells: [5, 6, 7, 8, 9] },
        2:  { name: 'Line 2 (Atas)',       cells: [0, 1, 2, 3, 4] },
        3:  { name: 'Line 3 (Bawah)',      cells: [10, 11, 12, 13, 14] },
        4:  { name: 'Line 4 (V-Shape)',    cells: [0, 6, 12, 8, 4] },
        5:  { name: 'Line 5 (Piramida)',   cells: [10, 6, 2, 8, 14] },
        6:  { name: 'Line 6 (Top-Zigzag)', cells: [0, 1, 7, 3, 4] },
        7:  { name: 'Line 7 (Bot-Zigzag)', cells: [10, 11, 7, 13, 14] },
        8:  { name: 'Line 8 (Puncak)',     cells: [5, 1, 2, 3, 9] },
        9:  { name: 'Line 9 (Lembah)',     cells: [5, 11, 12, 13, 9] },
        10: { name: 'Line 10 (Diagonal)',  cells: [0, 6, 7, 8, 14] }
    };

    // ─── Web Audio API Synthesizer (Zero asset loading issues, 100% reliable) ─────
    let audioCtx = null;
    let soundEnabled = true;

    function initAudio() {
        if (!audioCtx) {
            audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
    }

    function toggleSound() {
        initAudio();
        soundEnabled = !soundEnabled;
        const icon = document.getElementById('sound-icon');
        const text = document.getElementById('sound-text');
        if (soundEnabled) {
            icon.className = 'fa-solid fa-volume-high text-amber-400';
            text.innerText = 'AUDIO ON';
            playReelStopSound(true);
        } else {
            icon.className = 'fa-solid fa-volume-xmark text-gray-400';
            text.innerText = 'MUTED';
        }
    }

    function playReelTick() {
        if (!soundEnabled || !audioCtx) return;
        try {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(120 + Math.random() * 40, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.03, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.04);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.04);
        } catch(e){}
    }

    function playReelStopSound(isScatter = false) {
        if (!soundEnabled || !audioCtx) return;
        try {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            if (isScatter) {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
                osc.frequency.exponentialRampToValueAtTime(880, audioCtx.currentTime + 0.15); // A5
                gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.35);
            } else {
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(95, audioCtx.currentTime);
                gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.08);
            }
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + (isScatter ? 0.35 : 0.08));
        } catch(e){}
    }

    function playAnticipationSound() {
        if (!soundEnabled || !audioCtx) return;
        try {
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(320, audioCtx.currentTime);
            osc.frequency.linearRampToValueAtTime(750, audioCtx.currentTime + 1.2);
            gain.gain.setValueAtTime(0.05, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.2);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 1.2);
        } catch(e){}
    }

    function playWinSound(isBig = false) {
        if (!soundEnabled || !audioCtx) return;
        try {
            const notes = isBig 
                ? [261.63, 329.63, 392.00, 523.25, 659.25, 783.99, 1046.50] 
                : [329.63, 392.00, 523.25];
            notes.forEach((freq, idx) => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(freq, audioCtx.currentTime + idx * 0.1);
                gain.gain.setValueAtTime(0.08, audioCtx.currentTime + idx * 0.1);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + idx * 0.1 + 0.3);
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.start(audioCtx.currentTime + idx * 0.1);
                osc.stop(audioCtx.currentTime + idx * 0.1 + 0.3);
            });
        } catch(e){}
    }

    function playThunderSound() {
        if (!soundEnabled || !audioCtx) return;
        try {
            const bufferSize = audioCtx.sampleRate * 1.5;
            const buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
            const output = buffer.getChannelData(0);
            for (let i = 0; i < bufferSize; i++) {
                output[i] = Math.random() * 2 - 1;
            }
            const whiteNoise = audioCtx.createBufferSource();
            whiteNoise.buffer = buffer;
            const filter = audioCtx.createBiquadFilter();
            filter.type = 'lowpass';
            filter.frequency.setValueAtTime(800, audioCtx.currentTime);
            filter.frequency.exponentialRampToValueAtTime(60, audioCtx.currentTime + 1.4);
            const gain = audioCtx.createGain();
            gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.5);
            whiteNoise.connect(filter);
            filter.connect(gain);
            gain.connect(audioCtx.destination);
            whiteNoise.start();
            whiteNoise.stop(audioCtx.currentTime + 1.5);
        } catch(e){}
    }

    // ─── Modal Teleportation & Global Key Handlers ───────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        const backdrop = document.getElementById('win-popup-backdrop');
        const toast    = document.getElementById('small-win-toast');
        const depModal = document.getElementById('deposit-modal-backdrop');
        const payModal = document.getElementById('paytable-modal-backdrop');
        if (backdrop) document.body.appendChild(backdrop);
        if (toast)    document.body.appendChild(toast);
        if (depModal) document.body.appendChild(depModal);
        if (payModal) document.body.appendChild(payModal);
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeWinPopup();
            closeDepositModal();
            closePaytableModal();
        }
    });

    // ─── Paytable Modal Handlers ────────────────────────────────────────────────
    function openPaytableModal() {
        initAudio();
        const modal = document.getElementById('paytable-modal-backdrop');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closePaytableModal() {
        const modal = document.getElementById('paytable-modal-backdrop');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // Preview specific payline by clicking tag on left/right
    function previewPayline(lineNum) {
        initAudio();
        playReelStopSound(false);
        const line = paylinesMap[lineNum];
        if (!line) return;

        // Highlight cells temporarily
        for (let i = 0; i < 15; i++) {
            document.getElementById(`slot-cell-${i}`).classList.remove('payline-winner');
        }
        document.querySelectorAll('.payline-tag').forEach(t => t.classList.remove('active'));
        const tag = document.getElementById(`payline-tag-${lineNum}`);
        if (tag) tag.classList.add('active');

        line.cells.forEach(idx => {
            const c = document.getElementById(`slot-cell-${idx}`);
            if (c) c.classList.add('payline-winner');
        });

        const ticker = document.getElementById('payline-ticker');
        const text = document.getElementById('payline-ticker-text');
        ticker.style.display = 'block';
        text.innerText = `🔍 PREVIEW: ${line.name}`;

        setTimeout(() => {
            line.cells.forEach(idx => {
                const c = document.getElementById(`slot-cell-${idx}`);
                if (c && !isSpinning) c.classList.remove('payline-winner');
            });
            if (tag && !isSpinning) tag.classList.remove('active');
            if (ticker && !isSpinning) ticker.style.display = 'none';
        }, 1600);
    }

    // ─── Deposit Modal Handlers ──────────────────────────────────────────────────
    function openDepositModal() {
        initAudio();
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
        if (isSpinning) return;
        document.getElementById('bet-input').value = amount;
        document.getElementById('meter-bet').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    }

    // ─── Spin Buttons ────────────────────────────────────────────────────────────
    async function executeFreeSpin() {
        if (isSpinning) return;
        initAudio();
        const betAmount = parseFloat(document.getElementById('bet-input').value) || 1000;
        await spinCore(betAmount, true);
    }

    async function executeSpin() {
        if (isSpinning) return false;
        initAudio();

        const betAmount = parseFloat(document.getElementById('bet-input').value);
        if (!betAmount || betAmount < 1000) { alert('Nominal taruhan minimal adalah Rp 1.000!'); return false; }

        if (currentBalance < betAmount) {
            alert('Saldo virtual Anda tidak mencukupi untuk melakukan bet ini!');
            return false;
        }

        return await spinCore(betAmount, false);
    }

    // ─── Core Spin Logic ────────────────────────────────────────────────────────
    async function spinCore(betAmount, isFreeSpin) {
        isSpinning = true;
        resetUI(isFreeSpin);

        // Update Bet meter
        document.getElementById('meter-bet').innerText = isFreeSpin ? 'Rp 0 (GRATIS)' : ('Rp ' + new Intl.NumberFormat('id-ID').format(betAmount));
        document.getElementById('meter-win').innerText = 'Rp 0';

        // Phase 1: Start reel rolling
        const spinIntervals = startReelRolling(isFreeSpin);

        // Phase 2: Fetch backend result
        let data;
        try {
            data = await fetchSpin(betAmount, isFreeSpin);
        } catch(err) {
            stopReelRolling(spinIntervals);
            alert(err.message || err.error || 'Terjadi kesalahan pada server.');
            unlockUI();
            return false;
        }

        // Phase 3: Wait minimum 1.8s of rolling animation
        await sleep(1800);
        stopReelRolling(spinIntervals);

        // Phase 4: Land symbols column by column with Anticipation Suspense (bypass suspense on free spin)
        await landSymbolsWithAnticipation(data.grid, data.near_miss, isFreeSpin);

        // Phase 5: Check winning paylines or near-miss (HANYA jika bukan spin gratis)
        if (!isFreeSpin && data.winning_lines && data.winning_lines.length > 0) {
            // Animate win meter & highlight winning paylines
            highlightWinningPaylines(data.winning_lines);
            animateCountUp('meter-win', 0, data.win_amount, 800);
            playWinSound(data.win_amount > betAmount * 5);
        }

        // Show near-miss banner if triggered (hanya saat bukan free spin)
        if (!isFreeSpin && data.near_miss && data.near_miss_detail) {
            showNearMissBanner(data.near_miss_detail);
        }

        // Phase 6: Post-land sequence (Abaikan animasi kemenangan dan animasi kekalahan pada spin gratis)
        if (isFreeSpin) {
            // Spin Gratis: Langsung selesai tanpa animasi kemenangan / kekalahan
            await sleep(300);
        } else if (data.status_manipulasi === 'lose' || data.status_manipulasi.includes('Rungkad')) {
            await playLoseSequence();
        } else if (data.status_manipulasi.includes('win') || data.status_manipulasi === 'win') {
            await playJackpotSequence(data);
        } else {
            await playLowWinSequence(data);
        }

        // Phase 7: Update balance & history log
        updateBalance(data.new_balance);
        renderResultCard(data);
        addHistoryRow(data);

        unlockUI();
        return true;
    }

    // ─── API Call ────────────────────────────────────────────────────────────────
    function fetchSpin(betAmount, isFreeSpin = false) {
        return fetch("{{ route('api.spin') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ bet_amount: betAmount, is_free_spin: isFreeSpin })
        }).then(res => {
            if (!res.ok) return res.json().then(e => { throw e; });
            return res.json();
        });
    }

    // ─── Phase 1: Rolling Animation ─────────────────────────────────────────────
    function startReelRolling(isFreeSpin = false) {
        const gameBadge = document.getElementById('game-status-badge');
        if (isFreeSpin) {
            gameBadge.innerText = '⚡ SPIN GRATIS AKTIF... ⚡';
            gameBadge.className = 'text-xs px-3 py-1 rounded-full font-bold bg-fuchsia-950 border border-fuchsia-400 text-fuchsia-200 animate-pulse';
        } else {
            gameBadge.innerText = 'SPINNING REELS...';
            gameBadge.className = 'text-xs px-3 py-1 rounded-full font-bold bg-amber-900/80 border border-amber-500 text-amber-300 animate-pulse';
        }

        document.getElementById('result-card').classList.add('hidden');
        document.getElementById('payline-ticker').style.display = 'none';
        document.getElementById('near-miss-banner').style.display = 'none';
        document.querySelectorAll('.payline-tag').forEach(t => t.classList.remove('active'));
        hideJackpotBanner();

        const intervals = [];
        for (let i = 0; i < 15; i++) {
            const cell = document.getElementById(`slot-cell-${i}`);
            cell.classList.add('spinning');
            cell.classList.remove('matched', 'shattering', 'cascade-in', 'landing', 'anticipation', 'payline-winner');

            const col = i % 5;
            const delay = col * 50;

            let iv = null;
            setTimeout(() => {
                iv = setInterval(() => {
                    const s = availableSymbols[Math.floor(Math.random() * availableSymbols.length)];
                    cell.querySelector('.symbol-icon').innerText = s;
                    cell.querySelector('.symbol-label').innerText = symbolLabels[s] || 'OLYMPUS';
                    if (Math.random() < 0.15) playReelTick();
                }, 65 + col * 10);
                intervals.push(iv);
            }, delay);

            intervals.push(null);
        }
        return intervals;
    }

    function stopReelRolling(intervals) {
        intervals.forEach(iv => { if (iv) clearInterval(iv); });
        for (let i = 0; i < 15; i++) {
            document.getElementById(`slot-cell-${i}`).classList.remove('spinning');
        }
    }

    // ─── Phase 4: Land Symbols with Authentic Anticipation Tension ───────────────
    async function landSymbolsWithAnticipation(grid, isNearMiss = false, isFreeSpin = false) {
        const parsed = grid.map(text => {
            if (text.includes('ZEUS'))      return { icon: '⚡', label: text };
            if (text.includes('MAHKOTA'))   return { icon: '👑', label: 'MAHKOTA' };
            if (text.includes('CINCIN'))    return { icon: '💍', label: 'CINCIN' };
            if (text.includes('JAM PASIR')) return { icon: '⏳', label: 'JAM PASIR' };
            if (text.includes('CAWAN'))     return { icon: '🍷', label: 'CAWAN' };
            return { icon: text.split(' ')[0] || text, label: symbolLabels[text.split(' ')[0]] || 'OLYMPUS' };
        });

        let landedScatters = 0;

        for (let col = 0; col < 5; col++) {
            // Check if we need Anticipation on Reel 4 & 5 (col 3 & 4) — Lewati pada spin gratis
            if (!isFreeSpin && col === 3 && landedScatters >= 2) {
                // Suspense Anticipation Spin!
                const gameBadge = document.getElementById('game-status-badge');
                gameBadge.innerText = '⚡ ANTICIPATION: BUTUH 1 SCATTER LAGI! ⚡';
                gameBadge.className = 'text-xs px-3 py-1 rounded-full font-bold bg-amber-500 text-gray-950 animate-bounce shadow-lg shadow-amber-500/50';

                // Golden glow on reels 4 & 5
                for (let r = 0; r < 3; r++) {
                    document.getElementById(`slot-cell-${r * 5 + 3}`).classList.add('anticipation');
                    document.getElementById(`slot-cell-${r * 5 + 4}`).classList.add('anticipation');
                }

                playAnticipationSound();
                await sleep(1100);

                for (let r = 0; r < 3; r++) {
                    document.getElementById(`slot-cell-${r * 5 + 3}`).classList.remove('anticipation');
                    document.getElementById(`slot-cell-${r * 5 + 4}`).classList.remove('anticipation');
                }
            }

            // Land this column (3 rows)
            let colHasScatter = false;
            for (let row = 0; row < 3; row++) {
                const idx = row * 5 + col;
                const cell = document.getElementById(`slot-cell-${idx}`);
                cell.querySelector('.symbol-icon').innerText = parsed[idx].icon;
                cell.querySelector('.symbol-label').innerText = parsed[idx].label;
                cell.classList.remove('spinning');
                cell.classList.add('landing');
                setTimeout(() => cell.classList.remove('landing'), 350);

                if (parsed[idx].icon === '⚡') {
                    colHasScatter = true;
                    landedScatters++;
                }
            }

            playReelStopSound(isFreeSpin ? false : colHasScatter);
            await sleep(120);
        }
        await sleep(250);
    }

    // ─── Winning Paylines Highlighting ──────────────────────────────────────────
    function highlightWinningPaylines(winningLines) {
        if (!winningLines || winningLines.length === 0) return;

        const ticker = document.getElementById('payline-ticker');
        const text = document.getElementById('payline-ticker-text');
        ticker.style.display = 'block';

        let lineMessages = [];

        winningLines.forEach(wl => {
            // Activate Payline side tag
            const tag = document.getElementById(`payline-tag-${wl.line_number}`);
            if (tag) tag.classList.add('active');

            // Highlight matched cells
            if (wl.indices) {
                wl.indices.forEach(idx => {
                    const c = document.getElementById(`slot-cell-${idx}`);
                    if (c) c.classList.add('payline-winner');
                });
            }

            lineMessages.push(`${wl.name}: ${wl.count}x ${wl.symbol} (+Rp ${new Intl.NumberFormat('id-ID').format(wl.win_amount)})`);
        });

        text.innerText = '🎉 ' + lineMessages.join(' | ');
    }

    function showNearMissBanner(detail) {
        const banner = document.getElementById('near-miss-banner');
        const text = document.getElementById('near-miss-text');
        if (banner && text) {
            text.innerText = detail;
            banner.style.display = 'flex';
        }
    }

    // ─── Phase 5a: Jackpot Sequence (WIN) ───────────────────────────────────────
    async function playJackpotSequence(data) {
        showJackpotBanner();
        triggerScreenFlash('zeus');
        triggerScreenShake();
        playThunderSound();

        const matchedIndices = [];
        for (let i = 0; i < 15; i++) {
            const icon = document.getElementById(`slot-cell-${i}`).querySelector('.symbol-icon').innerText;
            if (icon === '⚡' || icon === '👑' || icon === '⏳') matchedIndices.push(i);
        }
        if (matchedIndices.length === 0) {
            for (let i = 0; i < 15; i++) matchedIndices.push(i);
        }

        matchedIndices.forEach(i => {
            const cell = document.getElementById(`slot-cell-${i}`);
            cell.classList.add('matched');
        });

        for (let k = 0; k < matchedIndices.length; k++) {
            await sleep(130);
            spawnLightningOnCell(matchedIndices[k]);
            spawnParticles(matchedIndices[k], ['⚡', '✨', '💫', '🌟', '⭐'], 8);
        }

        await sleep(500);

        matchedIndices.forEach(i => spawnMultiplierPopup(i, data.multiplier));

        await sleep(400);

        for (let k = 0; k < matchedIndices.length; k++) {
            await sleep(50);
            shatterCell(matchedIndices[k]);
        }
        await sleep(500);

        rainCoins(25);

        await sleep(300);
        await cascadeNewSymbols(matchedIndices);

        await sleep(400);
        showJackpotPopup(data);
    }

    // ─── Phase 5b: Low Win (Pancingan) ──────────────────────────────────────────
    async function playLowWinSequence(data) {
        const matchedIndices = [];
        for (let i = 0; i < 15; i++) {
            const cell = document.getElementById(`slot-cell-${i}`);
            if (cell.classList.contains('payline-winner')) {
                matchedIndices.push(i);
            }
        }
        if (matchedIndices.length === 0) {
            matchedIndices.push(5, 6, 7);
        }

        for (let k = 0; k < matchedIndices.length; k++) {
            await sleep(100);
            spawnParticles(matchedIndices[k], ['✨', '💰', '🌟'], 5);
            spawnMultiplierPopup(matchedIndices[k], data.multiplier);
        }

        await sleep(500);
        showLowWinToast(data);
    }

    // ─── Phase 5c: Lose (Rungkad) ───────────────────────────────────────────────
    async function playLoseSequence() {
        triggerScreenFlash('lose');

        for (let i = 0; i < 15; i++) {
            const cell = document.getElementById(`slot-cell-${i}`);
            cell.style.animation = 'reelShake 0.08s 5 alternate';
            setTimeout(() => { cell.style.animation = ''; }, 500);
        }

        await sleep(300);
        spawnParticles(7, ['💀', '😭', '🔥', '💸'], 8);
        await sleep(350);
    }

    // ─── WIN POPUP & TOAST ──────────────────────────────────────────────────────
    function showJackpotPopup(data) {
        const modal    = document.getElementById('win-popup-modal');
        const backdrop = document.getElementById('win-popup-backdrop');

        modal.className = 'jackpot';
        document.getElementById('popup-zeus-icon').className = 'popup-zeus-icon';
        document.getElementById('popup-zeus-icon').innerText = '⚡';
        document.getElementById('popup-subtitle').innerText  = 'ZEUS JACKPOT — DIKONTROL BANDAR';
        document.getElementById('popup-multiplier-text').innerText = `x${data.multiplier} Perkalian Zeus`;
        document.getElementById('popup-manipulation-label').innerText = data.status_label;
        document.getElementById('popup-close-btn').querySelector('span').innerText = 'TUTUP & PUTAR LAGI';

        document.getElementById('popup-stat-bet').innerText     = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.bet_amount);
        document.getElementById('popup-stat-payout').innerText  = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.win_amount);
        document.getElementById('popup-stat-balance').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.new_balance);

        animateCountUp('popup-win-amount', 0, data.win_amount, 1200);

        backdrop.classList.add('open');
        launchPopupConfetti(30);
    }

    function showLowWinToast(data) {
        const toast = document.getElementById('small-win-toast');
        document.getElementById('toast-icon').innerText       = '💎';
        document.getElementById('toast-amount').innerText     = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.win_amount);
        document.getElementById('toast-multiplier').innerText = `x${data.multiplier} Perkalian`;
        document.getElementById('toast-subtitle').innerText   = 'MENANG KECIL — PANCINGAN BANDAR';

        toast.classList.add('show');
        setTimeout(() => { toast.classList.remove('show'); }, 4000);
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
        if (!el) return;
        const start = performance.now();
        const fmt   = v => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(v));

        function step(now) {
            const elapsed  = now - start;
            const progress = Math.min(elapsed / durationMs, 1);
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
        void overlay.offsetWidth;
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
        const icon = cell.querySelector('.symbol-icon').innerText;
        spawnParticles(idx, [icon, '💥', '✨'], 6);

        cell.classList.remove('matched');
        cell.classList.add('shattering');
        setTimeout(() => {
            cell.classList.remove('shattering');
            cell.style.opacity = '0';
            cell.querySelector('.symbol-icon').innerText = '　';
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
    function resetUI(isFreeSpin = false) {
        const spinBtn  = document.getElementById('spin-btn');
        const spinIcon = document.getElementById('spin-icon');
        const spinText = document.getElementById('spin-text');
        const freeBtn  = document.getElementById('free-spin-btn');
        const freeText = document.getElementById('free-spin-text');

        if (isFreeSpin) {
            if (freeBtn) {
                freeBtn.disabled = true;
                freeBtn.classList.add('opacity-75', 'cursor-not-allowed');
                if (freeText) freeText.innerText = 'MEMUTAR...';
            }
            if (spinBtn) {
                spinBtn.disabled = true;
                spinBtn.classList.add('opacity-75', 'cursor-not-allowed');
                spinIcon.classList.add('fa-spin');
            }
        } else {
            if (spinBtn) {
                spinBtn.disabled = true;
                spinBtn.classList.add('opacity-75', 'cursor-not-allowed');
                spinIcon.classList.add('fa-spin');
                if (spinText) spinText.innerText = 'MEMUTAR REEL...';
            }
            if (freeBtn) {
                freeBtn.disabled = true;
                freeBtn.classList.add('opacity-75', 'cursor-not-allowed');
            }
        }

        for (let i = 0; i < 15; i++) {
            const c = document.getElementById(`slot-cell-${i}`);
            c.classList.remove('matched', 'shattering', 'cascade-in', 'landing', 'payline-winner', 'anticipation');
            c.style.opacity = '1';
            c.style.animation = '';
            c.style.borderColor = '';
            c.style.boxShadow = '';
        }
    }

    function unlockUI() {
        isSpinning = false;
        const spinBtn   = document.getElementById('spin-btn');
        const spinIcon  = document.getElementById('spin-icon');
        const spinText  = document.getElementById('spin-text');
        const freeBtn   = document.getElementById('free-spin-btn');
        const freeText  = document.getElementById('free-spin-text');
        const gameBadge = document.getElementById('game-status-badge');

        if (spinBtn) {
            spinBtn.disabled = false;
            spinBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            spinIcon.classList.remove('fa-spin');
            if (spinText) spinText.innerText = 'PUTAR (SPIN)';
        }

        if (freeBtn) {
            freeBtn.disabled = false;
            freeBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            if (freeText) freeText.innerText = 'SPIN GRATIS';
        }

        if (gameBadge) {
            gameBadge.innerText = 'READY TO SPIN';
            gameBadge.className = 'text-xs px-3 py-1 rounded-full font-bold bg-indigo-900/80 border border-indigo-700 text-indigo-300';
        }
    }

    function updateBalance(newBalance) {
        currentBalance = parseFloat(newBalance);
        const fmt = 'Rp ' + new Intl.NumberFormat('id-ID').format(currentBalance);
        document.getElementById('player-balance').innerText = fmt;
        document.getElementById('meter-balance').innerText  = fmt;
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

        if (data.is_free_spin) {
            card.className   = 'glass-card rounded-2xl p-6 border border-purple-500/50 bg-purple-950/40 shadow-2xl';
            iconBg.className = 'w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 bg-purple-900/80 text-purple-300 border border-purple-500/40';
            icon.className   = 'fa-solid fa-gift text-purple-300';
            title.className  = 'font-display font-black text-xl text-purple-300';
            title.innerText  = 'SPIN GRATIS (SIMULASI ACAK)';
            badge.className  = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-purple-950 text-purple-300 border border-purple-500/50';
            badge.innerText  = 'SPIN GRATIS';
        } else if (data.status_manipulasi === 'lose' || data.status_manipulasi.includes('Rungkad')) {
            card.className   = 'glass-card rounded-2xl p-6 border border-red-500/50 bg-red-950/40 shadow-2xl';
            iconBg.className = 'w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 bg-red-900/80 text-red-300 border border-red-500/40';
            icon.className   = 'fa-solid fa-skull-crossbones';
            title.className  = 'font-display font-black text-xl text-red-400';
            title.innerText  = 'RUNGKAD! (HASIL DI-SETTING BANDAR)';
            badge.className  = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-red-950 text-red-300 border border-red-500/50';
            badge.innerText  = 'ZONK / KALAH';
        } else if (data.status_manipulasi === 'win' || data.status_manipulasi.includes('win')) {
            card.className   = 'glass-card rounded-2xl p-6 border border-emerald-500/50 bg-emerald-950/40 shadow-2xl zeus-lightning';
            iconBg.className = 'w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 bg-emerald-900/80 text-emerald-300 border border-emerald-500/40';
            icon.className   = 'fa-solid fa-bolt-lightning text-amber-400';
            title.className  = 'font-display font-black text-xl text-emerald-400';
            title.innerText  = 'JACKPOT! (HASIL DI-SETTING BANDAR)';
            badge.className  = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-950 text-emerald-300 border border-emerald-500/50';
            badge.innerText  = 'MENANG BESAR';
        } else {
            card.className   = 'glass-card rounded-2xl p-6 border border-amber-500/50 bg-amber-950/40 shadow-2xl';
            iconBg.className = 'w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 bg-amber-900/80 text-amber-300 border border-amber-500/40';
            icon.className   = 'fa-solid fa-fish';
            title.className  = 'font-display font-black text-xl text-amber-400';
            title.innerText  = 'MENANG KECIL (PANCINGAN BANDAR)';
            badge.className  = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-amber-950 text-amber-300 border border-amber-500/50';
            badge.innerText  = 'PANCINGAN';
        }
    }

    // ─── History Row ─────────────────────────────────────────────────────────────
    function addHistoryRow(data) {
        const tbody   = document.getElementById('player-history-body');
        const emptyRow = document.getElementById('empty-history-row');
        if (emptyRow) emptyRow.remove();

        const now = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const isRungkad = data.status_manipulasi === 'lose' || data.status_manipulasi.includes('Rungkad');
        const badgeClass  = data.is_free_spin ? 'bg-purple-950 text-purple-300 border border-purple-500/40' : (isRungkad ? 'bg-red-950 text-red-300' : 'bg-emerald-950 text-emerald-300');
        const payoutClass = data.is_free_spin ? 'text-gray-400' : (data.win_amount > 0 ? 'text-emerald-400' : 'text-red-400');

        const betDisplay = data.is_free_spin 
            ? '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-950 border border-purple-500/50 text-purple-300"><i class="fa-solid fa-gift text-[9px] text-amber-300"></i> GRATIS (Rp 0)</span>'
            : `<span class="text-amber-300">Rp ${new Intl.NumberFormat('id-ID').format(data.bet_amount)}</span>`;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="py-2.5 px-3 font-mono text-indigo-400">${now}</td>
            <td class="py-2.5 px-3">${betDisplay}</td>
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
