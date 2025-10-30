<?php
/**
 * Create WordPress Admin User
 * Run this ONCE, then DELETE immediately
 */

// Load WordPress
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

echo "<h1>Criar Usuário Admin do WordPress</h1>";
echo "<hr>";

// Check if database has tables
global $wpdb;
$tables = $wpdb->get_results("SHOW TABLES LIKE '{$wpdb->prefix}%'");

if (empty($tables)) {
    echo "<p style='color:red;'>❌ <strong>ERRO:</strong> Nenhuma tabela do WordPress encontrada no banco de dados!</p>";
    echo "<p>Você precisa <strong>instalar o WordPress primeiro</strong>.</p>";
    echo "<p><a href='wp-admin/install.php'>Ir para Instalação do WordPress</a></p>";
} else {
    echo "<p style='color:green;'>✅ Tabelas do WordPress encontradas: " . count($tables) . "</p>";
    
    // Check if users exist
    $user_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}users");
    
    if ($user_count > 0) {
        echo "<p style='color:orange;'>⚠️ Já existem usuários no sistema.</p>";
        echo "<p>Se você esqueceu a senha, use o link:</p>";
        echo "<p><a href='wp-login.php?action=lostpassword' target='_blank'>Recuperar Senha</a></p>";
        echo "<p>Ou crie um novo usuário manualmente pelo painel admin.</p>";
    } else {
        echo "<p style='color:orange;'>⚠️ Nenhum usuário encontrado. Criando usuário admin...</p>";
        
        // Create admin user
        $username = 'admin';
        $password = 'Admin2025!#PontodoGolfe';
        $email = 'admin@pontodogolfeoutlet.com.br';
        
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            echo "<p style='color:red;'>❌ Erro ao criar usuário: " . $user_id->get_error_message() . "</p>";
        } else {
            // Make user admin
            $user = new WP_User($user_id);
            $user->set_role('administrator');
            
            echo "<p style='color:green;'>✅ <strong>Usuário admin criado com sucesso!</strong></p>";
            echo "<hr>";
            echo "<h2>Credenciais de Acesso:</h2>";
            echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
            echo "<tr><th>Campo</th><th>Valor</th></tr>";
            echo "<tr><td><strong>URL de Login:</strong></td><td><a href='wp-login.php' target='_blank'>https://pontodogolfeoutlet.com.br/wp-login.php</a></td></tr>";
            echo "<tr><td><strong>Usuário:</strong></td><td>" . esc_html($username) . "</td></tr>";
            echo "<tr><td><strong>Senha:</strong></td><td>" . esc_html($password) . "</td></tr>";
            echo "<tr><td><strong>Email:</strong></td><td>" . esc_html($email) . "</td></tr>";
            echo "</table>";
            echo "<hr>";
            echo "<p style='color:red;'><strong>⚠️ IMPORTANTE:</strong> Após fazer login, ALTERE a senha imediatamente!</p>";
            echo "<p style='color:red;'><strong>⚠️ SEGURANÇA:</strong> Delete este arquivo (create-admin.php) agora!</p>";
        }
    }
}

echo "<hr>";
echo "<p><em>Este arquivo deve ser deletado após o uso por questões de segurança.</em></p>";
?>

