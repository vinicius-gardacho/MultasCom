@props(['action'])

<form method="GET" action="{{ $action }}" {{ $attributes->class('mb-6 rounded-lg border border-slate-200 bg-white p-4 shadow-sm') }} role="search">
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{ $slot }}
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary">Filtrar</button>
        <a href="{{ $action }}" class="btn btn-secondary">Limpar</a>
    </div>
</form>
