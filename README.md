# The WIFI Scotland Group

Volunteer-led PHP + MySQL site campaigning for affordable, reliable connectivity as essential infrastructure—Scotland-focused, with a global spotlight on community models.

## Requirements

- PHP 8.1+ (8.2+ recommended) with PDO MySQL
- MySQL 8+ or MariaDB 10.5+

## Local setup

1. **Environment file** — copy the example and edit values for your machine:

   ```bash
   cp .env.example .env
   ```

   Set `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` to match the database user you use in **phpMyAdmin** (or your MySQL client). `APP_BASE_URL` can be `http://127.0.0.1:8080` for the built-in server, or left empty for relative URLs. Use `APP_ENV=local` while developing.

   **MAMP:** If phpMyAdmin is at `http://localhost:8888/...`, MySQL usually listens on **`127.0.0.1:8889`**, not `3306`. Set `DB_PORT=8889` (and the same `DB_USER` / `DB_PASSWORD` you use to log into phpMyAdmin).

2. **Create a database** (for example `wifi_group`) in phpMyAdmin or:

   ```bash
   mysql -u root -p -e "CREATE DATABASE wifi_group CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

3. **Import the schema** (creates tables and seeds starter news articles):

   - In phpMyAdmin: select the database → **Import** → choose `schema.sql`, or  
   - From the shell:

     ```bash
     mysql -u root -p wifi_group < schema.sql
     ```

4. **Run PHP’s built-in server** from the **project root** (same folder as `index.php`):

   ```bash
   php -S 127.0.0.1:8080
   ```

   Open `http://127.0.0.1:8080/`.

On production hosts you can set the same variables in the panel or process environment instead of shipping a `.env` file; the app reads `$_ENV` / `getenv` after optionally loading `.env`.

If a `vendor/` directory exists from an older setup, you can delete it locally; it is not used anymore and remains listed in `.gitignore`.

## Project layout

- **Web root** (this repo root): `index.php`, other `*.php` pages, `css/`, `images/`, `.htaccess`
- `includes/` — shared bootstrap (PDO, CSRF helpers) and layout partials (**blocked from direct web access** via `includes/.htaccess` on Apache)
- `schema.sql` — database tables and seed data
- `.env.example` — documented environment variables (safe to commit)
- `.env` — your local or deployment secrets (**gitignored**; never commit)

## Hostinger (fixed `public_html` root)

Plans that **cannot** change the document root should upload the **contents of this repository** into **`public_html`** so that **`public_html/index.php`** exists (not nested inside another `public/` folder).

- Place **`includes/`** as **`public_html/includes/`** (sibling of `index.php`).
- Place **`.env`** as **`public_html/.env`** (or use hPanel environment variables). The root `.htaccess` tries to block HTTP access to `.env` on Apache.
- Set **`APP_BASE_URL`** in `.env` to your live site URL (e.g. `https://grey-frog-720279.hostingersite.com`) so Open Graph image URLs resolve correctly.

## Security notes for production

- **`includes/`** must not be directly executable as public URLs; keep `includes/.htaccess` in place on Apache.
- Keep database credentials out of version control (use `.env` locally or host-managed env vars) and restrict file permissions on any secrets file.
- News article bodies in the database are rendered as HTML on `news-item.php`. The seed content is trusted; if you allow untrusted authors to post, add HTML sanitisation.
- Add HTTPS, rate limiting, and outbound email or ticketing integration as appropriate—this starter stores messages in MySQL only.

## Sitemap (implemented routes)

| Path | Purpose |
|------|---------|
| `/index.php` | Home |
| `/about.php` | About the campaign |
| `/scotland.php` | Scotland policy snapshot + official links |
| `/news.php` | News listing (from `news_items`) |
| `/news-item.php?slug=…` | Single article |
| `/get-involved.php` | Activist suggestions |
| `/join.php` | Member signup form → `member_signups` |
| `/global-spotlight.php` | International examples |
| `/resources.php` | Curated reference links |
| `/contact.php` | Contact form → `contact_messages` |
| `/credits.php` | Image licences and Unsplash references |
| `/privacy.php` | Privacy summary |

## Database tables

| Table | Purpose |
|-------|---------|
| `member_signups` | Join form submissions |
| `contact_messages` | Contact form submissions |
| `news_items` | Optional editorial posts (seeded) |
