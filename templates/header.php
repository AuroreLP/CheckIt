<?php 
require_once __DIR__ . '/../src/session.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CheckIt</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/override-bootstrap.css?v=2.1">
  <!-- Bootstrap JS (inclut Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 m-3 border-bottom">
      <div class="col-md-3 mb-2 mb-md-0">
        <a href="/" class="d-inline-flex link-body-emphasis text-decoration-none">
          <img src="assets/images/Logo-Checkit.png" alt="logo checkit" width="160">
        </a>
      </div>

      <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
        <li><a href="index.php" class="nav-link px-2 link-secondary">Home</a></li>
        <li><a href="dashboard.php?tab=projets" class="nav-link px-2">Dashboard</a></li>
      </ul>

      <div class="col-md-3 text-end">
        <?php if (isUserConnected()) { ?>
          <a href="dashboard.php" class="me-3 text-decoration-none text-muted">
            <i class="bi bi-person-circle me-1"></i>
            @<?php echo htmlspecialchars(getConnectedUserName()); ?>
          </a>
          <a href="logout.php" class="btn btn-outline-primary me-2">Déconnexion</a>
        <?php } else { ?>
          <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
        <?php } ?>
      </div>
    </header>
    <main>