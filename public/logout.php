<?php
require_once __DIR__ . "/../src/session.php";

// Supprimer toutes les variables de session
$_SESSION = [];

// Supprimer le cookie de session (pour vraiment tout effacer côté client)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Détruit la session
session_destroy();

$_SESSION['flash'] = "Vous avez été déconnecté(e).";

// Redirection vers la page de login
header('Location: login.php');
exit;
