<?php
// Diagnóstico independente (não carrega WordPress)
@header('Content-Type: text/plain; charset=utf-8');

// Resolve wp-config.php when this script is placed in subdirectories (e.g., wp-admin)
function find_wp_config_path() {
    $candidates = [
        __DIR__ . '/wp-config.php',
        dirname(__DIR__) . '/wp-config.php',
        dirname(dirname(__DIR__)) . '/wp-config.php',
    ];
    foreach ($candidates as $candidate) {
        if (is_readable($candidate)) { return $candidate; }
    }
    return '';
}

$wpConfigPath = find_wp_config_path();
if ($wpConfigPath === '') { die("Can't find wp-config.php\n"); }

$cfg = file_get_contents($wpConfigPath);
if ($cfg === false) { die("Can't read wp-config.php\n"); }

// Optional verbose errors via query string
if (isset($_GET['show_errors']) && $_GET['show_errors'] == '1') {
    @ini_set('display_errors', '1');
    @ini_set('display_startup_errors', '1');
    @error_reporting(E_ALL);
}

function get_const($src, $name) {
    // Aceita aspas simples ou duplas e espaços variados
    $pattern = "/define\\(\\s*['\"]" . preg_quote($name, '/') . "['\"]\\s*,\\s*(['\"])(.*?)\\1\\s*\\)\\s*;/";
    if (preg_match($pattern, $src, $m)) {
        return $m[2];
    }
    return '';
}

$host = get_const($cfg, 'DB_HOST');
$user = get_const($cfg, 'DB_USER');
$pass = get_const($cfg, 'DB_PASSWORD');
$db   = get_const($cfg, 'DB_NAME');

// Evita dar include no WordPress para não causar HTTP 500 em produção.

// Parâmetro opcional para forçar teste com IP direto
$useIp = isset($_GET['use_ip']) && $_GET['use_ip'] == '1';

echo "Reading credentials from wp-config.php\n";
echo "Host: $host\nDB: $db\nUser: $user\n\n";

// Teste DNS
$dnsOk = false; $ip = '';
if ($host) {
    $ip = gethostbyname($host);
    $dnsOk = $ip && $ip !== $host;
}
echo 'DNS resolve: ' . ($dnsOk ? 'OK (' . $ip . ')' : 'FAILED') . "\n";

// Teste porta 3306
$portOpen = false; $errno = 0; $errstr = '';
if ($host) {
    $start = microtime(true);
    $sock = @fsockopen($host, 3306, $errno, $errstr, 5);
    $elapsed = round((microtime(true) - $start) * 1000);
    if ($sock) { $portOpen = true; fclose($sock); }
    echo 'TCP 3306: ' . ($portOpen ? 'OPEN' : ('CLOSED (' . $errno . ' ' . $errstr . ')')) . ' in ' . $elapsed . " ms\n";
}

// Teste MySQL
$start = microtime(true);
$targetHost = ($useIp && $dnsOk) ? $ip : $host;
$mysqli = @new mysqli($targetHost, $user, $pass, $db);
$elapsed = round((microtime(true) - $start) * 1000);
if ($mysqli && !$mysqli->connect_errno) {
    echo "MySQL: CONNECTED in {$elapsed} ms\n";
    $row = $mysqli->query('SELECT DATABASE() db, NOW() t')->fetch_assoc();
    echo 'DB: ' . $row['db'] . ' | Time: ' . $row['t'] . "\n";
    $mysqli->close();
} else {
    echo 'MySQL ERROR: ' . ($mysqli ? $mysqli->connect_errno . ' - ' . $mysqli->connect_error : 'unknown') . "\n";
    if ($useIp) { echo "Tried with IP: $targetHost\n"; }
}


