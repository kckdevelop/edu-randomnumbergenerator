@extends('layouts.app')

@section('title', 'Daftar User Baru - Simulasi Slot Edukasi')

@section('content')
<div class="max-w-md mx-auto my-8">
    
    <!-- Hero Banner Card -->
    <div class="text-center mb-8">
        <div class="inline-flex p-4 rounded-2xl bg-gradient-to-b from-purple-600/30 to-amber-500/10 border border-amber-500/30 mb-4 shadow-xl shadow-purple-900/40">
            <span class="text-5xl">⚡✨</span>
        </div>
        <h2 class="font-display font-black text-3xl gold-gradient-text">DAFTAR USER BARU</h2>
        <p class="text-indigo-300 text-sm mt-1">Dapatkan Saldo Awal Rp 100.000 Untuk Simulasi Edukasi</p>
    </div>

    <!-- Register Form Container -->
    <div class="glass-card rounded-2xl p-6 sm:p-8 shadow-2xl gold-border relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between mb-6">
            <h3 class="font-display font-bold text-xl text-white flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-amber-400"></i>
                Formulir Pendaftaran
            </h3>
            <span class="bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-semibold px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                <i class="fa-solid fa-coins text-emerald-400"></i>
                Rp 100.000
            </span>
        </div>

        @if($errors->any())
            <div class="mb-5 bg-red-950/80 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-2"><i class="fa-solid fa-circle-exclamation text-red-400"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-400">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso"
                        class="w-full bg-indigo-950/60 border border-indigo-800 text-white placeholder-indigo-400/60 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                </div>
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">Alamat Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-400">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="email@sekolah.id"
                        class="w-full bg-indigo-950/60 border border-indigo-800 text-white placeholder-indigo-400/60 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-400">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <input type="password" name="password" id="password" required placeholder="Minimal 6 karakter"
                        class="w-full bg-indigo-950/60 border border-indigo-800 text-white placeholder-indigo-400/60 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-400">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Ulangi password"
                        class="w-full bg-indigo-950/60 border border-indigo-800 text-white placeholder-indigo-400/60 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-gray-950 font-black font-display text-base tracking-wide transition shadow-lg shadow-amber-500/20 active:scale-[0.98]">
                    DAFTAR & DAPATKAN SALDO
                </button>
            </div>
        </form>

        <div class="mt-6 pt-5 border-t border-indigo-900/60 text-center">
            <p class="text-xs text-indigo-300">
                Sudah memiliki akun?
                <a href="{{ route('login') }}" class="font-bold text-amber-400 hover:text-amber-300 hover:underline transition ml-1">
                    Masuk ke Sistem
                </a>
            </p>
        </div>

    </div>

    <!-- Saldo Info Card -->
    <div class="mt-6 bg-emerald-950/30 border border-emerald-500/30 p-4 rounded-xl text-xs text-emerald-200/90 leading-relaxed flex items-start gap-3">
        <i class="fa-solid fa-gift text-emerald-400 text-base mt-0.5"></i>
        <div>
            <strong class="font-bold text-emerald-300 block mb-1">Ketentuan Saldo Awal Simulasi:</strong>
            Setiap pendaftar akun baru otomatis mendapatkan saldo virtual simulasi sebesar <strong>Rp 100.000</strong> untuk mencoba simulasi permainan slot edukasi.
        </div>
    </div>
</div>
@endsection
