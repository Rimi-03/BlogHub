<?php

// 45 minutes * 60 seconds = 2700 seconds
$expiry_time = 180 * 60;
define('SESSION_DURATION', $expiry_time);

function generateAdminToken()
{
    // Generate a random secure token
    $token = bin2hex(random_bytes(32));
    $_SESSION['admin_token'] = $token;
    $_SESSION['token_generated_at'] = time();
    return $token;
}

function isTokenExpired()
{
    if (!isset($_SESSION['token_generated_at'])) return true;
    return (time() - $_SESSION['token_generated_at']) > SESSION_DURATION;
}
