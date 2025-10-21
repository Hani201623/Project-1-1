---
marp: true
paginate: true
---

# NGINX Deployment — Steps & Proof
**Author:** Hani Kebede

---

## Goal
Deploy the PHP API under NGINX + PHP-FPM

---

## Steps (Ubuntu)

1. Install:
```
sudo apt update
sudo apt install -y nginx php-fpm php-mysql
```
2. Copy code to `/var/www/html`
3. Put `nginx.conf` (server block) into:
```
/etc/nginx/sites-available/project1
sudo ln -s /etc/nginx/sites-available/project1 /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```
4. Create DB and table:
```
mysql -u root -p -e "CREATE DATABASE project1;"
mysql -u root -p project1 < /var/www/html/schema.sql
```

---

## Server Block (snippet)
```nginx
server {
  listen 80;
  server_name _;
  root /var/www/html;
  index index.php index.html;
  location / { try_files $uri /index.php?$query_string; }
  location ~ \.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/run/php/php8.2-fpm.sock;
  }
}
```

---

## Proof to Include (Screenshots)
- Browser `GET /health` shows `{"status":"ok"}`
- `systemctl status nginx` shows active (running)
- `curl http://SERVER/products` returns JSON
- MySQL table `products` exists

*(Add your screenshots as slides here)*

---

## Troubleshooting
- `sudo journalctl -u nginx -f`
- `sudo tail -n 200 /var/log/nginx/error.log`
- PHP-FPM socket path version

---

# End
