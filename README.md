# events-service

## Overview

Events and map-routes microservice.

## Contents

- `src/` — events and routes API logic.
- `migrations/` — database migrations.
- `tests/` — PHPUnit test suite.

## Run (in stack)

```bash
docker compose -f ../../my-dashboard-docker/docker-compose.yml up -d events-php
```

## Common Operations

```bash
# Migrations
docker compose -f ../../my-dashboard-docker/docker-compose.yml exec -T events-php php bin/console doctrine:migrations:migrate --no-interaction

# Tests
docker compose -f ../../my-dashboard-docker/docker-compose.yml exec -T events-php php bin/phpunit
```
