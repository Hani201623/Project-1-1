#!/usr/bin/env bash
set -euo pipefail

API_BASE="${1:-http://localhost}"
TOKEN="DEMO_TOKEN_123"

echo "Health check:"
curl -s "$API_BASE/health" | jq . || true

echo "Create product:"
NEW=$(curl -s -X POST "$API_BASE/products" -H "Content-Type: application/json" -d '{"name":"Milk","price":2.99}')
echo "$NEW" | jq . || true
ID=$(echo "$NEW" | jq -r .id)

echo "Get all:"
curl -s "$API_BASE/products" | jq . || true

echo "Get one:"
curl -s "$API_BASE/products/$ID" | jq . || true

echo "Update:"
curl -s -X PUT "$API_BASE/products/$ID" -H "Content-Type: application/json" -d '{"price":3.49}' | jq . || true

echo "Protected login:"
LOGIN=$(curl -s -X POST "$API_BASE/auth/login" -H "Content-Type: application/json" -d '{"username":"demo","password":"demo"}')
echo "$LOGIN" | jq . || true

echo "Protected profile:"
curl -s "$API_BASE/user/profile" -H "Authorization: Bearer $TOKEN" | jq . || true

echo "Admin metrics:"
curl -s "$API_BASE/admin/metrics" -H "Authorization: Bearer $TOKEN" | jq . || true

echo "Delete:"
curl -s -X DELETE "$API_BASE/products/$ID" | jq . || true
