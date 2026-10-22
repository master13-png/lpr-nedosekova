
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Аптека' }} — {{ config('app.name') }}</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand bg-dark navbar-dark mb-4">
        <div class="container">

<a class="navbar-brand d-inline-flex align-items-center gap-2"
   href="{{ route('catalog.index') }}">

    <span>Pharmacy Umbrella</span>

    <img
        src="{{ asset('images/umbrella.png') }}"
        alt="Pharmacy Umbrella"
        width="36"
        height="36"
        style="object-fit: contain;"
    >

</a>

            <a class="nav-link text-light" href="{{ route('feedback.form') }}">
                Обратная связь
            </a>
        </div>
    </nav>

    <main class="container flex-grow-1">

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')

    </main>

    <footer class="bg-light border-top py-3 mt-4">
        <div class="container small text-muted">

            {{ config('lab.student') }}

            ({{ config('lab.group') }},
            N = {{ config('lab.number') }})

            · Код занятия:
            <strong>{{ config('lab.code') }}</strong>

            · {{ now()->format('d.m.Y H:i:s') }}

        </div>
    </footer>

</body>
</html>
