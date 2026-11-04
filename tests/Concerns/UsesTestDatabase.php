<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\DB;

/**
 * Cria, em SQLite na memória, uma réplica da estrutura do banco PostgreSQL
 * (tabelas e views) com um conjunto pequeno de dados fictícios de teste.
 *
 * Os testes nunca acessam o banco real: phpunit.xml força DB_CONNECTION=sqlite
 * e DB_DATABASE=:memory:. As views abaixo reproduzem a lógica das views do
 * PostgreSQL usando funções de data do SQLite.
 */
trait UsesTestDatabase
{
    protected function setUpUsesTestDatabase(): void
    {
        $this->ensureTestDatabase();
        $this->createTestSchema();
        $this->seedTestData();
    }

    private function createTestSchema(): void
    {
        $statements = [
            'create table condutor (
                id_condutor integer primary key autoincrement,
                nome varchar(150) not null,
                cpf char(11) not null unique,
                numero_cnh varchar(20) not null unique,
                categoria_cnh varchar(5) not null,
                validade_cnh date not null,
                data_nascimento date not null,
                endereco varchar(200),
                telefone varchar(20),
                email varchar(120)
            )',
            'create table veiculo (
                id_veiculo integer primary key autoincrement,
                placa varchar(8) not null unique,
                renavam varchar(20) not null unique,
                marca varchar(50) not null,
                modelo varchar(60) not null,
                ano_fabricacao smallint not null,
                ano_modelo smallint not null,
                cor varchar(30),
                tipo_veiculo varchar(30) not null,
                condutor_id integer references condutor(id_condutor)
            )',
            'create table orgao_aplicador (
                id_orgao integer primary key autoincrement,
                nome varchar(150) not null,
                sigla varchar(20) not null,
                jurisdicao varchar(100) not null,
                endereco varchar(200),
                telefone varchar(20),
                email varchar(120)
            )',
            'create table status_multa (
                id_status integer primary key autoincrement,
                descricao varchar(30) not null unique
            )',
            'create table multa (
                id_multa integer primary key autoincrement,
                veiculo_id integer not null references veiculo(id_veiculo),
                condutor_id integer not null references condutor(id_condutor),
                orgao_id integer not null references orgao_aplicador(id_orgao),
                status_id integer not null default 1 references status_multa(id_status),
                codigo_infracao varchar(10) not null,
                descricao_infracao varchar(250) not null,
                data_hora_infracao timestamp not null,
                local_infracao varchar(200) not null,
                valor decimal(10,2) not null,
                pontos integer not null,
                data_vencimento date not null,
                data_pagamento date
            )',
            'create table pontuacao_condutor (
                id_pontuacao integer primary key autoincrement,
                condutor_id integer not null references condutor(id_condutor),
                multa_id integer not null unique references multa(id_multa),
                pontos integer not null,
                data_lancamento date not null default current_date,
                data_expiracao date not null
            )',
            'create view vw_viniciusgardacho_multasdetalhadas as
                select m.id_multa, m.data_hora_infracao, m.codigo_infracao, m.descricao_infracao,
                    m.local_infracao, m.valor, m.pontos, m.data_vencimento, m.data_pagamento,
                    sm.descricao as status_multa, c.id_condutor, c.nome as nome_condutor, c.cpf,
                    c.numero_cnh, v.id_veiculo, v.placa, v.marca, v.modelo, oa.id_orgao,
                    oa.sigla as orgao_sigla, oa.nome as orgao_nome, oa.jurisdicao
                from multa m
                    join condutor c on c.id_condutor = m.condutor_id
                    join veiculo v on v.id_veiculo = m.veiculo_id
                    join orgao_aplicador oa on oa.id_orgao = m.orgao_id
                    join status_multa sm on sm.id_status = m.status_id',
            "create view vw_viniciusgardacho_multasinadimplentes as
                select m.id_multa, c.nome as nome_condutor, c.cpf, c.telefone, v.placa,
                    m.descricao_infracao, m.valor, m.data_vencimento,
                    cast(julianday(date('now', 'localtime')) - julianday(m.data_vencimento) as integer) as dias_em_atraso,
                    sm.descricao as status_multa
                from multa m
                    join condutor c on c.id_condutor = m.condutor_id
                    join veiculo v on v.id_veiculo = m.veiculo_id
                    join status_multa sm on sm.id_status = m.status_id
                where m.data_pagamento is null
                    and sm.descricao in ('Pendente', 'Vencida')
                    and m.data_vencimento < date('now', 'localtime')
                order by dias_em_atraso desc",
            'create view vw_viniciusgardacho_rankinginfracoes as
                select codigo_infracao, descricao_infracao, count(*) as quantidade_ocorrencias,
                    sum(pontos) as total_pontos_gerados, round(avg(valor), 2) as valor_medio,
                    sum(valor) as valor_total
                from multa
                group by codigo_infracao, descricao_infracao
                order by quantidade_ocorrencias desc, valor_total desc',
            'create view vw_viniciusgardacho_veiculosmaismultados as
                select v.id_veiculo, v.placa, v.marca, v.modelo, v.tipo_veiculo,
                    c.nome as nome_proprietario,
                    (select count(*) from multa m where m.veiculo_id = v.id_veiculo) as qtd_multas,
                    (select coalesce(sum(m.valor), 0) from multa m where m.veiculo_id = v.id_veiculo) as valor_total_multas
                from veiculo v
                    left join condutor c on c.id_condutor = v.condutor_id
                where exists (select 1 from multa m where m.veiculo_id = v.id_veiculo)
                order by qtd_multas desc, valor_total_multas desc',
            "create view vw_viniciusgardacho_cnhproximasvencer as
                select c.id_condutor, c.nome as nome_condutor, c.numero_cnh, c.categoria_cnh, c.validade_cnh,
                    cast(julianday(c.validade_cnh) - julianday(date('now', 'localtime')) as integer) as dias_para_vencer,
                    (select count(*) from multa m where m.condutor_id = c.id_condutor) as qtd_multas_condutor
                from condutor c
                where c.validade_cnh <= date('now', 'localtime', '+90 days')
                order by c.validade_cnh asc",
        ];

        foreach ($statements as $statement) {
            DB::statement($statement);
        }
    }

    private function seedTestData(): void
    {
        $dia = fn (int $dias) => today()->addDays($dias)->toDateString();

        DB::table('status_multa')->insert([
            ['id_status' => 1, 'descricao' => 'Pendente'],
            ['id_status' => 2, 'descricao' => 'Paga'],
            ['id_status' => 3, 'descricao' => 'Contestada'],
            ['id_status' => 4, 'descricao' => 'Cancelada'],
            ['id_status' => 5, 'descricao' => 'Vencida'],
        ]);

        DB::table('orgao_aplicador')->insert([
            ['id_orgao' => 1, 'nome' => 'Órgão de Teste Estadual', 'sigla' => 'TESTE-EST', 'jurisdicao' => 'Estadual', 'endereco' => null, 'telefone' => null, 'email' => null],
            ['id_orgao' => 2, 'nome' => 'Órgão de Teste Federal', 'sigla' => 'TESTE-FED', 'jurisdicao' => 'Federal', 'endereco' => null, 'telefone' => null, 'email' => null],
        ]);

        DB::table('condutor')->insert([
            ['id_condutor' => 1, 'nome' => 'Ana Teste', 'cpf' => '11111111111', 'numero_cnh' => '90000000001', 'categoria_cnh' => 'B', 'validade_cnh' => $dia(30), 'data_nascimento' => '1990-01-15', 'endereco' => null, 'telefone' => '(42) 90000-0001', 'email' => null],
            ['id_condutor' => 2, 'nome' => 'Bruno Teste', 'cpf' => '22222222222', 'numero_cnh' => '90000000002', 'categoria_cnh' => 'AB', 'validade_cnh' => $dia(700), 'data_nascimento' => '1985-06-20', 'endereco' => null, 'telefone' => null, 'email' => null],
            ['id_condutor' => 3, 'nome' => 'Carla Teste', 'cpf' => '33333333333', 'numero_cnh' => '90000000003', 'categoria_cnh' => 'C', 'validade_cnh' => $dia(-10), 'data_nascimento' => '1978-11-02', 'endereco' => null, 'telefone' => null, 'email' => null],
        ]);

        DB::table('veiculo')->insert([
            ['id_veiculo' => 1, 'placa' => 'TST1A11', 'renavam' => '90000000011', 'marca' => 'Marca A', 'modelo' => 'Modelo A', 'ano_fabricacao' => 2015, 'ano_modelo' => 2016, 'cor' => 'Azul', 'tipo_veiculo' => 'carro', 'condutor_id' => 1],
            ['id_veiculo' => 2, 'placa' => 'TST2B22', 'renavam' => '90000000022', 'marca' => 'Marca B', 'modelo' => 'Modelo B', 'ano_fabricacao' => 2020, 'ano_modelo' => 2020, 'cor' => null, 'tipo_veiculo' => 'moto', 'condutor_id' => 2],
            ['id_veiculo' => 3, 'placa' => 'TST3C33', 'renavam' => '90000000033', 'marca' => 'Marca C', 'modelo' => 'Modelo C', 'ano_fabricacao' => 2018, 'ano_modelo' => 2019, 'cor' => 'Branco', 'tipo_veiculo' => 'caminhão', 'condutor_id' => 3],
        ]);

        DB::table('multa')->insert([
            ['id_multa' => 1, 'veiculo_id' => 1, 'condutor_id' => 1, 'orgao_id' => 1, 'status_id' => 1, 'codigo_infracao' => '165', 'descricao_infracao' => 'Infração de teste gravíssima', 'data_hora_infracao' => $dia(-40).' 10:30:00', 'local_infracao' => 'Rua de Teste, 1', 'valor' => '2934.70', 'pontos' => 7, 'data_vencimento' => $dia(-10), 'data_pagamento' => null],
            ['id_multa' => 2, 'veiculo_id' => 1, 'condutor_id' => 1, 'orgao_id' => 2, 'status_id' => 2, 'codigo_infracao' => '218-I', 'descricao_infracao' => 'Infração de teste média', 'data_hora_infracao' => $dia(-100).' 08:00:00', 'local_infracao' => 'Rodovia de Teste, Km 1', 'valor' => '130.16', 'pontos' => 4, 'data_vencimento' => $dia(-70), 'data_pagamento' => $dia(-75)],
            ['id_multa' => 3, 'veiculo_id' => 2, 'condutor_id' => 2, 'orgao_id' => 1, 'status_id' => 5, 'codigo_infracao' => '181-XVII', 'descricao_infracao' => 'Infração de teste leve', 'data_hora_infracao' => $dia(-60).' 14:15:00', 'local_infracao' => 'Avenida de Teste, 2', 'valor' => '195.23', 'pontos' => 3, 'data_vencimento' => $dia(-30), 'data_pagamento' => null],
            ['id_multa' => 4, 'veiculo_id' => 2, 'condutor_id' => 2, 'orgao_id' => 2, 'status_id' => 3, 'codigo_infracao' => '230-V', 'descricao_infracao' => 'Infração de teste contestada', 'data_hora_infracao' => $dia(-5).' 19:45:00', 'local_infracao' => 'Rodovia de Teste, Km 2', 'valor' => '293.47', 'pontos' => 7, 'data_vencimento' => $dia(25), 'data_pagamento' => null],
        ]);

        DB::table('pontuacao_condutor')->insert([
            ['id_pontuacao' => 1, 'condutor_id' => 1, 'multa_id' => 1, 'pontos' => 7, 'data_lancamento' => $dia(-40), 'data_expiracao' => $dia(325)],
            ['id_pontuacao' => 2, 'condutor_id' => 1, 'multa_id' => 2, 'pontos' => 4, 'data_lancamento' => $dia(-400), 'data_expiracao' => $dia(-35)],
            ['id_pontuacao' => 3, 'condutor_id' => 2, 'multa_id' => 3, 'pontos' => 3, 'data_lancamento' => $dia(-60), 'data_expiracao' => $dia(305)],
        ]);
    }
}
