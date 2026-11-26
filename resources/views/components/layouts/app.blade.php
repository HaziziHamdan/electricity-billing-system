@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <header class="sticky top-0 z-10 border-b border-gray-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center gap-x-8 gap-y-2 px-4 py-3">
            <a href="{{ route('usages.index') }}" class="text-sm font-semibold text-gray-900">
                {{ config('app.name') }}
            </a>
            @php
                $links = [
                    'usages.index'  => ['Records', 'usages.*'],
                    'reports.bills' => ['Bill report', 'reports.*'],
                    'tariffs.index' => ['Tariff rates', 'tariffs.*'],
                ];
            @endphp
            <nav class="-mx-1 flex gap-1 overflow-x-auto" aria-label="Main">
                @foreach ($links as $route => [$label, $pattern])
                    @if (Route::has($route))
                        <x-nav-link :href="route($route)" :active="request()->routeIs($pattern)">
                            {{ $label }}
                        </x-nav-link>
                    @endif
                @endforeach
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8">
        @if (session('status'))
            <div class="mb-6 flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
                <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 0 1 0 1.4l-8 8a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.4L8 12.6l7.3-7.3a1 1 0 0 1 1.4 0Z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($title)
            <h1 class="mb-6 text-2xl font-semibold">{{ $title }}</h1>
        @endif

        {{ $slot }}
    </main>
</body>
</html>