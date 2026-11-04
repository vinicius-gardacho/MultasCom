@props(['label', 'value', 'hint' => null])

<div {{ $attributes->class('rounded-lg border border-slate-200 bg-white p-4 shadow-sm') }}>
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
