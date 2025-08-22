<?php
// Protection contre les erreurs "headers already sent"
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

function isUserConnected(): bool {
    return isset($_SESSION['user']);
}

function getConnectedUserName() {
    // Si le username est directement stocké dans la session
    if (isset($_SESSION['username'])) {
        return $_SESSION['username'];
    }
    
    // Si les infos utilisateur sont dans $_SESSION['user']
    if (isset($_SESSION['user']['username'])) {
        return $_SESSION['user']['username'];
    }
    
    // S'il y a un ID utilisateur et qu'il faut récupérer le username depuis la DB
    if (isset($_SESSION['user_id']) || isset($_SESSION['user']['id'])) {
        try {
            require_once __DIR__ . '/pdo.php';
            
            if (!isset($pdo)) {
                $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            
            $user_id = $_SESSION['user_id'] ?? $_SESSION['user']['id'];
            $stmt = $pdo->prepare("SELECT username FROM user WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ? $user['username'] : 'Utilisateur';
        } catch (PDOException $e) {
            // En cas d'erreur, retourner une valeur par défaut
            error_log("Erreur DB dans getConnectedUserName: " . $e->getMessage());
            return 'Utilisateur';
        }
    }
    
    return 'Utilisateur';
}
?>