<?php

namespace App\Http\Requests;

use App\Models\OrgaoAplicador;
use App\Models\StatusMulta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MultaIndexRequest extends FormRequest
{
    /**
     * Em caso de filtro inválido, volta para a listagem sem filtros.
     */
    protected $redirectRoute = 'multas.index';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', Rule::exists(StatusMulta::class, 'descricao')],
            'orgao' => ['nullable', 'integer', Rule::exists(OrgaoAplicador::class, 'id_orgao')],
            'data_inicio' => ['nullable', 'date_format:Y-m-d'],
            'data_fim' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:data_inicio'],
        ];
    }
}
