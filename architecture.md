##  Decisões Técnicas e Arquiteturais

Este projeto foi desenvolvido com base nos princípios de Domain-Driven Design (DDD). A escolha dessa arquitetura surgiu após a análise da complexidade da principal regra de negócio do sistema: a transferência de valores.

A partir dessa análise, foi possível identificar e separar claramente os subdomínios envolvidos:

- **Subdomínio Principal (Core Domain):** Transfer
- **Subdomínio de Suporte:** Wallet
- **Subdomínio Genérico:** Customer

Cada subdomínio foi organizado em seu próprio Bounded Context, permitindo uma melhor separação de responsabilidades, maior clareza do domínio e evolução independente das regras de negócio.

---

##  Arquitetura e Organização

A arquitetura foi escolhida justamente para lidar com regras de negócio mais complexas, como validações de saldo, autorização de transferências, controle de estados da transação e possíveis falhas no processo.  
O uso de DDD ajudou a manter o domínio expressivo, evitando lógica espalhada em camadas técnicas.

Durante o desenvolvimento, foram aplicados alguns padrões de projeto, com foco em desacoplamento e flexibilidade:

- **Adapter:** para integração com serviços externos ou camadas técnicas
- **Factory:** para centralizar a criação de objetos complexos do domínio
- **Memento:** utilizado para manter um backup do estado da transação, permitindo restaurar o estado anterior em caso de falha

---

##  CQRS (Command Query Responsibility Segregation)

Neste projeto, foram aplicados os princípios de CQRS de forma simples e pragmática.

As operações que representam comandos, ou seja, ações que alteram o estado do sistema (como a criação de uma transferência), não retornam dados. Essas operações têm como responsabilidade apenas executar a ação e indicar sucesso ou falha por meio de exceções.

Já as operações de consulta (queries) são responsáveis exclusivamente por retornar dados, especialmente quando há necessidade de acompanhar ou visualizar uma mudança de estado, como o status de uma transferência.

Essa separação ajuda a:

- Deixar mais claro o propósito de cada operação
- Evitar acoplamento entre escrita e leitura
- Facilitar a evolução e o escalonamento do sistema no futuro

O CQRS foi adotado como um princípio arquitetural, e não como uma implementação complexa, mantendo a simplicidade e a clareza do projeto.

---

## Geração de Identificadores Fora do Banco de Dados

Outra decisão arquitetural importante foi a geração dos identificadores das entidades fora do banco de dados, sem depender de IDs auto-incrementais.

Os IDs são gerados pela própria aplicação, o que traz alguns benefícios importantes:

- Permite criar e manipular entidades antes da persistência no banco de dados
- Facilita o uso de armazenamentos intermediários, como Redis, antes de uma gravação definitiva em um banco relacional
- Ajuda a suportar cenários de alta concorrência, evitando contenção em sequências do banco
- Torna mais simples a estratégia de sharding e distribuição de dados entre múltiplos bancos

Essa decisão contribui diretamente para a escalabilidade do sistema e reduz o acoplamento entre o domínio e o mecanismo de persistência.

---

##  SOLID e Desacoplamento

Os princípios do SOLID foram considerados desde o início do projeto.  
As dependências entre camadas são feitas por meio de interfaces, o que traz benefícios como:

- Facilidade na criação de testes unitários
- Redução de acoplamento entre domínio e infraestrutura
- Possibilidade de troca de tecnologias (ex: banco de dados ou serviços externos) com impacto mínimo no domínio

---

##  Testes

Os testes foram escritos utilizando PHPUnit, seguindo o padrão GIVEN / WHEN / THEN, o que torna os cenários mais legíveis e próximos da linguagem de negócio.

Para os testes unitários, foram utilizados mocks, garantindo que cada teste valide apenas o comportamento da unidade em questão, sem dependência de implementações externas.

Todo o projeto foi desenvolvido seguindo a abordagem TDD (Test-Driven Development), utilizando *baby steps*, o que ajudou a manter o código simples, testável e evolutivo.

---

## Tratamento de Valores Monetários

Para evitar problemas comuns relacionados a cálculos com números decimais, especialmente em operações financeiras, foi adotada a seguinte estratégia:

- Internamente, todos os valores são tratados como inteiros (ex: centavos)
- Para o cliente ou camada de apresentação, os valores são convertidos para formato decimal

Essa abordagem garante maior precisão nos cálculos e evita erros de arredondamento.

---

## Tratamento de Erros

Foram criadas exceções específicas para cada tipo de erro, permitindo um tratamento mais claro e previsível das falhas, tanto no domínio quanto nas camadas superiores da aplicação.

Isso melhora a legibilidade do código e facilita o entendimento dos fluxos de erro.
