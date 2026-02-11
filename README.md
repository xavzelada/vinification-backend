# Gestor de Vinificacion Backend (Symfony + Postgres)

## Requisitos
- PHP >= 7.4
- Composer
- PostgreSQL 14+

## Setup rapido
1) Instalar dependencias
```
composer install
```

2) Configurar variables
Editar `.env` con tu `DATABASE_URL` y `JWT_PASSPHRASE`.

3) Generar llaves JWT
```
mkdir -p config/jwt
openssl genrsa -out config/jwt/private.pem 4096
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

4) Migrar y seed
```
composer run migrate
composer run seed
```

Seed crea:
- Bodega demo `BODEGA-01`
- Admin `admin@bodega.test` / `admin123`

5) Levantar servidor
```
composer run dev
```

## Endpoints clave
- `POST /auth/login`
- `POST /batches/{id}/measurements` (guarda medicion, evalua alertas, recalcula recomendaciones)
- `GET /docs` (Swagger UI)

## Scripts
- `composer run dev`
- `composer run migrate`
- `composer run seed`
- `composer run test`
- `composer run test:ci`

## Nota sobre recomendaciones
Las recomendaciones son heuristicas y explicables. No sustituyen criterio enologico.

## Tests via Docker
```
docker compose --profile test up --abort-on-container-exit test
```
