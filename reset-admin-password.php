<?php
/**
 * Reset Admin Password via Database
 * Run this ONCE, then DELETE immediately
 */

// Database credentials from wp-config
define('DB_NAME', 'u426126796_pontodogolfe');
define('DB_USER', 'u426126796_pontodogolfe');
define('DB_PASSWORD', 'Los@ngo#081081');
define('DB_HOST', 'auth-db1075.hstgr.io');

echo "<h1>Reset de Senha Admin</h1>";
echo "<hr>";

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_error) {
    die("<p style='color:red;'>❌ Erro de conexão: " . $mysqli->connect_error . "</p>");
}

echo "<p style='color:green;'>✅ Conectado ao banco de dados</p>";

// New password
$new_password = 'Los@ngo#081081';
$username = 'admin';
$email = 'dietrich.representacoes@gmail.com';

// Check if user exists
$result = $mysqli->query("SELECT ID, user_login FROM wp_users WHERE user_login = '$username' OR user_email = '$email'");

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['ID'];
    
    echo "<p style='color:blue;'>✅ Usuário encontrado: " . htmlspecialchars($user['user_login']) . " (ID: $user_id)</p>";
    
    // Generate WordPress password hash using bcrypt
    $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
    
    // Update password with WordPress-compatible hash
    $stmt = $mysqli->prepare("UPDATE wp_users SET user_pass = ? WHERE ID = ?");
    $stmt->bind_param("si", $password_hash, $user_id);
    
    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Senha atualizada com sucesso!</p>";
        echo "<hr>";
        echo "<h2>Credenciais Atualizadas:</h2>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Valor</th></tr>";
        echo "<tr><td><strong>URL de Login:</strong></td><td><a href='wp-login.php' target='_blank'>https://pontodogolfeoutlet.com.br/wp-login.php</a></td></tr>";
        echo "<tr><td><strong>Usuário:</strong></td><td>" . htmlspecialchars($user['user_login']) . "</td></tr>";
        echo "<tr><td><strong>Senha Nova:</strong></td><td>" . htmlspecialchars($new_password) . "</td></tr>";
        echo "</table>";
        echo "<hr>";
        echo "<p style='color:red;'><strong>⚠️ IMPORTANTE:</strong> Delete este arquivo AGORA!</p>";
    } else {
        echo "<p style='color:red;'>❌ Erro ao atualizar senha: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
} else {
    echo "<p style='color:orange;'>⚠️ Usuário não encontrado. Tentando criar...</p>";
    
    // Try to create user
    $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
    $stmt = $mysqli->prepare("INSERT INTO wp_users (user_login, user_pass, user_nicename, user_email, user_status) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $username, $password_hash, $username, $email);
    
    if ($stmt->execute()) {
        $user_id = $mysqli->insert_id;
        $stmt->close();
        $user_id = $mysqli->insert_id;
        echo "<p style='color:green;'>✅ Usuário criado com ID: $user_id</p>";
        
        // Make user admin - need to check if usermeta table exists
        $meta_query = "INSERT INTO wp_usermeta (user_id, meta_key, meta_value) 
                       VALUES ($user_id, 'wp_capabilities', 'a:1:{s:13:\"administrator\";b:1;}')";
        $mysqli->query($meta_query);
        
        $level_query = "INSERT INTO wp_usermeta (user_id, meta_key, meta_value) 
                        VALUES ($user_id, 'wp_user_level', 10)";
        $mysqli->query($level_query);
        
        echo "<p style='color:green;'>✅ Permissões de admin atribuídas</p>";
        echo "<hr>";
        echo "<h2>Credenciais Criadas:</h2>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Valor</th></tr>";
        echo "<tr><td><strong>URL de Login:</strong></td><td><a href='wp-login.php' target='_blank'>https://pontodogolfeoutlet.com.br/wp-login.php</a></td></tr>";
        echo "<tr><td><strong>Usuário:</strong></td><td>" . htmlspecialchars($username) . "</td></tr>";
        echo "<tr><td><strong>Senha:</strong></td><td>" . htmlspecialchars($new_password) . "</td></tr>";
        echo "</table>";
        echo "<hr>";
        echo "<p style='color:red;'><strong>⚠️ IMPORTANTE:</strong> Delete este arquivo AGORA!</p>";
    } else {
        echo "<p style='color:red;'>❌ Erro ao criar usuário: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
}

$mysqli->close();

echo "<hr>";
echo "<p><em>Este arquivo deve ser deletado após o uso por questões de segurança.</em></p>";

// No helper functions needed - using native PHP password functions
?>

