<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="404 - Page Not Found">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('code') - @yield('message')</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon"
        href="{{ $logoPerusahaan === 'favicon.ico' ? asset('favicon.ico') : asset('storage/' . $logoPerusahaan) }}" />

    {{-- Menggunakan Vite untuk build assets (Tailwind CSS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    {{-- Font Awesome for social icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* Custom style for the 404 glitch effect */
        .glitch-text::after {
            content: '@yield('code')';
            position: absolute;
            left: 5px;
            top: 5px;
            color: #22d3ee;
            z-index: -1;
            filter: blur(1.5px);
        }
    </style>
</head>

<body
    class="bg-white text-black font-custom flex items-center justify-center min-h-screen p-4 relative overflow-hidden">

    <div class="absolute top-1/4 left-[15%] h-24 w-0.5 bg-cyan-400"></div>
    <div class="absolute bottom-1/4 left-[18%] h-32 w-0.5 bg-cyan-400"></div>
    <div class="absolute top-10 right-[12%] h-28 w-0.5 bg-cyan-400"></div>
    <div class="absolute bottom-1/2 right-[20%] h-40 w-0.5 bg-cyan-400"></div>

    <main class="w-full max-w-lg mx-auto text-center z-10">

        {{-- Kode Error dengan efek Glitch --}}
        <h1 class="text-9xl font-extrabold text-black tracking-tighter relative inline-block glitch-text">
            @yield('code')
        </h1>

        {{-- Judul Error --}}
        <h2 class="mt-4 text-2xl font-bold text-black tracking-widest">
            @yield('message')
        </h2>

        {{-- Deskripsi Tambahan --}}
        <p class="mt-6 text-base text-gray-600">
            @yield('description')
        </p>

        {{-- Bagian Aksi/Tombol --}}
        <div class="mt-10">
            {{-- Tombol Beranda --}}
            <a href="{{ url('/') }}"
                class="inline-block bg-transparent border-2 border-black px-8 py-2 text-sm font-semibold text-black hover:bg-black hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition-all duration-300">
                BERANDA
            </a>
        </div>
    </main>
</body>

</html>