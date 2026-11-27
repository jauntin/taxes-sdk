# Taxes SDK

Laravel package wrap Taxes microservice HTTP API (local API on port 8011). Consumer apps (`gbli-events/backend`, `gatherguard/backend`) install from GitHub VCS.

- v1, `TaxesFacade`: tax calc + municipal location lookup (`/api/v1/taxes/...`).
- v2, `TaxesFacadeV2`: Kentucky LGPT jurisdiction lookup (`/api/v2/ky/lgpt/lookup`).

## Request flow

Facade → `TaxesService*` → `CacheableTaxesClient*` → `TaxesClient*` → Laravel `Http`.

Cacheable client proxy every call through `__call`, cache by method name + serialized args. New public method on `TaxesClient*` cached, no extra code. Facade resolve to service, not client: new endpoint = client method + `TaxesService*` method + facade `@method` line + usage example in `README.md` — README is public API doc for consumers.

## Commands

Run inside app container at `/workspace/taxes-sdk`:

- `composer test`
- `composer analyze` (Pint check, then PHPStan level 8 on `src`)

`composer.lock` and `vendor/` gitignored. "Composer detected issues in your platform" error = `vendor/` built on different PHP. Run `composer update` in container.

## Tests and CI

Tests never hit real service. Fake responses with `Http::fake` in `tests/Mockable.php`.

CI run PHPUnit with both `--prefer-lowest` and `--prefer-stable`. Code must work on lowest versions `composer.json` allow. On PRs, CI run Pint, push fix commit — pull before push again.