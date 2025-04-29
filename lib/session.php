<?php
// Protection contre les erreurs "headers already sent"
if (session_status() === PHP_SESSION_NONE) {
  session_set_cookie_params([
      'lifetime' => 0,
      'path' => '/',
      'domain' => '',
      'secure' => isset($_SERVER['HTTPS']), // true si HTTPS
      'httponly' => true,
      'samesite' => 'Lax'
  ]);
  session_start();
}

function isUserConnected():bool
{
    return isset($_SESSION['user']);
}

?>