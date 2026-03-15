# Copilot Instructions - Events Service

Scope: This repository only (my-dashboard-backend/events-service).

## Stack

- PHP 8.2+, Symfony, Doctrine.

## Rules

- Keep controllers orchestration-only.
- Put branching business rules in dedicated services or policies.
- Preserve idempotent behavior where async interactions are involved.
- Keep OpenAPI/Swagger docs aligned with routes and payloads.
- Do not create cross-service DB shortcuts.

## Quality

- Run service tests after changes: docker compose exec events-php bin/phpunit.
- Validate migrations when data model changes.
