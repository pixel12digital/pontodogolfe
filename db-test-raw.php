<?php
// Diagnóstico independente (não carrega WordPress)
@header('Content-Type: text/plain; charset=utf-8');

$cfg = file_get_contents(__DIR__ . '/wp-config.php');
if ($cfg === false) { die("Can't read wp-config.php\n"); }

function get_const($src, $name) {
    if (preg_match("/define\(\s*'" . preg_quote($name, '/') . "'\s*,\s*'([^']*)'\s*\)\s*;/", $src, $m)) {
        return $m[1];
    }
    return '';
}

$host = get_const($cfg, 'DB_HOST');
$user = get_const($cfg, 'DB_USER');
$pass = get_const($cfg, 'DB_PASSWORD');
$db   = get_const($cfg, 'DB_NAME');

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
$mysqli = @new mysqli($host, $user, $pass, $db);
$elapsed = round((microtime(true) - $start) * 1000);
if ($mysqli && !$mysqli->connect_errno) {
    echo "MySQL: CONNECTED in {$elapsed} ms\n";
    $row = $mysqli->query('SELECT DATABASE() db, NOW() t')->fetch_assoc();
    echo 'DB: ' . $row['db'] . ' | Time: ' . $row['t'] . "\n";
    $mysqli->close();
} else {
    echo 'MySQL ERROR: ' . ($mysqli ? $mysqli->connect_errno . ' - ' . $mysqli->connect_error : 'unknown') . "\n";
}


