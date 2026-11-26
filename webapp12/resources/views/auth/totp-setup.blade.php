<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header title="Activa la verificación en dos pasos" description="Agrega la cuenta en Google Authenticator (o similar) y escribe el código de 6 dígitos" />

        <div class="flex flex-col gap-2 text-center text-sm text-zinc-600 dark:text-zinc-400">
            <p>En la app elige "Ingresar clave de configuración" y escribe esta clave (tipo: basada en tiempo):</p>
            <p class="font-mono text-base select-all break-all">{{ $secret }}</p>
            <p><a class="underline" href="{{ $uri }}">Abrir en mi app de autenticación (móvil)</a></p>
        </div>

        <form method="POST" action="{{ route('totp.confirm') }}" class="flex flex-col gap-6">
            @csrf
            <flux:input name="code" label="Código de 6 dígitos" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required autofocus />
            @error('code')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <flux:button variant="primary" type="submit" class="w-full">Activar y continuar</flux:button>
        </form>
    </div>
</x-layouts.auth>
