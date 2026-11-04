@props(['colspan', 'message' => 'Nenhum registro encontrado.'])

<tr>
    <td colspan="{{ $colspan }}" class="py-8 text-center text-slate-500">{{ $message }}</td>
</tr>
