<?php

namespace App\Http\Requests;

use App\Models\Condutor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CondutorIndexRequest extends FormRequest
{
    /**
     * Em caso de filtro inválido, volta para a listagem sem filtros.
     */
    protected $redirectRoute = 'condutores.index';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'categoria' => ['nullable', 'string', Rule::exists(Condutor::class, 'categoria_cnh')],
            'cnh' => ['nullable', Rule::in(['valida', 'vencida'])],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['cnh' => 'situação da CNH'];
    }
}
