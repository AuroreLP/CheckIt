<?php
  require_once __DIR__ . '/../src/session.php'; 
  
  if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
  }
  
  require_once __DIR__ . '/../templates/header.php';
  require_once __DIR__ . '/../src/pdo.php';
  require_once __DIR__ . '/../src/project.php';
  require_once __DIR__ . '/../src/domain.php';
  require_once __DIR__ . '/../src/task.php';
  

$userId = $_SESSION['user']['id'];
$domain_id = isset($_GET['domain']) ? (int)$_GET['domain'] : null;

// Récupérer tous les domaines
$domains = getAllDomains($pdo);

// Récupérer les projets filtrés
$projects = getProjectsByUserAndDomain($pdo, $userId, $domain_id);

?>

<div class="container">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
    <h1>Mes Projets</h1>
    <div class="d-flex flex-column flex-md-row gap-3 mt-3 mt-md-0">
      <?php if (isUserConnected()) { ?>
        <a href="new-project.php" class="btn btn-primary mt-3 mt-md-0">
          <i class="bi bi-plus-lg"></i> Ajouter un projet
        </a>
      <?php } ?>
    </div>
    <form method="get" action="mes-projets.php" class="d-inline-block">
      <select name="domain" id="domain" onchange="this.form.submit()" class="form-select btn btn-primary px-5" style="width: auto;">
        <option value="">Filtrer par domaine</option>
        <?php foreach ($domains as $domain): ?>
          <option value="<?= htmlspecialchars($domain['id']) ?>" <?= ($domain['id'] == $domain_id) ? 'selected' : '' ?>>
            <?= htmlspecialchars($domain['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

    <div class="row mt-5">
      <?php if (isUserConnected()) {
        if ($projects) {
          foreach ($projects as $project) { ?>
            <div class="col-md-4 my-2">
              <div class="card">
                <div class="card-header text-center">
                  <h3 class="card-title fs-5 fs-md-4 fs-lg-3 text-truncate">
                    <?= htmlspecialchars($project['title']) ?>
                  </h3>
                </div>
                <div class="card-body">
                  <?php
                    $tasks = getProjectTasks($pdo, $project['id']);
                    if ($tasks) {
                      $tasks = array_slice($tasks, 0, 3); // Limiter à 3 tâches
                  ?>
                  <ul class="project-group">
                    <?php foreach ($tasks as $task) { ?>
                      <li class="project-group-task">
                        <a class="me-2"
                          href="?id=<?= $task['id'] ?>&action=updateTaskStatus&redirect=project&task_id=<?= $task['id'] ?>&status=<?= !$task['status'] ?>">
                          <i class="bi bi-check-circle<?= ($task['status'] ? '-fill' : '') ?>"></i>
                        </a>
                        <?= htmlspecialchars($task['name']) ?>
                      </li>
                    <?php } ?>
                  </ul>
                  <?php } ?>
                  <div class="d-flex justify-content-between align-items-center mt-3">
                    <a href="show-project.php?id=<?= $project['id'] ?>" class="btn btn-primary">Voir le projet</a>
                    <span class="badge rounded-pill text-bg-primary">
                      <i class="bi <?= htmlspecialchars($project['domain_icon'] ?? '') ?>"></i>
                      <?= htmlspecialchars($project['domain_name'] ?? 'Domaine inconnu') ?>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          <?php }
        } else { ?>
          <p>Aucun projet</p>
        <?php }
      } else { ?>
        <p>Pour consulter vos projets, veuillez vous connecter</p>
        <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
      <?php } ?>
    </div>

  </div>


</div>

<?php
  require_once __DIR__ . "/../templates/footer.php"
?>