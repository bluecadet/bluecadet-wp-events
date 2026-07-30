# Building the docs locally

The docs site has two parts, both output into `.docs-build/` (gitignored):

- **Hooks reference** — `bin/docs/generate-hooks.php` (pure PHP, no dependencies).
- **API reference** — phpDocumentor. It is intentionally **not** a Composer dependency (heavy + conflict-prone), so run it via the PHAR or Docker.

## One-time

```bash
composer install   # provides the autoloader the hooks generator reflects
```

## Build the full site

```bash
composer docs         # API (phpDocumentor via Docker) + Hooks + landing page
composer docs:serve   # preview at http://localhost:8080
```

`composer docs` runs three steps into `.docs-build/`:

| Script | Builds | Needs |
|---|---|---|
| `docs:api` | `api/` (phpDocumentor) | Docker |
| `docs:hooks` | `hooks/` | PHP only |
| `docs:landing` | `index.html` | PHP only |

This is the exact layout the GitHub Pages workflow (`.github/workflows/docs.yml`) publishes.

> **Iterating on just the hooks page?** `composer docs:hooks && composer docs:serve` is enough (no Docker). Note the landing page's **API Reference** link will 404 until you've run a full `composer docs` at least once, since it links into `api/`.

### Prefer a PHAR over Docker for the API?

```bash
curl -fsSL -o phpDocumentor.phar \
  https://github.com/phpDocumentor/phpDocumentor/releases/latest/download/phpDocumentor.phar
php phpDocumentor.phar run -c phpdoc.dist.xml   # in place of `composer docs:api`
```
