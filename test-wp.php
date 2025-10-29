<?php
// Test WordPress connection
define( 'WP_USE_THEMES', false );
require __DIR__ . '/wp-blog-header.php';

echo "<h1>WordPress Test</h1>";
echo "<p>WordPress carregou com sucesso!</p>";
echo "<p>Versão do WordPress: " . get_bloginfo('version') . "</p>";
echo "<p>URL do site: " . get_bloginfo('url') . "</p>";
?>

