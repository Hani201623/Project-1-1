---
marp: true
size: 4:3
paginate: true
title: Project 1 – REST API Foundations
---

# Project 1  
## Secure & Deployable REST API (SQLite + Bearer Tokens)


---

### Authentication (Bearer)
- Protect mutating routes (POST/PUT/DELETE) with `Authorization: Bearer <TOKEN>`
- Env var: `API_TOKEN` (default: `devtoken123`)

### Storage
- Default: **SQLite** file at `code/data.sqlite`
- Optional: switch to MySQL with `MYSQL_*` env vars
