# Project 1 (Fixed Package)

This package includes:
- Minimal PHP REST API with 8 endpoints (plus 2 Bearer-protected)
- MySQL schema
- cURL test script
- HTML tester
- Marp slides for tutorial and NGINX deployment
- Plan and checklist

## Quick Start (Local)
1. Create DB and table:
   ```sql
   CREATE DATABASE project1;
   USE project1;
   SOURCE code/schema.sql;
   ```
2. Serve PHP (e.g., built-in for quick demo):
   ```bash
   php -S 0.0.0.0:80 -t code
   ```
3. Test:
   ```bash
   bash code/curl_tests.sh http://localhost
   ```

For NGINX, see `presentation/NGINX_Deployment.md`.
