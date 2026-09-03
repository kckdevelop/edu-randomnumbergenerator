<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduSlot - Simulasi Gates of Olympus & Manipulasi Bandar')</title>
    
    <!-- Google Fonts: Outfit & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Gates of Olympus Theme Styles -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        olympus: {
                            dark: '#0B0A1A',
                            card: '#16152B',
                            purple: '#6D28D9',
                            gold: '#F59E0B',
                            goldHover: '#D97706',
                            accent: '#EC4899',
                            red: '#EF4444',
                            green: '#10B981',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            background: radial-gradient(circle at top center, #1e1035 0%, #0b0a1a 70%);
            color: #f3f4f6;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        .gold-gradient-text {
            background: linear-gradient(135deg, #FDE68A 0%, #F59E0B 50%, #B45309 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gold-border {
            border: 1px solid rgba(245, 158, 11, 0.3);
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.15);
        }

        .glass-card {
            background: rgba(22, 21, 43, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Slot Reel base styles (detailed animations overridden per-page) */
        .slot-box {
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .zeus-lightning {
            animation: lightningFlash 0.6s ease-in-out infinite alternate;
        }

        @keyframes lightningFlash {
            0% { box-shadow: 0 0 10px #F59E0B, 0 0 30px #F59E0B; }
            100% { box-shadow: 0 0 25px #FBBF24, 0 0 60px #F59E0B; }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0b0a1a;
        }
        ::-webkit-scrollbar-thumb {
            background: #3730a3;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #4f46e5;
        }
    </style>

    @stack('styles')
</head>
<body class="flex flex-col min-h-screen">

    <!-- Header Navbar -->
    <header class="border-b border-indigo-900/50 bg-olympus-dark/80 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-purple-600 flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <i class="fa-solid fa-bolt-lightning text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="font-display font-black text-xl tracking-wide gold-gradient-text">EDU-SLOT SIMULATOR</h1>
                    <p class="text-xs text-indigo-300 font-medium">Platform Edukasi Manipulasi Judi Online</p>
                </div>
            </div>

            @auth
            <div class="flex items-center space-x-4">
                <div class="hidden md:flex items-center space-x-2 bg-indigo-950/60 border border-indigo-800/50 px-3 py-1.5 rounded-lg text-sm">
                    <i class="fa-solid fa-user-shield text-amber-400"></i>
                    <span class="text-gray-300 font-medium">{{ Auth::user()->name }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ Auth::user()->isAdmin() ? 'bg-purple-600/80 text-purple-200' : 'bg-emerald-600/80 text-emerald-200' }}">
                        {{ strtoupper(Auth::user()->role) }}
                    </span>
                </div>

                @if(!Auth::user()->isAdmin())
                <div class="bg-amber-500/10 border border-amber-500/30 px-3 py-1.5 rounded-lg flex items-center space-x-2">
                    <i class="fa-solid fa-wallet text-amber-400"></i>
                    <span class="text-xs text-amber-200 font-semibold">Saldo:</span>
                    <span id="nav-user-balance" class="text-sm font-black font-display text-amber-400">
                        Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}
                    </span>
                </div>
                @endif

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-500/20 hover:bg-red-500/40 border border-red-500/30 text-red-300 px-3 py-1.5 rounded-lg text-sm transition flex items-center space-x-1.5 font-medium">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @if(session('success'))
            <div class="mb-4 bg-emerald-950/80 border border-emerald-500/50 text-emerald-200 px-4 py-3 rounded-xl flex items-center justify-between shadow-lg">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-950/80 border border-red-500/50 text-red-200 px-4 py-3 rounded-xl flex items-center justify-between shadow-lg">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-circle-exclamation text-red-400 text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-200"><i class="fa-solid fa-xmark"></i></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-indigo-900/40 bg-olympus-dark/90 py-4 text-center text-xs text-indigo-400">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                &copy; {{ date('Y') }} Simulasi Edukasi Judi Online - Gates of Olympus Demo.
            </div>
            <div class="text-amber-400/80 font-medium">
                <i class="fa-solid fa-triangle-exclamation text-amber-400 mr-1"></i>
                Dibuat Khusus Untuk Tujuan Pembelajaran & Bukti Manipulasi Backend
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
