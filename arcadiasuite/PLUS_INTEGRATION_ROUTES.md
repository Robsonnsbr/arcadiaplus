# Arcadia ⇄ Plus Integration Routes

## Base URLs
- Arcadia proxy: `/plus/*`
- Plus backend direct (server-side Arcadia): `PLUS_BASE_URL` (`server/plus/client.ts`)

## Routes consumed by Arcadia

### From Arcadia server (`plusClient`)
- `GET /api/health`
- `GET /api/suite/empresas`
- `GET /api/clientes?page={page}`
- `POST /api/clientes`
- `PUT /api/clientes/{id}`
- `POST /api/fornecedores`
- `GET /api/suite/produtos?page={page}`
- `POST /api/vendas`
- `GET /api/estoque`
- `GET /api/contas-receber`
- `POST /api/suite/nfe/emitir`
- `POST /api/suite/nfce/emitir`
- `GET /api/graficos/dados-cards`
- `GET /api/graficos/grafico-vendas-mes`
- `GET /api/graficos/grafico-conta-receber`
- `GET /api/graficos/grafico-conta-pagar`

### From Arcadia frontend (via proxy `/plus`)
- `GET /api/suite/stats`
- `GET /api/suite/planos`
- `POST /api/suite/planos`
- `PUT /api/suite/planos/{id}`
- `DELETE /api/suite/planos/{id}`
- `GET /api/suite/empresas`
- `POST /api/suite/empresas`
- `PUT /api/suite/empresas/{id}`
- `DELETE /api/suite/empresas/{id}`
- `GET /api/suite/usuarios`
- `GET /api/suite/clientes`
- `GET /api/suite/fornecedores`
- `GET /api/suite/produtos`
- `GET /api/suite/vendas`
- `GET /api/suite/dashboard`

## Laravel side
- New routes are declared in `ControlPLUS/routes/api.php` without auth middleware.
- New controllers:
  - `App\Http\Controllers\API\ArcadiaPlus\IntegrationController`
  - `App\Http\Controllers\API\ArcadiaPlus\SuiteController`

These endpoints are mock-compatible and return JSON expected by Arcadia.
These endpoints now read/write real ControlPLUS database data (no cache mocks).
