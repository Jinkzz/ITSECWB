<?php
define('DEBUG_MODE', true); // Change to false for production

$host = 'localhost';
$db   = 'wecseb_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $logEntry = "[$date] [User ID: Guest] [IP: $ip] [DB_ERROR] " . $e->getMessage() . PHP_EOL;
    file_put_contents(__DIR__ . '/app.log', $logEntry, FILE_APPEND);

    echo "<div style='padding: 20px; font-family: sans-serif;'>";
    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        echo "<h3 style='color: red;'>Detailed Error Trace (Debug Enabled):</h3>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<pre style='background: #f4f4f4; padding: 10px;'>" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . "</pre>";
    } else {
        echo "<h3 style='color: red;'>An unexpected error occurred.</h3>";
        echo "<p>Please try again later or contact administration.</p>";
    }
    echo "</div>";
    exit;
}
?>