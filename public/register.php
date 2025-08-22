<?php
ob_start();
require_once __DIR__ . "/../src/session.php";
require_once __DIR__ . "/../src/pdo.php";
require_once __DIR__ . "/../src/authentication.php";

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registerUser'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validation des données
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis";
    } elseif (strlen($username) < 3) {
        $errors[] = "Le nom d'utilisateur doit contenir au moins 3 caractères";
    } elseif (strlen($username) > 50) {
        $errors[] = "Le nom d'utilisateur ne peut pas dépasser 50 caractères";
    } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        $errors[] = "Le nom d'utilisateur ne peut contenir que des lettres, chiffres, tirets et underscores";
    }
    
    if (empty($email)) {
        $errors[] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email n'est pas valide";
    } elseif (strlen($email) > 255) {
        $errors[] = "L'email ne peut pas dépasser 255 caractères";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis";
    } elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une minuscule, une majuscule, un chiffre et un caractère spécial";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    // Vérifier si l'utilisateur ou l'email existe déjà
    if (empty($errors)) {
        if (isUsernameExists($pdo, $username)) {
            $errors[] = "Ce nom d'utilisateur est déjà utilisé";
        }
        
        if (isEmailExists($pdo, $email)) {
            $errors[] = "Cet email est déjà utilisé";
        }
    }
    
    // Si pas d'erreurs, créer l'utilisateur
    if (empty($errors)) {
        $userId = createUser($pdo, $username, $email, $password);
        
        if ($userId) {
            $success = true;
            $_SESSION['flash'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            // Optionnel : rediriger vers login après quelques secondes
            // header('Refresh: 3; URL=login.php');
        } else {
            $errors[] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
        }
    }
}
?>

<?php require_once __DIR__ . "/../templates/header.php"; ?>

<div class="container col-xxl-8 px-4 py-5">
    <h1>S'inscrire</h1>
    
    <?php if ($success): ?>
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Inscription réussie !</h4>
            <p>Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.</p>
            <hr>
            <a href="login.php" class="btn btn-primary">Se connecter</a>
        </div>
    <?php else: ?>
        
        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <form action="" method="post" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur *</label>
                <input type="text" 
                       name="username" 
                       id="username" 
                       class="form-control <?= in_array('username', array_column($errors, 'field')) ? 'is-invalid' : '' ?>"
                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
                       required
                       minlength="3"
                       maxlength="50">>
                <div class="form-text">3-50 caractères, lettres, chiffres, tirets et underscores uniquement</div>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Email *</label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       class="form-control"
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                       required
                       maxlength="255">
            </div>
            
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe *</label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       class="form-control" 
                       required
                       minlength="8">
                <div class="form-text">Au moins 8 caractères avec majuscule, minuscule, chiffre et caractère spécial</div>
            </div>
            
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmer le mot de passe *</label>
                <input type="password" 
                       name="confirm_password" 
                       id="confirm_password" 
                       class="form-control" 
                       required
                       minlength="8">
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <input type="submit" name="registerUser" value="S'inscrire" class="btn btn-primary">
                <a href="login.php" class="btn btn-outline-secondary">Déjà un compte ? Se connecter</a>
            </div>
        </form>
        
    <?php endif; ?>
</div>

<script>
// Validation côté client pour confirmation mot de passe
document.addEventListener('DOMContentLoaded', function() {
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
});
</script>

<?php
require_once __DIR__ . "/../templates/footer.php";
ob_end_flush();
?>