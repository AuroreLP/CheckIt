<?php
  ob_start(); // Démarre un tampon de sortie
  require_once __DIR__ . "/../src/session.php"; 
  require_once __DIR__. "/../src/pdo.php";
  require_once __DIR__. "/../src/user.php";

  $errors = [];

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginUser'])) {
    $user = verifyUserLoginPassword($pdo, $_POST['username'], $_POST['password']);

    if ($user) {
      // on va le connecter => session
      $_SESSION['user'] = $user;
      header('location: mes-projets.php');
      exit;

    } else {
      // afficher une erreur
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

    <input type="submit" name="loginUser" value="connexion" class="btn btn-primary">

  </form>
</div>


<?php
  require_once __DIR__. "/../templates/footer.php";
  ob_end_flush(); // Vide le tampon et envoie tout au navigateur
?>