<?php
/**
 * Quick Test - Database Connection
 * Delete this file after testing
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Teste de Conexão WordPress</h2>";
echo "<hr>";

try {
    $host = 'auth-db1075.hstgr.io';
    $dbname = 'u426126796_pontodogolfe';
    $username = 'u426126796_pontodogolfe';
    $password = 'Los@ngo#081081';
    
    echo "<p>Tentando conectar ao banco...</p>";
    
    $mysqli = new mysqli($host, $username, $password, $dbname);
    
    if ($mysqli->connect_error) {
        throw new Exception("Erro de conexão: " . $mysqli->connect_error);
    }
    
    echo "<p style='color:green;'>✅ Conexão OK!</p>";
    
    $result = $mysqli->query("SHOW TABLES LIKE 'wp_%'");
    echo "<p>Tabelas encontradas: " . ($result ? $result->num_rows : 0) . "</p>";
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Ir para o WordPress</a></p>";
?>

