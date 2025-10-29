<?php
/**
 * Quick Test - Database Connection
 * Delete this file after testing
 */

define( 'DB_NAME', 'u426126796_pontodogolfe' );
define( 'DB_USER', 'u426126796_pontodogolfe' );
define( 'DB_PASSWORD', 'Los@ngo#081081' );
define( 'DB_HOST', 'auth-db1075.hstgr.io' );

echo "<h2>Teste de Conexão WordPress</h2>";
echo "<hr>";

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    echo "<p style='color:red;'>❌ Erro: " . $mysqli->connect_error . "</p>";
} else {
    echo "<p style='color:green;'>✅ Conexão OK!</p>";
    
    $result = $mysqli->query("SHOW TABLES LIKE 'wp_%'");
    echo "<p>Tabelas encontradas: " . ($result ? $result->num_rows : 0) . "</p>";
    
    $mysqli->close();
}

echo "<hr>";
echo "<p><a href='index.php'>Ir para o WordPress</a></p>";
?>

