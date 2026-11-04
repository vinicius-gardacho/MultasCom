<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }} · {{ config('app.name') }}</title>
    <style>
        @page { margin: 90px 32px 50px 32px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #0f172a; }
        header { position: fixed; top: -70px; left: 0; right: 0; height: 56px; border-bottom: 2px solid #1e3a8a; }
        header .logo { position: absolute; top: 0; left: 0; width: 40px; height: 40px; }
        header .sistema { margin-left: 50px; font-size: 14px; font-weight: bold; color: #1e3a8a; }
        header .sistema span { color: #f59e0b; }
        header .titulo { margin-left: 50px; font-size: 12px; font-weight: bold; margin-top: 4px; }
        header .meta { position: absolute; top: 0; right: 0; text-align: right; color: #475569; font-size: 8px; line-height: 1.5; }
        footer { position: fixed; bottom: -32px; left: 0; right: 0; height: 18px; border-top: 1px solid #cbd5e1; color: #64748b; font-size: 8px; padding-top: 4px; }
        .descricao { color: #475569; margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1e3a8a; color: #fff; font-weight: bold; text-align: left; padding: 5px 6px; }
        td { padding: 4px 6px; border-bottom: 1px solid #e2e8f0; vertical-align: top; }
        tr:nth-child(even) td { background: #f8fafc; }
        .direita { text-align: right; }
        .vazio { text-align: center; color: #64748b; padding: 16px; }
    </style>
</head>
<body>
    <header>
        <img class="logo" src="{{ public_path('logo.svg') }}" alt="">
        <div class="sistema">Multas<span>Com</span></div>
        <div class="titulo">{{ $titulo }}</div>
        <div class="meta">
            Gerado em {{ $geradoEm->format('d/m/Y H:i') }}<br>
            por {{ auth()->user()?->name ?? '—' }}<br>
            {{ count($linhas) }} registro(s)
        </div>
    </header>

    <footer>
        {{ config('app.name') }} — dados do banco de infrações de trânsito
    </footer>

    <main>
        @if ($descricao)
            <p class="descricao">{{ $descricao }}</p>
        @endif

        <table>
            <thead>
                <tr>
                    @foreach ($colunas as $coluna => $alinhamento)
                        <th @class(['direita' => $alinhamento === 'right'])>{{ $coluna }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($linhas as $linha)
                    <tr>
                        @foreach (array_values($colunas) as $indice => $alinhamento)
                            <td @class(['direita' => $alinhamento === 'right'])>{{ $linha[$indice] }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($colunas) }}" class="vazio">Nenhum registro encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>
