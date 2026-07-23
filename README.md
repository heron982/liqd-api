# Compra e venda de BTC

API Laravel para auth, carteira, preço fake de BTC, compra/venda e histórico.

## Endpoints

Todos prefixados com `/api`.

| Método | Rota | Auth |
|--------|------|------|
| POST | `/register` | não |
| POST | `/login` | não |
| GET | `/me` | sim |
| GET | `/wallet` | sim |
| GET | `/market/btc` | sim |
| POST | `/trade/buy` | sim |
| POST | `/trade/sell` | sim |
| GET | `/transactions` | sim |

## Estrutura

```text
.
├── docker-compose.yml
├── docker/
│   ├── nginx/                 # config do reverse proxy
│   └── php/                   # imagem PHP-FPM
├── routes/api.php
├── database/migrations/
└── app/Modules/               # domínio da aplicação
```

Cada módulo agrupa um domínio. As ações ficam em `Features/` (slices como `Buy/`, `Login/`, …) com Controller, Service, Request e DTO. Model, Repository, Enum e DTOs compartilhados ficam no nível do módulo.

```text
app/Modules/
├── Auth/
│   ├── Features/
│   │   ├── Login/             Controllers, Services, Requests, Dtos
│   │   ├── Register/          Controllers, Services, Requests, Dtos
│   │   └── Me/                Controllers
│   └── Repositories/          UserRepository
├── Wallet/
│   ├── Features/
│   │   └── Show/              Controllers, Services, Dtos
│   ├── Models/                Wallet
│   └── Repositories/          WalletRepository
├── Market/
│   └── Features/
│       └── BtcPrice/          Controllers, Services, Dtos
├── Trade/
│   ├── Features/
│   │   ├── Buy/               Controllers, Services, Requests, Dtos
│   │   └── Sell/              Controllers, Services, Requests, Dtos
│   └── Dtos/                  DTO compartilhado da resposta de trade
├── Transaction/
│   ├── Features/
│   │   └── Index/             Controllers, Services
│   ├── Models/                Transaction
│   ├── Repositories/          TransactionRepository
│   ├── Enums/                 TransactionType
│   └── Dtos/                  CreateTransactionDto, TransactionResponseDto
└── Shared/
    └── Helpers/               MoneyHelper, CacheHelper
```

Exemplo de um slice de ação (`Trade/Features/Buy`):

```text
Trade/Features/Buy/
├── Controllers/BuyController.php
├── Services/BuyService.php
├── Requests/BuyRequest.php
└── Dtos/BuyResultDto.php
```


## Padrões

Fluxo de uma request:

`Request → Controller → Service → Repository/Helper → resposta (DTO)`

- **Controller** — só HTTP
- **Service** — regra de negócio
- **Repository** — isolamento do Eloquent
- **DTO** — contrato tipado dos dados que circulam entre as camadas (Spatie Laravel Data)

Método público único nos controllers/services: `execute`.

Princípios usados:

- **SoC** — HTTP, regra e persistência em camadas distintas
- **SRP** — uma ação por slice (`BuyService`, `SellService`, …)
- **DIP (leve)** — o service depende de repository/helper, não de detalhe HTTP nem de query solta na regra
