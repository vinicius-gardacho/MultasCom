<x-layouts.guest title="Entrar">
    <h1 class="mb-4 text-lg font-semibold">Acesso ao sistema</h1>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="usuario" class="form-label">Usuário</label>
            <input id="usuario" name="usuario" type="text" value="{{ old('usuario') }}" required autofocus autocomplete="username" maxlength="100" class="form-input">
            @error('usuario')
                <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="senha" class="form-label">Senha</label>
            <input id="senha" name="senha" type="password" required autocomplete="current-password" class="form-input">
            @error('senha')
                <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-full">Entrar</button>
    </form>
</x-layouts.guest>
