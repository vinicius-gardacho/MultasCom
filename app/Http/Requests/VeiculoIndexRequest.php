<?php

namespace App\Http\Requests;

use App\Models\Veiculo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VeiculoIndexRequest extends FormRequest
{
    /**
     * Em caso de filtro inválido, volta para a listagem sem filtros.
     */
    protected $redirectRoute = 'veiculos.index';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'tipo' => ['nullable', 'string', Rule::exists(Veiculo::class, 'tipo_veiculo')],
        ];
    }
}
