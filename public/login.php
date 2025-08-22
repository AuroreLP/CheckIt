<?php
ob_start();
require_once __DIR__ . "/../src/session.php";
require_once __DIR__. "/../src/pdo.php";
require_once __DIR__. "/../src/authentication.php";

// Rediriger si déjà connecté
if (isUserConnected()) {
    header('location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginUser'])) {
    // Protection CSRF basique (vous pouvez implémenter un token plus tard)
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validation basique côté serveur
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    }
    
    if (empty($errors)) {
        $user = verifyUserLoginPassword($pdo, $username, $password);
        
        if ($user) {
            // Régénérer l'ID de session pour éviter la fixation de session
            session_regenerate_id(true);
            
            // Ne stocker que les données nécessaires en session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ];
            
            // Message de succès
            $_SESSION['flash'] = "Connexion réussie ! Bienvenue " . htmlspecialchars($user['username']);
            
            header('location: index.php');
            exit;
        } else {
            // Message générique pour éviter l'énumération d'utilisateurs
            $errors[] = "Identifiants incorrects";
            
            // Délai pour ralentir les attaques par force brute
            sleep(1);
        }
    }
}

// Récupérer le message flash s'il existe
$flashMessage = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<?php require_once __DIR__ . "/../templates/header.php"; ?>

<div class="container col-xxl-8 px-4 py-5">
    <h1>Se connecter</h1>
    
    <?php if ($flashMessage): ?>
        <div class="alert alert-info" role="alert">
            <?= htmlspecialchars($flashMessage) ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <form action="" method="post" novalidate>
        <div class="mb-3">
            <label for="username" class="form-label">Nom d'utilisateur</label>
            <input type="text" 
                   name="username" 
                   id="username" 
                   class="form-control" 
                   value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                   required 
                   autocomplete="username">
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" 
                   name="password" 
                   id="password" 
                   class="form-control" 
                   required
                   autocomplete="current-password">
        </div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
            <input type="submit" name="loginUser" value="Se connecter" class="btn btn-primary">
            <a href="register.php" class="btn btn-outline-secondary">Pas de compte ? S'inscrire</a>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . "/../templates/footer.php";
ob_end_flush();
?>