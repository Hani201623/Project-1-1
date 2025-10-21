<?php
// index.php - Vanilla PHP REST API router for 'products' and 'users' (demo)
// Endpoints (8 total):
// 1. GET  /products
// 2. GET  /products/{id}
// 3. POST /products           (body: name, price)
// 4. PUT  /products/{id}      (body: name?, price?)
// 5. DELETE /products/{id}
// 6. GET  /health
// 7. POST /auth/login         -> returns demo bearer token
// 8. GET  /user/profile       (Bearer protected)
// + Bonus (Bearer protected admin list): GET /admin/metrics

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/util.php';

$method = $_SERVER['REQUEST_METHOD'];
$path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts  = array_values(array_filter(explode('/', $path)));
// Basic CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($method === 'OPTIONS') {
    http_response_code(204); exit;
}

// route helper
function match($parts, $pattern) {
    // $pattern like ['products', '{id}']
    if (count($parts) !== count($pattern)) return false;
    $params = [];
    for ($i=0; $i<count($pattern); $i++) {
        if (preg_match('/^{.+}$/', $pattern[$i])) {
            $key = trim($pattern[$i], '{}');
            $params[$key] = $parts[$i];
        } else if ($pattern[$i] !== $parts[$i]) {
            return false;
        }
    }
    return $params;
}

// ROUTES
if ($method === 'GET' && $path === '/health') {
    send_json(["status" => "ok"]);
}

if ($method === 'POST' && $path === '/auth/login') {
    $body = json_body();
    $user = $body['username'] ?? '';
    $pass = $body['password'] ?? '';
    // DEMO auth logic
    if ($user === 'demo' && $pass === 'demo') {
        send_json(["token" => (getenv('API_TOKEN') ?: 'DEMO_TOKEN_123')]);
    } else {
        send_json(["error" => "Invalid credentials"], 401);
    }
}

// Bearer-protected endpoints
if ($method === 'GET' && $path === '/user/profile') {
    require_token();
    send_json(["username" => "demo", "role" => "user", "message" => "This is a protected profile endpoint."]);
}

if ($method === 'GET' && $path === '/admin/metrics') {
    require_token();
    send_json(["uptime_sec" => 1234, "requests" => 42, "db" => "ok"]);
}

// /products collection
if ($method === 'GET' && $path === '/products') {
    $stmt = $pdo->query('SELECT id, name, price FROM products ORDER BY id DESC');
    $rows = $stmt->fetchAll();
    send_json($rows);
}

$params = match($parts, ['products', '{id}']);
if ($params !== false) {
    $id = intval($params['id']);
    if ($method === 'GET') {
        $stmt = $pdo->prepare('SELECT id, name, price FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) send_json(["error" => "Not found"], 404);
        send_json($row);
    }
    if ($method === 'PUT') {
        $body = json_body();
        $name = $body['name'] ?? null;
        $price = $body['price'] ?? null;
        $fields = [];
        $vals = [];
        if (!is_null($name)) { $fields[] = 'name = ?'; $vals[] = $name; }
        if (!is_null($price)) { $fields[] = 'price = ?'; $vals[] = $price; }
        if (!$fields) send_json(["error" => "No fields provided"], 400);
        $vals[] = $id;
        $sql = 'UPDATE products SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($vals);
        send_json(["updated" => true]);
    }
    if ($method === 'DELETE') {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        send_json(["deleted" => true]);
    }
}

// POST /products
if ($method === 'POST' && $path === '/products') {
    $body = json_body();
    $name = $body['name'] ?? '';
    $price = $body['price'] ?? 0;
    if (!$name) send_json(["error" => "name required"], 400);
    $stmt = $pdo->prepare('INSERT INTO products(name, price) VALUES (?,?)');
    $stmt->execute([$name, $price]);
    $id = $pdo->lastInsertId();
    send_json(["id" => intval($id), "name" => $name, "price" => floatval($price)], 201);
}

// Fallback
send_json(["error" => "Route not found", "method" => $method, "path" => $path], 404);
?>
