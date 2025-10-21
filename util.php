<?php
// util.php - helpers (JSON, auth)
function json_body() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function send_json($payload, $status=200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
    exit;
}

// Simple Bearer token check for demo purposes
function require_token() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        send_json(["error" => "Missing Authorization header"], 401);
    }
    $auth = $headers['Authorization'];
    if (strpos($auth, 'Bearer ') !== 0) {
        send_json(["error" => "Invalid Authorization header"], 401);
    }
    $token = substr($auth, 7);
    $valid = getenv('API_TOKEN') ?: 'DEMO_TOKEN_123';
    if ($token !== $valid) {
        send_json(["error" => "Invalid token"], 403);
    }
}
?>
