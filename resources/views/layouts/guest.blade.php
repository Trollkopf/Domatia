<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Domatia')</title>
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/swiper.js'])
    @yield('style', '')

    <style>
        body {
            font-family: 'Titillium Web', sans-serif;
            background-color: #fff;
            color: #222;
        }

        h1,
        h2,
        h3 {
            font-weight: 300;
        }

        a {
            text-decoration: none;
        }
    </style>
</head>

<body>
    @include('layouts.navigation')

    <main class="pb-4">
        @yield('content')
        @yield('slider')
    </main>

    <footer class="text-center text-muted border-top py-4 mt-5">
        © {{ date('Y') }} Domatia. Todos los derechos reservados.
    </footer>

    @stack('scripts')
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
</body>

</html>
