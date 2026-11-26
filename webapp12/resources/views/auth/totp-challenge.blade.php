<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <x-auth-header title="Verificación en dos pasos" description="Ingresa el código de 6 dígitos de tu app de autenticación" />

        <form method="POST" action="{{ route('totp.verify') }}" class="flex flex-col gap-6">
            @csrf
            <flux:input name="code" label="Código de 6 dígitos" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required autofocus />
            @error('code')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <flux:button variant="primary" type="submit" class="w-full">Verificar</flux:button>
        </form>
    </div>
</x-layouts.auth>
