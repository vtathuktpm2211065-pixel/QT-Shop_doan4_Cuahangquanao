<?php
// tools/check_admin.php

function readDotEnv($path)
{
    if (!file_exists($path)) {
        return [];
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (!str_contains($line, '=')) continue;
        [$k, $v] = array_map('trim', explode('=', $line, 2));
        $v = trim($v, "\"'");
        $data[$k] = $v;
    }
    return $data;
}

$env = readDotEnv(__DIR__ . '/../.env');
$dbConnection = getenv('DB_CONNECTION') ?: ($env['DB_CONNECTION'] ?? 'sqlite');

try {
    if ($dbConnection === 'mysql') {
        $host = getenv('DB_HOST') ?: ($env['DB_HOST'] ?? '127.0.0.1');
        $port = getenv('DB_PORT') ?: ($env['DB_PORT'] ?? '3306');
        $database = getenv('DB_DATABASE') ?: ($env['DB_DATABASE'] ?? null);
        $username = getenv('DB_USERNAME') ?: ($env['DB_USERNAME'] ?? 'root');
        $password = getenv('DB_PASSWORD') ?: ($env['DB_PASSWORD'] ?? '');

        if (empty($database)) {
            echo "ERROR: DB_DATABASE not set in environment or .env\n";
            exit(1);
        }

        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);
        $db = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    } else {
        // default: sqlite
        $dbPath = __DIR__ . '/../database/database.sqlite';
        if (!file_exists($dbPath)) {
            echo "ERROR: database file not found at $dbPath\n";
            exit(1);
        }
        $db = new PDO('sqlite:' . $dbPath);
    }

    $stmt = $db->query("SELECT id,email,username,banned,password FROM users WHERE username='admin' OR email='admin@gmail.com';");
    $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    if (empty($rows)) {
        echo "NOT_FOUND\n";
    } else {
        foreach ($rows as $r) {
            echo "FOUND\n";
            echo "id: " . ($r['id'] ?? '') . "\n";
            echo "email: " . ($r['email'] ?? '') . "\n";
            echo "username: " . ($r['username'] ?? '') . "\n";
            echo "banned: " . ($r['banned'] ?? '') . "\n";
            echo "password (stored): " . ($r['password'] ?? '') . "\n";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
