@extends('layouts.app')

@section('title', 'Login - Simulasi Slot Edukasi Manipulasi Bandar')

@section('content')
<div class="max-w-md mx-auto my-8">
    
    <!-- Hero Banner Card -->
    <div class="text-center mb-8">
        <div class="inline-flex p-4 rounded-2xl bg-gradient-to-b from-purple-600/30 to-amber-500/10 border border-amber-500/30 mb-4 shadow-xl shadow-purple-900/40">
            <span class="text-5xl">⚡👑</span>
        </div>
        <h2 class="font-display font-black text-3xl gold-gradient-text">GATES OF OLYMPUS</h2>
        <p class="text-indigo-300 text-sm mt-1">Simulasi Edukasi: Membongkar Rahasia Manipulasi Bandar</p>
    </div>

    <!-- Login Form Container -->
    <div class="glass-card rounded-2xl p-6 sm:p-8 shadow-2xl gold-border relative overflow-hidden">
        <div class="absolute -top-12 -right-12 w-32 h-32 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <h3 class="font-display font-bold text-xl text-white mb-6 flex items-center gap-2">
            <i class="fa-solid fa-lock text-amber-400"></i>
            Masuk ke Sistem Simulasi
        </h3>

        @if($errors->any())
            <div class="mb-5 bg-red-950/80 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl text-sm">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-2"><i class="fa-solid fa-circle-exclamation text-red-400"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">Alamat Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-400">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="Masukkan alamat email"
                        class="w-full bg-indigo-950/60 border border-indigo-800 text-white placeholder-indigo-400/60 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-indigo-400">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <input type="password" name="password" id="password" value="" required placeholder="Masukkan password"
                        class="w-full bg-indigo-950/60 border border-indigo-800 text-white placeholder-indigo-400/60 rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:border-amber-400 transition text-sm">
                </div>
            </div>

            <button type="submit" 
                class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-gray-950 font-black font-display text-base tracking-wide transition shadow-lg shadow-amber-500/20 active:scale-[0.98]">
                MASUK SEKARANG
            </button>
        </form>

        <!-- Menu / Link Register User Baru -->
        <div class="mt-6 pt-5 border-t border-indigo-900/60 text-center">
            <p class="text-xs text-indigo-300 mb-2">Belum memiliki akun pemain?</p>
            <a href="{{ route('register') }}" 
                class="w-full py-2.5 px-4 rounded-xl bg-indigo-900/50 hover:bg-indigo-800/60 border border-indigo-700/60 text-amber-400 hover:text-amber-300 font-bold text-sm tracking-wide transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-user-plus text-amber-400"></i>
                Daftar User Baru (Saldo Awal Rp 100.000)
            </a>
        </div>

    </div>

    <!-- Educational Warning Notice -->
    <div class="mt-6 bg-amber-950/30 border border-amber-500/30 p-4 rounded-xl text-xs text-amber-200/90 leading-relaxed flex items-start gap-3">
        <i class="fa-solid fa-triangle-exclamation text-amber-400 text-base mt-0.5"></i>
        <div>
            <strong class="font-bold text-amber-300 block mb-1">Peringatan Tujuan Pembelajaran:</strong>
            Aplikasi ini dibuat murni untuk mengedukasi siswa bahwa sistem perjudian online dikendalikan sepenuhnya oleh pemilik server. Pemain (siswa) tidak akan pernah menang secara konsisten dalam jangka panjang.
        </div>
    </div>
</div>
@endsection
