<?php
  require_once 'templates/header.php';
  require_once 'lib/pdo.php';
  require_once 'lib/project.php';
  require_once 'lib/domain.php';
  require_once 'lib/task.php';
  

  if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user']['id'];
$domain_id = isset($_GET['domain']) ? (int)$_GET['domain'] : null;

// Récupérer tous les domaines
$domains = getAllDomains($pdo);

// Récupérer les projets filtrés
$projects = getProjectsByUserAndDomain($pdo, $userId, $domain_id);

?>

<div class="container">
  <div class="d-flex justify-content-between align-items-center">
    <h1>Mes Projets</h1>
    <?php if (isUserConnected()) { ?>
      <a href="new-project.php" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Ajouter un projet</a>
    <?php } ?>
  </div>
  <form method="get" action="my-projects.php">
      <select name="domain" id="domain" onchange="this.form.submit()" class="form-control btn btn-primary">
        <i class="bi bi-caret-down-fill"></i>
        <option value="">Tous les domaines</option>
        <?php foreach ($domains as $domain): ?>
          <option value="<?= htmlspecialchars($domain['id']) ?>" <?= ($domain['id'] == $domain_id) ? 'selected' : '' ?>>
          <?= htmlspecialchars($domain['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

  <div class="row mt-5">

    <?php if (isUserConnected()) { 
      if ($projects) {
        foreach ($projects as $project) { ?>
          <div class="col-md-4 my-2">
            <div class="card w-100">
              <div class="card-header d-flex align-items-center justify-content-evenly">
                <h3 class="card-title text-center"><?=$project['title'] ?></h3>
              </div>
              <div class="card-body d-flex flex-column ">
                <?php $tasks = getProjectTasks($pdo, $project['id']); ?>
                <?php if ($tasks) { ?>
                <ul class="project-group">
                    <?php foreach ($tasks as $task) { ?>
                      <li class="project-group-task"><a class="me-2" href="?id=<?=$task['id']?>&action=updateTaskStatus&redirect=project&task_id=<?=$task['id'] ?>&status=<?=!$task['status'] ?>"><i class="bi bi-check-circle<?=($task['status'] ? '-fill' : '')?>"></i></a> <?= $task['name'] ?></li>
                    <?php } ?>
                </ul>
                <?php } ?>
                <div class="d-flex justify-content-between align-items-end mt-2">
                  <a href="show-project.php?id=<?=$project['id'] ?>" class="btn btn-primary">Voir le projet</a>
                  <span class="badge rounded-pill text-bg-primary">
                    <i class="bi <?= htmlspecialchars($project['domain_icon'] ?? '') ?>"></i>
                    <?= htmlspecialchars($project['domain_name'] ?? 'Domaine inconnu') ?>
                  </span>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      <?php } else { ?>
        <p>Aucun projet</p>
      <?php } ?>

    <?php } else { ?>
      <p>Pour consulter vos projets, veuillez vous connecter</p>
      <a href="login.php" class="btn btn-outline-primary me-2">Login</a>
    <?php } ?>

  </div>


</div>

<?php
  require_once __DIR__ . "/templates/footer.php"
?>