<?php
session_start();
require 'config.php';

// 1. Force HTTPS
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === "off") {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

// 2. Session Timeout (15 minutes = 900 seconds)
$timeout_duration = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?error=Session_Expired");
    exit;
}
$_SESSION['last_activity'] = time();

// 3. Logging Feature
function writeLog($type, $message) {
    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Guest';
    $logEntry = "[$date] [User ID: $user_id] [IP: $ip] [$type] $message" . PHP_EOL;
    file_put_contents(__DIR__ . '/app.log', $logEntry, FILE_APPEND);
}

// 4. Advanced Error Messaging
function customExceptionHandler($e) {
    writeLog('ERROR', $e->getMessage()); // Always log the error silently
    
    echo "<div style='padding: 20px; font-family: sans-serif;'>";
    if (defined('DEBUG_MODE') && DEBUG_MODE === true) {
        echo "<h3 style='color: red;'>Detailed Error Trace (Debug Enabled):</h3>";
        echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
        echo "<pre style='background: #f4f4f4; padding: 10px;'>" . $e->getTraceAsString() . "</pre>";
    } else {
        echo "<h3 style='color: red;'>An unexpected error occurred.</h3>";
        echo "<p>Please try again later or contact administration.</p>";
    }
    echo "</div>";
    exit;
}
set_exception_handler('customExceptionHandler');
?>