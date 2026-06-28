# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Local development

```bash
# 1. Copy and edit env values (DB credentials, APP_BASE_URL)
cp .env.example .env

# 2. Import schema and seed data
mysql -u root -p wire_scotland < schema.sql

# 3. Run the built-in PHP server from the repo root
php -S 127.0.0.1:8080
```

**MAMP users:** MySQL listens on `127.0.0.1:8889`, not `3306`. Set `DB_PORT=8889`.

There is no build step, bundler, or test runner. The site is vanilla PHP — edit a file and reload.

## Architecture

The site is a flat collection of PHP pages (one file per route) at the repo root. Each page follows this pattern:

```php
<?php
declare(strict_types=1);
// Set page-level variables consumed by the partials:
$pageTitle = '...';
$pageDescription = '...';
$currentNav = 'pageid';     // matches 'id' keys in header.php $navStructure
$pageOgImage = '/images/x.jpg';  // optional, omit if not needed
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/header.php';
// ... page HTML ...
require_once __DIR__ . '/includes/footer.php';
```

`includes/bootstrap.php` is the single entry point that loads `.env`, connects to MySQL (lazily, via `db()`), and defines all shared helpers. It must be required before `header.php`.

## Key helpers (defined in `includes/bootstrap.php`)

| Helper | Purpose |
|--------|---------|
| `e(?string $s): string` | HTML-escape for output — use on all user-supplied or DB-sourced strings |
| `db(): PDO` | Lazy singleton PDO connection (throws on failure) |
| `db_available(): bool` | Non-throwing check; used to degrade gracefully when DB is absent |
| `csrf_token(): string` | Generate/return session CSRF token |
| `csrf_validate(mixed $token): bool` | Validate POST token; always call before processing form data |
| `flash_set(string $key, string $msg)` / `flash_take(string $key): ?string` | One-shot session flash messages |
| `base_url(string $path): string` | Prepends `APP_BASE_URL` if set; returns absolute URL |
| `absolute_url_for_path(string $path): ?string` | Returns null when `APP_BASE_URL` is unset (Open Graph only) |
| `external_link_attrs(string $href): string` | Returns `target="_blank" rel="noopener noreferrer"` for off-site links |
| `image_asset(string $filename): string` | Validates filename, returns `/images/{filename}` |

## WiFi map page

`wifi-map.php` uses **Leaflet** with a Scotland council-area GeoJSON overlay (`data/scotland-council-areas.min.geojson`). Stats are fetched from `api/wifi-map-stats.php`, which reads `data/wifi-area-stats.json` — no database involved. Map interaction logic lives in `js/wifi-map.js`.

## Database

Three tables (`member_signups`, `contact_messages`, `news_items`). Forms use PDO prepared statements. News item bodies are stored as trusted HTML (no untrusted author input currently); if that changes, add sanitisation before rendering on `news-item.php`.

## Deployment (Hostinger)

Deploy repo contents directly into `public_html/` — `public_html/index.php` must exist at the top level. Set `.env` there or use hPanel environment variables. The `includes/.htaccess` blocks direct HTTP access to the `includes/` directory; keep it in place.

## Design Context

### Users
Three audiences in equal measure: general public in Scotland (curious or frustrated residents), activists and community organisers (looking to connect with the campaign), and policymakers/journalists (needing credible evidence and citable sources). Everyone arrives with intent — nobody is browsing idly.

### Brand Personality
**Three words: Urgent. Credible. Rooted.** This is a rights issue, not a charity appeal. The campaign argues connectivity is infrastructure like roads and water. Voice is plain-spoken and direct — respects the reader, doesn't plead, argues. Emotional goal: make visitors feel the issue is serious, the campaign is trustworthy, and joining is the obvious next step.

### Aesthetic Direction
**Editorial / magazine meets protest poster.** References: Positive Money, 38 Degrees — modern campaigning orgs with design confidence. The headline IS the design; typography carries authority. Ink-heavy over pastel-light. Editorial layout variety (pull quotes, graphic bands, asymmetric sections) over uniform card grids. One graphic identity moment people remember. Light mode primary.

### Design Principles
1. **The headline is the hero.** Every page opens with a statement that makes you stop.
2. **Urgency without panic.** Confident directness over breathless scrollbait.
3. **Serve three audiences without alienating any.** Navigation makes the right path obvious for resident, organiser, or policy researcher.
4. **Earn credibility through specificity.** Real data, real places, real stories — framed so evidence lands.
5. **Every interaction feels like forward motion.** The user always knows what to do next and feels pulled toward it.
