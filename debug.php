<?php
// Debug WordPress
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>WordPress Debug</h1>";

// Test 1: Check wp-config.php
if (file_exists(__DIR__ . '/wp-config.php')) {
    echo "<p style='color:green;'>✅ wp-config.php exists</p>";
    require_once __DIR__ . '/wp-config.php';
} else {
    echo "<p style='color:red;'>❌ wp-config.php NOT found</p>";
    die();
}

// Test 2: Check database connection
$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($mysqli->connect_error) {
    echo "<p style='color:red;'>❌ Database connection failed: " . $mysqli->connect_error . "</p>";
} else {
    echo "<p style='color:green;'>✅ Database connection OK</p>";
    $mysqli->close();
}

// Test 3: Check wp-blog-header.php
if (file_exists(__DIR__ . '/wp-blog-header.php')) {
    echo "<p style='color:green;'>✅ wp-blog-header.php exists</p>";
} else {
    echo "<p style='color:red;'>❌ wp-blog-header.php NOT found</p>";
}

// Test 4: Check theme
if (file_exists(__DIR__ . '/wp-content/themes/twenty-twenty-four/style.css')) {
    echo "<p style='color:green;'>✅ Theme exists</p>";
} else {
    echo "<p style='color:red;'>❌ Theme NOT found</p>";
}

// Test 5: Try to load WordPress
echo "<hr><h2>Loading WordPress...</h2>";
try {
    define('WP_USE_THEMES', false);
    require_once __DIR__ . '/wp-blog-header.php';
    echo "<p style='color:green;'>✅ WordPress loaded successfully!</p>";
    echo "<p>Version: " . get_bloginfo('version') . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ WordPress failed to load: " . $e->getMessage() . "</p>";
}
?>

