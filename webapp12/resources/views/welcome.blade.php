<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-white text-zinc-900 dark:bg-zinc-900 dark:text-zinc-100">
    <main class="text-center space-y-6 p-6">
        <h1 class="text-3xl font-semibold">{{ config('app.name') }}</h1>
        <div><x-server-badge /></div>

        @auth
            <p>Bienvenido, {{ auth()->user()->name }}.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-md bg-zinc-900 px-4 py-2 text-white dark:bg-white dark:text-zinc-900">
                    Cerrar sesión
                </button>
            </form>
        @else
            <div class="flex justify-center gap-3">
                <a href="{{ route('login') }}" class="rounded-md border border-zinc-300 px-4 py-2">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="rounded-md bg-zinc-900 px-4 py-2 text-white dark:bg-white dark:text-zinc-900">Registrarse</a>
            </div>
        @endauth
    </main>
</body>
</html>
