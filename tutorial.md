---
marp: true
size: 4:3
paginate: true
title: Project 1 Tutorial — REST API, Security, NGINX
---

# Project 1 Tutorial
## Build • Secure • Deploy

---

### Run locally
```bash
php -S 0.0.0.0:8000 -t code
```


---

### 7) Auth (Bearer) & SQLite

- Set token (optional): `export API_TOKEN='devtoken123'`
- Mutating routes require header: `Authorization: Bearer $API_TOKEN`
- SQLite DB auto-creates at `code/data.sqlite`

Test:
```bash
export API_TOKEN=devtoken123
curl -s -X POST http://localhost:8000/api/items   -H "Authorization: Bearer $API_TOKEN" -H "Content-Type: application/json"   -d '{"name":"Cheese","note":"aged"}'
```

---

### 8) Extra endpoints to reach 8+

- `GET /api/version`
- `GET /api/stats`
- `GET /api/items/search?q=milk`
