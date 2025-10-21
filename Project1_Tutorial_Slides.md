---
marp: true
paginate: true
---

# Project 1 — API Tutorial
**Author:** Hani Kebede  
**Course:** ASE 230

---

## Overview
- Stack: PHP + MySQL + NGINX
- Endpoints: 8 total (+2 Bearer protected)
- Goal: CRUD for `products` + auth demo

---

## API List
1. `GET /health` — service status
2. `POST /auth/login` — returns token
3. `GET /user/profile` — protected
4. `GET /admin/metrics` — protected
5. `GET /products` — list
6. `GET /products/{id}` — get one
7. `POST /products` — create
8. `PUT /products/{id}` — update
9. `DELETE /products/{id}` — delete

---

## Request/Response — Examples

### `GET /health`
**Request**
```
GET /health
```
**Response**
```json
{ "status": "ok" }
```

---

### `POST /auth/login`
**Request**
```
POST /auth/login
Content-Type: application/json

{"username":"demo","password":"demo"}
```
**Response**
```json
{ "token": "DEMO_TOKEN_123" }
```

---

### `GET /user/profile` (Bearer)
**Request**
```
GET /user/profile
Authorization: Bearer DEMO_TOKEN_123
```
**Response**
```json
{"username":"demo","role":"user"}
```

---

### `GET /products`
**Request**
```
GET /products
```
**Response**
```json
[{"id":1,"name":"Milk","price":2.99}]
```

---

### `POST /products`
**Request**
```
POST /products
Content-Type: application/json

{"name":"Milk","price":2.99}
```
**Response**
```json
{"id":1,"name":"Milk","price":2.99}
```

---

### `GET /products/{id}`
**Request**
```
GET /products/1
```
**Response**
```json
{"id":1,"name":"Milk","price":2.99}
```

---

### `PUT /products/{id}`
**Request**
```
PUT /products/1
Content-Type: application/json

{"price":3.49}
```
**Response**
```json
{"updated":true}
```

---

### `DELETE /products/{id}`
**Request**
```
DELETE /products/1
```
**Response**
```json
{"deleted":true}
```

---

## DB Schema
```sql
CREATE TABLE products(
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(255) NOT NULL,
 price DECIMAL(10,2) NOT NULL DEFAULT 0.00
);
```

---

## Test Tools
- `code/curl_tests.sh`
- `code/test.html`

**Tip:** Run PHP built-in server for quick checks:
```
php -S 0.0.0.0:80 -t code
```

---

## Notes
- Keep token in env: `API_TOKEN`
- Validate inputs, sanitize outputs
- Use prepared statements (PDO)

---

# End
