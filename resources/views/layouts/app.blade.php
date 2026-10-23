<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Pharmacy Umbrella') }}</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="icon"
          type="image/png"
          href="{{ asset('images/umbrella.png') }}">
</head>

<body class="d-flex flex-column min-vh-100 bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">

            <a class="navbar-brand d-inline-flex align-items-center gap-2"
               href="{{ route('catalog.index') }}">

                <span>Pharmacy Umbrella</span>

                <img
                    src="{{ asset('images/umbrella.png') }}"
                    alt="Зонтик"
                    width="36"
                    height="36"
                    style="object-fit: contain;"
                >
            </a>

            <div class="d-flex gap-3">
                <a class="text-white text-decoration-none"
                   href="{{ route('catalog.index') }}">
                    Каталог
                </a>

                <a class="text-white text-decoration-none"
                   href="{{ route('feedback.form') }}">
                    Обратная связь
                </a>

                <a class="text-white text-decoration-none"
                   href="{{ route('medicines.index') }}">
                    Управление препаратами
                </a>
            </div>

        </div>
    </nav>

    <main class="container py-4 flex-grow-1">

        @yield('content')

    </main>

    <footer class="mt-auto py-3 border-top bg-white">
        <div class="container text-center text-muted small">

            <div>
                Pharmacy Umbrella © {{ date('Y') }}
            </div>

            <div>
                Недосекова Александра Ивановна |
                Ис-24-22б |
                Вариант 14 |
                {{ config('lab.code') }}
            </div>

        </div>
    </footer>

</body>
</html>
