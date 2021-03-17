# memberkit-php-sdk

Um SDK Simples para a API do MemberKit usando PHP

## Índice

- [Instalação](#instalação)
- [Configuração](#configuração)
- [Transações](#transações)
  - [Criando uma transação](#criando-uma-transação)
  - [Capturando uma transação](#capturando-uma-transação)
  - [Estornando uma transação](#estornando-uma-transação)
    - [Estornando uma transação parcialmente](#estornando-uma-transação-parcialmente)
    - [Estornando uma transação com split](#estornando-uma-transação-com-split)
  - [Retornando transações](#retornando-transações)
  - [Retornando uma transação](#retornando-uma-transação)
  - [Retornando recebíveis de uma transação](#retornando-recebíveis-de-uma-transação)
  - [Retornando um recebível de uma transação](#retornando-um-recebível-de-uma-transação)
  - [Retornando o histórico de operações de uma transação](#retornando-o-histórico-de-operações-de-uma-transação)
  - [Notificando cliente sobre boleto a ser pago](#notificando-cliente-sobre-boleto-a-ser-pago)
  - [Retornando eventos de uma transação](#retornando-eventos-de-uma-transação)
  - [Calculando Pagamentos Parcelados](#calculando-pagamentos-parcelados)
  - [Testando pagamento de boletos](#testando-pagamento-de-boletos)

## Instalação

Instale a biblioteca utilizando o comando

`composer require fgubert/memberkit-php-sdk`

## Configuração

Para incluir a biblioteca em seu projeto, basta fazer o seguinte:

```php
<?php
require('vendor/autoload.php');

$mk = new MemberKit\Client('SUA_CHAVE_DE_API');
```

E então, você pode poderá utilizar o cliente para fazer requisições ao MemberKit, com base na documentação de integração da API disponível em:
https://gist.github.com/rainerborene/26bc6b66bbc5dd4f78a1141df31ef718

## Transações

Nesta seção será explicado como utilizar transações da API do MemberKit utilizando o SDK.

### Lista todas as assinaturas ativas na área de membros.

```php
<?php
$test = $mk->membership_levels();
```

### Lista todas as turmas ativas na área de membros.

```php
<?php
$test = $mk->classrooms();
```

### Lista os membros e respectivas pontuações acumuladas no ranking.

| Parâmetro    | Descrição            |
|--------------|----------------------|
| classroom_id | ID da Turma. |

```php
<?php
$test = $mk->rankings($classrom_id);
```

### Lista as pontuações de um membro específico.

| Parâmetro    | Descrição            |
|--------------|----------------------|
| user_id | ID do Usuário do Aluno |

```php
<?php
$test = $mk->user_ranking($user_id);
```

### Gera um novo token de autenticação com duração de 1 hora.

| Parâmetro    | Descrição            |
|--------------|----------------------|
| email |Endereço de email do aluno |

```php
<?php
$test = $mk->token($email);
```

### Cadastrar ou Atualizar um Aluno

 **Importante:** Há três níveis de acesso na Memberkit: matrículas individuais, acesso ilimitado ou assinatura. Ao enviar sua requisição, use apenas uma modalidade de inscrição usando o parâmetro `classroom_ids`, `unlimited` ou `membership_level_id`.

| Parâmetro           | Descrição                                                                   |
|---------------------|-----------------------------------------------------------------------------|
| full_name           | Nome completo do aluno                                                      |
| email               | Endereço de email do aluno                                                  |
| status              | Use `inactive` para inativo, `pending` para pendente ou `active` como ativo |
| blocked             | Bloqueio global de acesso na área de membros, sendo `true` ou `false`       |
| classroom_ids       | IDs de turmas separado por vírgula                                          |
| unlimited           | Acesso ilimitado, sendo `true` ou `false`                                   |
| membership_level_id | Código de assinatura                                                        |
| expires_at          | Data de expiração da matricula (ex: 13/12/2020)                             |


```php
<?php
$test = $mk->newUser($full_name, $email, $status='active', $blocked=false, $classroom_ids=array(), $unlimited=false, $membership_level_id=null, $expires_at=null);
```

### Adiciona uma nova pontuação.

Para adicionar manualmente uma pontuação ao ranking de um aluno.

| Parâmetro   | Descrição                                       |
|-------------|-------------------------------------------------|
| user_email  | Email do aluno                                  |
| reason      | Motivo da pontuação (ex: Postagem no instagram) |
| value       | Quantidade de pontos adicionados                |
| course_id   | ID do curso                                     |


```php
<?php
$test = $mk->scores($user_email, $reason, $value, $course_id);
```

### Remove a pontuação adicionada manualmente

Para remover uma pontuação adicionada manualmente ao ranking de um aluno, você deve passar o mesmo motivo de quando foi adicionada (campo reason).

| Parâmetro   | Descrição                                       |
|-------------|-------------------------------------------------|
| user_email  | Email do aluno                                  |
| reason      | Motivo da pontuação (ex: Postagem no instagram) |
| course_id   | ID do curso                                     |


```php
<?php
$test = $mk->delete_scores($user_email, $reason, $course_id);
```

### Marca todas as aulas de um curso como não concluídas.

Para marcra todas as aulas de um curso como não concluídas para um aluno em específico.

| Parâmetro   | Descrição                                       |
|-------------|-------------------------------------------------|
| user_email  | Email do aluno                                  |
| course_id   | ID do curso                                     |

```php
<?php
$test = $mk->delete_lesson_statuses($user_email, $course_id);
```
