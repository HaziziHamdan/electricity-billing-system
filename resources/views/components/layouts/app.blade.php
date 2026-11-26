@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <header class="border-b bg-white">
        <nav class="mx-auto flex max-w-6xl items-center gap-6 px-4 py-3">
            <a href="{{ route('usages.index') }}" class="font-semibold">{{ config('app.name') }}</a>

            @php
                $links = [
                    'usages.index'  => ['Records', 'usages.*'],
                    'reports.bills' => ['Bill report', 'reports.*'],
                    'tariffs.index' => ['Tariff rates', 'tariffs.*'],
                ];
            @endphp

            @foreach ($links as $route => [$label, $pattern])
                @if (Route::has($route))
                    <a href="{{ route($route) }}"
                       @class([
                           'text-sm hover:text-blue-700',
                           'font-semibold text-blue-700' => request()->routeIs($pattern),
                           'text-gray-600' => ! request()->routeIs($pattern),
                       ])>
                        {{ $label }}
                    </a>
                @endif
            @endforeach
        </nav>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8">
        @if (session('status'))
            <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
                {{ session('status') }}
            </div>
        @endif

        @if ($title)
            <h1 class="mb-6 text-2xl font-semibold">{{ $title }}</h1>
        @endif

        {{ $slot }}
    </main>
</body>
</html>