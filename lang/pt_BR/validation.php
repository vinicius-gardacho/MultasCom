<?php

// Mensagens das regras usadas pela aplicação. Regras não listadas aqui usam a
// tradução padrão em inglês do framework (APP_FALLBACK_LOCALE).
return [
    'after_or_equal' => 'O campo :attribute deve ser uma data igual ou posterior a :date.',
    'before_or_equal' => 'O campo :attribute deve ser uma data igual ou anterior a :date.',
    'date' => 'O campo :attribute deve ser uma data válida.',
    'date_format' => 'O campo :attribute deve estar no formato :format.',
    'exists' => 'O valor selecionado para :attribute é inválido.',
    'in' => 'O valor selecionado para :attribute é inválido.',
    'integer' => 'O campo :attribute deve ser um número inteiro.',
    'max' => [
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'string' => 'O campo :attribute não pode ter mais de :max caracteres.',
    ],
    'min' => [
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
    ],
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser um texto.',

    'attributes' => [
        'q' => 'pesquisa',
        'categoria' => 'categoria da CNH',
        'tipo' => 'tipo de veículo',
        'status' => 'status',
        'orgao' => 'órgão aplicador',
        'data_inicio' => 'data inicial',
        'data_fim' => 'data final',
        'usuario' => 'usuário',
        'senha' => 'senha',
    ],
];
