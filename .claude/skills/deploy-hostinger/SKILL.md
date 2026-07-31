---
name: deploy-hostinger
description: Deploy the WIRES site to Hostinger. Use when the user asks to deploy, ship, or push this site live, or asks about public_html layout, .env on the server, or the includes/.htaccess protection.
---

## Deployment (Hostinger)

Deploy repo contents directly into `public_html/` — `public_html/index.php` must exist at the top level. Set `.env` there or use hPanel environment variables. The `includes/.htaccess` blocks direct HTTP access to the `includes/` directory; keep it in place.
