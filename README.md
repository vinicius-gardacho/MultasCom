# MultasCom

Sistema web em Laravel para consulta de infrações de trânsito, construído sobre o banco
PostgreSQL já existente do trabalho de Banco de Dados II (tema: infrações de trânsito,
região de Guarapuava, Pitanga, Irati e municípios vizinhos — PR).

A aplicação **se adapta ao banco existente**: não cria, altera nem remove tabelas, colunas,
views, índices ou dados. Todas as funcionalidades são de **consulta**.

---

## Sumário

1. [Visão geral da plataforma](#1-visão-geral-da-plataforma)
2. [Telas e funcionalidades](#2-telas-e-funcionalidades)
3. [Banco de dados utilizado](#3-banco-de-dados-utilizado)
4. [Arquitetura e organização do código](#4-arquitetura-e-organização-do-código)
5. [Segurança](#5-segurança)
6. [Requisitos](#6-requisitos)
7. [Instalação e execução](#7-instalação-e-execução)
8. [Login: usuário e senha](#8-login-usuário-e-senha)
9. [Testes automatizados](#9-testes-automatizados)
10. [Cuidados com o banco de dados](#10-cuidados-com-o-banco-de-dados)
11. [Solução de problemas](#11-solução-de-problemas)
12. [Observações sobre os dados atuais](#12-observações-sobre-os-dados-atuais)
13. [Limitações e sugestões futuras](#13-limitações-e-sugestões-futuras)

---

## 1. Visão geral da plataforma

O MultasCom permite que um usuário autenticado consulte, em um só lugar:

- os **condutores** cadastrados, a situação da CNH e os pontos ativos na carteira;
- os **veículos** e seus proprietários;
- as **multas**, com filtros por status, órgão aplicador e período;
- as **multas inadimplentes** (em atraso);
- os **órgãos aplicadores** e quanto cada um autuou;
- **relatórios** prontos baseados nas views do banco, com exportação em **PDF**.

Tecnologias:

| Camada | Tecnologia |
| --- | --- |
| Linguagem | PHP 8.3+ |
| Framework | Laravel 13 |
| Banco de dados | PostgreSQL 18 (Neon) |
| Interface | Blade + Tailwind CSS 4, compilado com Vite |
| Testes | PHPUnit 12 com SQLite em memória |

Fluxo de uma requisição:

```
Navegador ──> rotas (routes/web.php) ──> middleware auth ──> Controller
                                                              │
                                    FormRequest (valida filtros)
                                                              │
                                   Model Eloquent (tabela ou view)
                                                              │
                                     PostgreSQL (somente SELECT)
                                                              │
Navegador <── HTML (Blade + Tailwind) <──────────────────────┘
```

---

## 2. Telas e funcionalidades

Todas as telas, exceto o login, exigem autenticação. As listagens são paginadas e os
filtros ficam na URL (é possível salvar ou compartilhar uma pesquisa).

Identidade visual: a logo do MultasCom (uma multa com sinal de alerta) aparece na tela de
login, no canto superior esquerdo de todas as páginas (com link para o painel), como ícone
da aba do navegador e no cabeçalho dos PDFs. O rodapé de todas as páginas mostra o
**usuário conectado** no momento.

| Menu | Endereço | O que mostra |
| --- | --- | --- |
| Login | `/login` | Acesso ao sistema. Bloqueia por 1 minuto após 5 tentativas inválidas. |
| Painel | `/` | Totais de condutores, veículos, multas e órgãos; multas por status (quantidade e valor); total inadimplente; CNHs vencidas; top 5 infrações, veículos mais multados e CNHs a vencer. |
| Condutores | `/condutores` | Pesquisa por nome, CPF ou número da CNH; filtros por categoria da CNH e situação (válida/vencida). Mostra quantidade de multas e pontos ativos. |
| Detalhe do condutor | `/condutores/{id}` | Dados pessoais, veículos, multas e extrato de pontuação com lançamentos ativos e expirados. |
| Veículos | `/veiculos` | Pesquisa por placa (aceita com ou sem hífen), RENAVAM, marca ou modelo; filtro por tipo. |
| Detalhe do veículo | `/veiculos/{id}` | Dados do veículo, proprietário, multas e valor total. |
| Multas | `/multas` | Pesquisa por placa, CPF, nome do condutor, código ou descrição; filtros por status, órgão e período da infração; total encontrado e valor somado. |
| Detalhe da multa | `/multas/{id}` | Infração, local, data, órgão, condutor, veículo, status, pagamento e lançamento de pontos. |
| Inadimplentes | `/multas/inadimplentes` | Multas sem pagamento, com status Pendente ou Vencida e vencimento anterior a hoje, ordenadas por dias de atraso. |
| Órgãos | `/orgaos` | Os 15 órgãos com contato, quantidade e valor total de multas (link para as multas do órgão). |
| Relatórios | `/relatorios/ranking-infracoes` | Infrações mais cometidas (ocorrências, pontos, valor médio e total). |
| | `/relatorios/veiculos-mais-multados` | Veículos com mais multas e valor total. |
| | `/relatorios/cnh-proximas-vencer` | CNHs vencidas ou que vencem nos próximos 90 dias. |
| Exportar PDF | `/relatorios/{relatório}/pdf` | Botão **Exportar PDF** em cada relatório: baixa um PDF A4 paisagem com **todos** os registros (sem paginação), data/hora de geração, usuário e numeração de páginas. Arquivo `nome-do-relatorio-AAAA-MM-DD.pdf`. |

Regras exibidas pelo sistema (todas vindas do banco, nenhuma inventada):

- **Pontos ativos**: soma dos lançamentos de `pontuacao_condutor` cuja `data_expiracao` é
  hoje ou posterior.
- **CNH vencida**: `validade_cnh` anterior a hoje.
- **Inadimplência**: regra da view `vw_viniciusgardacho_multasInadimplentes`.
- **Status da multa**: descrições cadastradas em `status_multa`
  (Pendente, Paga, Contestada, Cancelada, Vencida).

---

## 3. Banco de dados utilizado

- **Database:** `viniciusgardacho_trabalho_db`
- **Schema:** `viniciusgardacho_trabalho_db`
- **Servidor:** Neon (PostgreSQL 18), conexão com SSL obrigatório.

### Tabelas e views mapeadas

| Tabela / view | Model | Chave |
| --- | --- | --- |
| `condutor` | `App\Models\Condutor` | `id_condutor` |
| `veiculo` | `App\Models\Veiculo` | `id_veiculo` |
| `orgao_aplicador` | `App\Models\OrgaoAplicador` | `id_orgao` |
| `status_multa` | `App\Models\StatusMulta` | `id_status` |
| `multa` | `App\Models\Multa` | `id_multa` |
| `pontuacao_condutor` | `App\Models\PontuacaoCondutor` | `id_pontuacao` |
| `vw_viniciusgardacho_multasdetalhadas` | `App\Models\Views\MultaDetalhada` | `id_multa` |
| `vw_viniciusgardacho_multasinadimplentes` | `App\Models\Views\MultaInadimplente` | `id_multa` |
| `vw_viniciusgardacho_rankinginfracoes` | `App\Models\Views\RankingInfracao` | — |
| `vw_viniciusgardacho_veiculosmaismultados` | `App\Models\Views\VeiculoMaisMultado` | `id_veiculo` |
| `vw_viniciusgardacho_cnhproximasvencer` | `App\Models\Views\CnhProximaVencer` | `id_condutor` |

### Relacionamentos

```
condutor 1 ──< veiculo            (veiculo.condutor_id, aceita nulo)
condutor 1 ──< multa              (multa.condutor_id)
condutor 1 ──< pontuacao_condutor (pontuacao_condutor.condutor_id)
veiculo  1 ──< multa              (multa.veiculo_id)
orgao_aplicador 1 ──< multa       (multa.orgao_id)
status_multa    1 ──< multa       (multa.status_id)
multa    1 ──o| pontuacao_condutor (pontuacao_condutor.multa_id é único; nem toda multa tem)
```

### Adaptações às convenções do Laravel

| Situação no banco | Como o Laravel foi adaptado |
| --- | --- |
| Tabelas no singular e chaves `id_<tabela>` | Atributo `#[Table(name:, key:)]` em cada model |
| Sem colunas `created_at`/`updated_at` | `timestamps: false` |
| Tabelas em schema próprio, acesso pelo pooler do Neon | Nome da tabela qualificado com `DB_SCHEMA` (trait `UsesExistingSchema`) |
| Views sem chave e não graváveis | Models somente leitura (trait `IsReadOnlyView`) |
| Sem tabelas de sessão, cache e fila | Drivers `file`, `file` e `sync` |
| Sem tabela de usuários | Usuário único definido no `.env` (`App\Auth\EnvUserProvider`) |

---

## 4. Arquitetura e organização do código

```
app/
├── Auth/EnvUserProvider.php          # login com usuário único do .env
├── Http/
│   ├── Controllers/                  # Dashboard, Condutor, Veiculo, Multa,
│   │   │                             # OrgaoAplicador, Relatorio
│   │   └── Auth/LoginController.php
│   ├── Middleware/SecurityHeaders.php
│   └── Requests/                     # validação dos filtros e do login
├── Models/                           # 6 tabelas
│   ├── Concerns/                     # UsesExistingSchema, IsReadOnlyView
│   └── Views/                        # 5 views (somente leitura)
├── Providers/AppServiceProvider.php  # registra o login "env" e bloqueia comandos destrutivos
└── Support/Formatacao.php            # moeda, CPF e datas no padrão brasileiro
bootstrap/app.php                     # middleware e tratamento de erros do banco
config/                               # configurações (banco lido do .env)
lang/pt_BR/                           # mensagens em português
public/logo.svg                       # logo (favicon e PDFs)
resources/
├── css/app.css                       # Tailwind e classes de componentes
└── views/
    ├── components/                   # layouts, logo, cartões, indicadores, filtros, erros
    ├── auth/  condutores/  veiculos/  multas/  orgaos/  relatorios/  errors/
    └── dashboard.blade.php
routes/web.php                        # todas as rotas
tests/                                # testes (SQLite em memória)
```

Decisões principais:

- **Consultas eficientes:** contagens e somas usam subconsultas (`withCount`/`withSum`) e
  as relações são carregadas antecipadamente (`with`), evitando o problema de N+1 consultas.
  A listagem de multas usa a view de multas detalhadas, que já traz os joins prontos.
- **Validação:** cada listagem tem um `FormRequest`. Valores de filtro precisam existir no
  banco (ex.: o status precisa existir em `status_multa`). Filtro inválido volta para a
  listagem sem filtros e exibe a mensagem.
- **Componentes Blade reutilizáveis:** layout, cartão, indicador, selo de status,
  formulário de filtros, linha vazia e página de erro.

---

## 5. Segurança

- Todas as telas exigem login (middleware `auth`).
- A senha de acesso fica no `.env` apenas como **hash bcrypt**; sem as variáveis
  configuradas, nenhum login é aceito.
- Limite de 5 tentativas de login por minuto (por usuário + IP).
- Proteção CSRF nos formulários; sessão regenerada no login e invalidada no logout.
- Apenas `login` e `logout` aceitam `POST`; o restante é `GET` (há teste garantindo isso).
- Cabeçalhos `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` e
  `Cache-Control: no-store` nas páginas autenticadas (os dados incluem CPF e endereço).
- Falhas no banco mostram uma página amigável (HTTP 503) sem SQL, nomes de tabelas ou
  dados de conexão; os detalhes vão para `storage/logs/laravel.log`.
- Credenciais do banco ficam somente no `.env`, que é ignorado pelo Git.
- Recomenda-se conectar com um usuário **somente leitura** (como o usuário `professor`).

---

## 6. Requisitos

- **PHP 8.3 ou superior** com as extensões `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`,
  `fileinfo`, `dom` e `pdo_sqlite` (para os testes).
- Os PDFs são gerados pelo pacote `barryvdh/laravel-dompdf` (instalado pelo
  `composer install`), em PHP puro, sem programas externos.
- **Composer 2**.
- **Node.js 20 ou superior** e **npm**.
- Acesso à internet para conectar ao Neon.

No Windows, uma forma simples de instalar PHP e Composer é o instalador oficial do
[php.new](https://php.new) ou o [Laravel Herd](https://herd.laravel.com). Depois de
instalar, confirme:

```bash
php -v
```

```bash
composer -V
```

```bash
php -m
```

A saída de `php -m` deve conter `pdo_pgsql`. Se não aparecer, habilite
`extension=pdo_pgsql` e `extension=pgsql` no `php.ini`.

---

## 7. Instalação e execução

Execute os comandos na pasta do projeto.

**1. Instalar as dependências PHP**

```bash
composer install
```

**2. Criar o arquivo de configuração**

```bash
cp .env.example .env
```

No Windows (PowerShell), use `Copy-Item .env.example .env`.

**3. Gerar a chave da aplicação**

```bash
php artisan key:generate
```

**4. Configurar o banco no `.env`**

```dotenv
DB_CONNECTION=pgsql
DB_HOST=<host do Neon>
DB_PORT=5432
DB_DATABASE=viniciusgardacho_trabalho_db
DB_USERNAME=<usuário, de preferência somente leitura>
DB_PASSWORD='<senha>'
DB_SCHEMA=viniciusgardacho_trabalho_db
DB_SSLMODE=require
```

Para o banco do trabalho, use o host e o usuário somente leitura informados no documento
da disciplina. Senhas com caracteres especiais (`@`, `#`, `$`) devem ficar entre aspas simples.

**5. Configurar o login** (veja a seção 8)

**6. Instalar e compilar a interface**

```bash
npm install
```

```bash
npm run build
```

**7. Testar a conexão (somente leitura)**

```bash
php artisan db:show
```

Deve listar PostgreSQL 18, o database `viniciusgardacho_trabalho_db` e 6 tabelas.

**8. Iniciar o servidor**

```bash
php artisan serve
```

Acesse **http://localhost:8000** e entre com o usuário e a senha configurados.

Durante o desenvolvimento da interface, `npm run dev` (em outro terminal) recompila o CSS
automaticamente a cada alteração.

---

## 8. Login: usuário e senha

O sistema tem **um único usuário**, definido no `.env`:

```dotenv
ADMIN_USERNAME=admin
ADMIN_PASSWORD_HASH='<hash bcrypt da senha>'
```

A senha nunca é gravada em texto puro. Para definir (ou trocar) a senha:

1. Gere o hash, trocando `sua-senha` pela senha desejada:

   ```bash
   php -r "echo password_hash('sua-senha', PASSWORD_BCRYPT), PHP_EOL;"
   ```

2. Copie o resultado (começa com `$2y$`) para `ADMIN_PASSWORD_HASH`, **entre aspas simples**.
3. Limpe o cache de configuração:

   ```bash
   php artisan config:clear
   ```

4. Entre em `/login` com `ADMIN_USERNAME` e a senha escolhida.

Se `ADMIN_USERNAME` ou `ADMIN_PASSWORD_HASH` estiverem vazios, nenhum login é aceito.

---

## 9. Testes automatizados

```bash
php artisan test
```

São 79 testes cobrindo models, views, login, todas as telas, filtros, validações,
exportação em PDF, logo e rodapé, páginas de erro e cabeçalhos de segurança.

Os testes **não acessam o PostgreSQL**: rodam em SQLite na memória, com uma réplica da
estrutura das tabelas e views e dados fictícios (`tests/Concerns/UsesTestDatabase.php`).
O `phpunit.xml` força essa conexão e o `TestCase` interrompe a execução se detectar outro banco.

Verificação de estilo do código:

```bash
vendor/bin/pint --test
```

---

## 10. Cuidados com o banco de dados

O banco é compartilhado e já está populado. **Não execute:**

| Comando | Por quê |
| --- | --- |
| `php artisan migrate` | Criaria a tabela `migrations` no banco |
| `php artisan db:seed` | Tentaria inserir dados |
| `php artisan migrate:fresh`, `migrate:refresh`, `migrate:reset`, `db:wipe` | Apagariam o banco (bloqueados pela aplicação com `DB::prohibitDestructiveCommands()`) |

O projeto não possui migrations nem seeders. Comandos seguros: `db:show`, `route:list`,
`config:clear`, `about`, `test` e `serve`.

---

## 11. Solução de problemas

| Sintoma | Causa provável e solução |
| --- | --- |
| `Vite manifest not found` | Rode `npm install` e `npm run build`. |
| Página sem estilo ou layout quebrado | Rode `npm run build` de novo (o Tailwind só gera as classes encontradas no build). |
| "Banco de dados indisponível" | Confira `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD` e `DB_SSLMODE=require`; rode `php artisan config:clear`. Detalhes em `storage/logs/laravel.log`. |
| `could not find driver` | Extensão `pdo_pgsql` desabilitada no `php.ini`. |
| `relation "condutor" does not exist` | `DB_DATABASE` ou `DB_SCHEMA` incorretos (os dois são `viniciusgardacho_trabalho_db`). |
| Login sempre inválido | Hash fora das aspas simples, variáveis vazias ou cache antigo: rode `php artisan config:clear`. |
| "Muitas tentativas de acesso" | Aguarde 1 minuto. |
| `No application encryption key` | Rode `php artisan key:generate`. |

---

## 12. Observações sobre os dados atuais

- Volume: 222 condutores, 300 veículos, 15 órgãos, 5 status, 215 multas e 126 lançamentos de pontos.
- 89 multas não possuem lançamento em `pontuacao_condutor` (a tela de detalhe informa isso).
- Cada código de infração aparece em uma única multa, por isso o ranking de infrações exibe
  uma ocorrência por item.
- Algumas multas e lançamentos têm datas posteriores à data atual; os dados são exibidos
  como estão no banco.
- Os dados foram gerados para o trabalho acadêmico e são fictícios.

---

## 13. Limitações e sugestões futuras

As sugestões abaixo dependem de alterações no banco ou de credenciais com permissão de
escrita e **só devem ser feitas com autorização**:

- **Cadastro e edição** de multas, registro de pagamento e mudança de status: exigem um
  usuário com permissão de escrita (o usuário `professor` possui apenas `SELECT`).
- **Exclusões:** as chaves estrangeiras de `multa` e `pontuacao_condutor` usam
  `ON DELETE SET NULL` em colunas `NOT NULL`, o que faria uma exclusão falhar; seria preciso
  rever essas restrições.
- **Múltiplos usuários e perfis:** exigiriam uma tabela de usuários no lugar da credencial
  única do `.env`.
- **Desempenho:** índices em `multa(status_id)`, `multa(orgao_id)` e
  `multa(data_hora_infracao)` ajudariam se o volume de dados crescer.
