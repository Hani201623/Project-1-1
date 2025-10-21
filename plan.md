# Project 1 — Plan (HW2-style)

## Milestones
- **M1 (Day 1):** Environment ready (PHP 8, SQLite). Run `php -S 0.0.0.0:8000 -t code`.
- **M2 (Day 1):** Base routes: `/api/health`, `/api/items` (GET), `/api/items/search`.
- **M3 (Day 2):** CRUD for items (POST with Bearer, `GET /items/{id}`, PUT, DELETE).
- **M4 (Day 2):** Validation & Security (JSON-only, PDO prepared, CORS allowlist).
- **M5 (Day 3):** Tests (curl commands) + README snippet.
- **M6 (Day 3):** NGINX notes (php-fpm), health check.
- **M7 (Day 3):** Marp slides → export to **PDF**.

## Tasks & Owner
- API & DB schema — Hani K.
- Implement routes & auth — Hani K.
- curl tests — Hani K.
- NGINX notes — Hani K.
- Slides — Hani K.

## Risks & Mitigation
- DB issues → default to SQLite file; MySQL optional via env vars.
- Time crunch → scope to one resource `items`.
- CORS errors → allow localhost only in dev; prefer HTTPS in deploy.
