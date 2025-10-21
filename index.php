<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allow = ['http://localhost:3000', 'http://127.0.0.1:3000', 'http://localhost:8080'];
if (in_array($origin, $allow, true)) { header("Access-Control-Allow-Origin: $origin"); header("Vary: Origin"); }
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

function env(string $k, ?string $d=null): ?string { return $_ENV[$k] ?? getenv($k) ?: $d; }

class DB {
  private static ?\PDO $pdo = null;
  public static function pdo(): \PDO {
    if (self::$pdo) return self::$pdo;
    if (env('MYSQL_HOST')) {
      $dsn = "mysql:host=".env('MYSQL_HOST').";dbname=".env('MYSQL_DB','project1').";charset=utf8mb4";
      self::$pdo = new \PDO($dsn, env('MYSQL_USER','root'), env('MYSQL_PASSWORD',''), [\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION]);
    } else {
      self::$pdo = new \PDO('sqlite:' . __DIR__ . '/data.sqlite', null, null, [\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION]);
    }
    self::$pdo->exec("CREATE TABLE IF NOT EXISTS items (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, note TEXT DEFAULT '', created_at TEXT NOT NULL)");
    return self::$pdo;
  }
}

function json_input(): array { $raw = file_get_contents('php://input') ?: ''; $d = json_decode($raw, true); return is_array($d)?$d:[]; }
function respond($d, int $s=200): void { http_response_code($s); echo json_encode($d, JSON_UNESCAPED_SLASHES); exit; }
function requires_auth(): void {
  $h = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
  if (stripos($h, 'Bearer ') !== 0) respond(['error'=>'Missing bearer token'], 401);
  $t = trim(substr($h, 7)); $exp = env('API_TOKEN', 'devtoken123');
  if (!hash_equals($exp, $t)) respond(['error'=>'Invalid token'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];
$path = strtok($_SERVER['REQUEST_URI'], '?');

if ($path === '/api/health' && $method === 'GET') respond(['status'=>'ok','time'=>gmdate('c')]);
if ($path === '/api/version' && $method === 'GET') respond(['version'=>'1.0.0','php'=>PHP_VERSION]);
if ($path === '/api/stats' && $method === 'GET') { $c = DB::pdo()->query("SELECT COUNT(*) c FROM items")->fetch(\PDO::FETCH_ASSOC); respond(['items_count'=>(int)($c['c']??0)]); }
if ($path === '/api/items' && $method === 'GET') { $st=DB::pdo()->query("SELECT * FROM items ORDER BY id DESC"); respond(['items'=>$st->fetchAll(\PDO::FETCH_ASSOC)]); }
if ($path === '/api/items/search' && $method === 'GET') { $q=$_GET['q']??''; $q="%".str_replace(['%','_'],['\\%','\\_'],$q)."%"; $st=DB::pdo()->prepare("SELECT * FROM items WHERE name LIKE ? ESCAPE '\\' OR note LIKE ? ESCAPE '\\' ORDER BY id DESC"); $st->execute([$q,$q]); respond(['items'=>$st->fetchAll(\PDO::FETCH_ASSOC)]); }
if (preg_match('#^/api/items/(\d+)$#',$path,$m)) {
  $id=(int)$m[1];
  if ($method==='GET') { $st=DB::pdo()->prepare("SELECT * FROM items WHERE id=?"); $st->execute([$id]); $it=$st->fetch(\PDO::FETCH_ASSOC); if(!$it) respond(['error'=>'Not found'],404); respond($it); }
  if ($method==='PUT') { requires_auth(); $b=json_input(); $name=trim((string)($b['name']??'')); $note=trim((string)($b['note']??'')); if($name===''||strlen($name)>120) respond(['error'=>'Invalid name'],422); $st=DB::pdo()->prepare("UPDATE items SET name=?, note=? WHERE id=?"); $st->execute([$name,$note,$id]); respond(['updated'=>true]); }
  if ($method==='DELETE') { requires_auth(); $st=DB::pdo()->prepare("DELETE FROM items WHERE id=?"); $st->execute([$id]); respond(['deleted'=>true]); }
}
if ($path === '/api/items' && $method === 'POST') { requires_auth(); $b=json_input(); $name=trim((string)($b['name']??'')); $note=trim((string)($b['note']??'')); if($name===''||strlen($name)>120) respond(['error'=>'Invalid name'],422); $st=DB::pdo()->prepare("INSERT INTO items(name,note,created_at) VALUES(?,?,?)"); $st->execute([$name,$note,gmdate('c')]); respond(['id'=>(int)DB::pdo()->lastInsertId(),'name'=>$name,'note'=>$note],201); }

respond(['error'=>'Route not found','path'=>$path],404);

// Item by ID: GET / PUT (auth) / DELETE (auth)
if (preg_match('#^/api/items/(\d+)$#', $path, $m)) {
  $id = (int)$m[1];

  if ($method === 'GET') {
    $stmt = DB::pdo()->prepare("SELECT * FROM items WHERE id=?");
    $stmt->execute([$id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$item) respond(['error' => 'Not found'], 404);
    respond($item);
  }

  if ($method === 'PUT') {
    requires_auth();
    $b = json_input();
    $name = trim((string)($b['name'] ?? ''));
    $note = trim((string)($b['note'] ?? ''));
    if ($name === '' || strlen($name) > 120) respond(['error' => 'Invalid name'], 422);
    $stmt = DB::pdo()->prepare("UPDATE items SET name=?, note=? WHERE id=?");
    $stmt->execute([$name, $note, $id]);
    respond(['updated' => true]);
  }

  if ($method === 'DELETE') {
    requires_auth();
    $stmt = DB::pdo()->prepare("DELETE FROM items WHERE id=?");
    $stmt->execute([$id]);
    respond(['deleted' => true]);
  }
}
