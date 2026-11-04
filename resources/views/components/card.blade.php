@props(['title' => null, 'action' => null, 'actionText' => 'Ver todos'])

<section {{ $attributes->class('rounded-lg border border-slate-200 bg-white shadow-sm') }}>
    @if ($title)
        <header class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3">
            <h2 class="text-base font-semibold text-slate-800">{{ $title }}</h2>
            @if ($action)
                <a href="{{ $action }}" class="link text-sm">{{ $actionText }}</a>
            @endif
        </header>
    @endif

    <div class="p-4">
        {{ $slot }}
    </div>
</section>
