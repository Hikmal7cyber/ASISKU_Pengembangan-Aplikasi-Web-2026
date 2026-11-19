<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ASISKU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center relative bg-slate-900">

    <!-- Background Gedung FILKOM UB dengan overlay gelap -->
    <div class="absolute inset-0 bg-cover bg-center z-0" 
         style="background-image: url('{{ asset('images/bg-filkom.jpg') }}');">
        <div class="absolute inset-0 bg-slate-900/55 backdrop-blur-[1px]"></div>
    </div>

    <!-- Kontainer Utama -->
    <div class="relative z-10 w-full max-w-md px-4 flex flex-col items-center">
        
        <!-- Kartu Putih Form Login -->
        <div class="w-full bg-white rounded-3xl shadow-2xl px-8 py-10">
            
            <!-- Logo ASISKU -->
            <div class="flex flex-col items-center mb-7">
                <img src="{{ asset('images/logo-asisku.png') }}" alt="Logo ASISKU" class="h-24 object-contain">
            </div>

            <!-- Pesan Error jika Login Gagal -->
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-xs font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Form Login -->
            <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="username" class="block text-xs font-semibold text-slate-700 mb-1.5">Username</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#08244B] focus:ring-1 focus:ring-[#08244B] transition">
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-800 focus:outline-none focus:border-[#08244B] focus:ring-1 focus:ring-[#08244B] transition">
                </div>

                <div class="pt-2 flex justify-center">
                    <button type="submit"
                        class="w-44 py-2.5 rounded-xl bg-[#08244B] hover:bg-[#0d3366] text-white text-xs font-bold tracking-wider uppercase shadow-md transition cursor-pointer">
                        LOGIN
                    </button>
                </div>

                <div class="pt-1">
                    <a href="#" onclick="alert('Silakan hubungi Koordinator Asprak atau Admin Lab FILKOM untuk reset password.'); return false;"
                       class="text-xs font-bold text-[#08244B] hover:underline">
                        Forgot Password?
                    </a>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <p class="mt-5 text-center text-xs text-white/90 font-medium drop-shadow">
            2026 Pengembangan Aplikasi Web - Muhammad Hikmal Al-Ghifary
        </p>
    </div>

</body>
</html>