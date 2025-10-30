<?php
// Diagnóstico simples de conexão MySQL usando constantes do wp-config.php
@header('Content-Type: text/plain; charset=utf-8');
define('SHORTINIT', true);
require_once __DIR__ . '/wp-config.php';

$host = defined('DB_HOST') ? DB_HOST : '';
$user = defined('DB_USER') ? DB_USER : '';
$pass = defined('DB_PASSWORD') ? DB_PASSWORD : '';
$db   = defined('DB_NAME') ? DB_NAME : '';

$start = microtime(true);
$mysqli = @new mysqli($host, $user, $pass, $db);
$elapsed = round((microtime(true) - $start) * 1000);

echo "Trying to connect to MySQL...\n";
echo "Host: $host\nDB: $db\nUser: $user\n";

if ($mysqli && !$mysqli->connect_errno) {
    echo "OK: Connected in {$elapsed} ms\n";
    $res = $mysqli->query('SELECT NOW() as now');
    if ($res) {
        $row = $res->fetch_assoc();
        echo 'Server time: ' . $row['now'] . "\n";
    }
    $mysqli->close();
    exit(0);
}

echo "ERROR: {$mysqli->connect_errno} - {$mysqli->connect_error}\n";
// Sugestões comuns
echo "\nChecks:\n- Credenciais corretas?\n- Host acessível e porta 3306 aberta?\n- Usuário tem permissão para este host (whitelist)?\n- Limite de conexões/recursos no provedor?\n";


