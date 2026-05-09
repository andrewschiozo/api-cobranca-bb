# BB Cobranças SDK (PHP 8.4+)

**Biblioteca desacoplada e testada para integração com a API de Boletos e Cobranças do Banco do Brasil.**

![PHP 8.4](https://img.shields.io/badge/PHP%208.4-%23777BB4.svg?&logo=php&logoColor=white)
![License](https://img.shields.io/badge/GPL--3.0-bf0808.svg?&logo=gnu&logoColor=white)
![Logo Banco do Brasil](https://img.shields.io/badge/BANCO%20DO%20BRASIL-ffed00.svg)


Criada com foco em **desacoplamento** e **testabilidade**, esta lib implementa a lógica do Banco do Brasil isolada de qualquer framework PHP.

Projetada em **Arquitetura Hexagonal (Ports and Adapters)** para garantir que você possa trocar a camada de comunicação HTTP ou o Logger sem precisar tocar na lógica de negócio do BB (Domínio).

De lambuja, ainda entrega uma implementação de httpclient funcional, um container que entrega os adapters mínimos e um serviço de fachada pra executar todos os usecases.

Isso não é uma LIB, é uma MÃE.

---
### Recursos disponíveis

#### Registro
- Data de vencimento
- Valor do título
- Nosso número
- Dados do Pagador
- - Número do documento
- - Nome
- - Endereço (logradouro, cep, cidade, bairro, uf)
- - Contato (telefone, email)

### Consulta
- Consulta de boletos pelo nosso número, retornando todos os dados do boleto

### Alteração (instrução)
- Data de vencimento
- Valor do título

#### Autenticação
- Integração oAuth para obtenção do access_token

### Observações iniciais
O uso da configuração pelos arquivos `.env` e `src/Infrastructure/Bootstrap/container.php` é totalmente **OPCIONAL**, você pode criar instanciar as classes manualmente, criar seu próprio container ou configurar um container já existente.


### Instalação

```bash
composer install
```

### Testes
```bash
./vendor/bin/phpunit
```

### Referências
[Documentação oficial do Banco do Brasil](https://apoio.developers.bb.com.br/apis/5?versaoApi=2)

### ToDo
- Corrigir Testes existentes
- Criar Testes Unitários p/ os Formatters e ValueObjects
- Implementar todas as opções de registro do boleto
- Implementar todas as opções de alteração do boleto
- Substituir retornos em array por retornos tipados (DTOs) p/ melhor previsibilidade
