<?php
ob_start();
require_once __DIR__ . "/../src/session.php";
require_once __DIR__. "/../src/pdo.php";
require_once __DIR__. "/../src/user.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginUser'])) {
    echo "<div class='alert alert-warning'>Tentative de connexion en cours...</div>";
    
    $user = verifyUserLoginPassword($pdo, $_POST['username'], $_POST['password']);
    
    echo "<div class='alert alert-info'>Résultat verifyUserLoginPassword: " . ($user ? 'UTILISATEUR TROUVÉ' : 'UTILISATEUR NON TROUVÉ') . "</div>";
    
    if ($user) {
        $_SESSION['user'] = $user;
        header('location: mes-projets.php');
        exit;
    } else {
        $errors[] = "Pseudo ou mot de passe incorrect";
    }
}
?>

<?php require_once __DIR__. "/../templates/header.php"; ?>

<div class="container col-xxl-8 px-4 py-5">
  <h1>Se connecter</h1>

  <?php if (!empty($errors)): ?>
    <?php foreach ($errors as $error): ?>
      <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <form action="" method="post">
    <div class="mb-3">
      <label for="username" class="form-label">Pseudo</label>
      <input type="text" name="username" id="username" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Mot de passe</label>
      <input type="password" name="password" id="password" class="form-control" required>
    </div>

    <input type="submit" name="loginUser" value="connexion" class="btn btn-primary" onclick="console.log('Bouton cliqué!');">

  </form>
</div>


<?php
  require_once __DIR__. "/../templates/footer.php";
  ob_end_flush(); // Vide le tampon et envoie tout au navigateur
?>