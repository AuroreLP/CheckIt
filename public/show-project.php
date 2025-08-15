<?php
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/project.php';
require_once __DIR__ . '/../src/domain.php';
require_once __DIR__ . '/../src/task.php';

// Vérifier l'existence du projet
  if (!isset($_GET['id'])) {
    die("ID du projet manquant.");
  }
  
// Récupérer les informations du projet
$project_id = (int)$_GET['id'];
$project = getProjectById($pdo, $project_id);
$tasks = getProjectTasks($pdo, $project_id);

// Récupérer les phases disponibles
$phases = Phase::cases();

$errors = [];
$success = false;

// Mise à jour du projet si besoin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateProject'])) {
  updateProject($pdo, $project['id'], $_POST);
}

  // appel la fonction saveTask pour créer une tâche
if (isset($_POST['saveTask'])) {
  $name = trim($_POST['name'] ?? '');
  $phase = $_POST['phase'] ?? '';
  $deadline = $_POST['deadline'] ?? '';
  $description = trim($_POST['description'] ?? '');

  if (empty($name)) $errors[] = "Le nom est requis.";
  if (!in_array($phase, array_column(Phase::cases(), 'value'))) $errors[] = "Phase invalide.";
  if (empty($deadline)) $errors[] = "La date limite est requise.";

  if (empty($errors)) {
      addTask($pdo, $name, $phase, $deadline, $description, $project_id, 0);
      $success = true;
      $tasks = getProjectTasks($pdo, $project_id); // pour rafraîchir l'affichage
  }
}

  // appel de la fonction editTask pour modifier une tâche
  if (isset($_POST['editTask'])) {
    $taskId = (int)$_POST['task_id'];
    $name = trim($_POST['name'] ?? '');
    $phase = $_POST['phase'] ?? '';
    $deadline = $_POST['deadline'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) $errors[] = "Le nom est requis.";
    if (!in_array($phase, array_column(Phase::cases(), 'value'))) $errors[] = "Phase invalide.";
    if (empty($deadline)) $errors[] = "La date limite est requise.";

    if (empty($errors)) {
        editTask($pdo, $taskId, $name, $phase, $deadline, $description);
        $success = true;
        $tasks = getProjectTasks($pdo, $project_id); // rafraîchir
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'updateTaskStatus' && isset($_GET['task_id']) && isset($_GET['status'])) {
  $taskId = (int)$_GET['task_id'];
  $newStatus = (int)$_GET['status'];
  updateTaskStatus($pdo, $taskId, $newStatus);
  $tasks = getProjectTasks($pdo, $project_id); // Rafraîchir les tâches
}


  // appel de la fonction deleteTask pour supprimer une tâche
  if (isset($_GET['action']) && $_GET['action'] === 'deleteProjectTask' && isset($_GET['task_id'])) {
    $taskId = (int)$_GET['task_id'];
    deleteTask($pdo, $taskId);
    $tasks = getProjectTasks($pdo, $project_id); // Rafraîchir la liste des tâches
  }

?>

<div class="container">
  <div class="d-flex justify-content-between align-items-end mt-2">
    <h1><?= htmlspecialchars($project['title']) ?></h1>
    <span class="badge rounded-pill text-bg-primary fs-4">
      <i class="bi <?= htmlspecialchars($project['domain_icon'] ?? '') ?>"></i>
      <?= htmlspecialchars($project['domain_name'] ?? 'Domaine inconnu') ?>
    </span>
  </div>
  <div class="row mt-3 mb-2">
    <div class="col-12">
      <textarea name="needs" class="form-control" rows="3"><?= htmlspecialchars($project['needs']); ?></textarea>
    </div>
  </div>
  <div class="d-flex justify-content-between align-items-center mt-3 flex-column flex-md-row gap-2">
  <!-- Groupe modifier + supprimer -->
    <div class="d-flex gap-2 flex-column flex-sm-row w-100 w-md-auto">
      <a href="edit-project.php?id=<?= $project_id ?>" class="btn btn-outline-primary flex-fill">
        <i class="bi bi-pencil"></i> Modifier
      </a>
      <a href="mes-projets.php" class="btn btn-secondary mt-2 mt-md-0">
      Retour à la liste des projets
      </a>
      <form method="post" action="" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');" style="margin:0;" class="flex-fill">
        <input type="hidden" name="deleteProject" value="1">
        <button type="submit" class="btn btn-danger w-100">
          <i class="bi bi-trash3-fill"></i> Supprimer
        </button>
      </form>
    </div>
  </div>

  <div class="row mt-3">
    <h2 class="border-bottom pt-3 mt-3">Tâches</h2>
    <?php if ($success): ?>
      <div id="successMessage" class="alert alert-success" role="alert">
        Tâche ajoutée avec succès !
      </div>
      <script>
        setTimeout(() => {
          const msg = document.getElementById('successMessage');
          if (msg) msg.style.display = 'none';
        }, 3000); // 3 secondes
      </script>
    <?php endif; ?>

      <?php foreach ($errors as $error): ?>
          <p style="color:red;"><?= htmlspecialchars($error) ?></p>
      <?php endforeach; ?>

      <form method="post" class="row gy-2 gx-3 align-items-center">
        <div class="col-md-3"><input type="text" name="name" placeholder="Tâche" class="form-control" required></div>
        <div class="col-md-4"><input type="textarea" name="description" placeholder="Description" class="form-control"></div>
        <div class="col-md-3">
          <select name="phase" class="form-select">
            <option value="">Phase</option>
            <?php foreach ($phases as $case): ?>
              <option value="<?= $case->value ?>" <?= (isset($task) && $task['phase'] === $case->value) ? 'selected' : '' ?>>
                <?= ucfirst($case->name) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-auto"><input type="date" name="deadline" class="form-control"></div>
        <div class="col-md-auto"><input type="submit" name="saveTask" value="Ajouter une tâche" class="btn btn-primary"></div>
      </form>

      <div class="row m-4 border rounded p-2">
        <div class="accordion mb-2" id="accordionTasks">
          <?php foreach ($tasks as $task) { ?>
            <div class="accordion-item border rounded shadow-sm mb-5">
              <h2 class="accordion-header" id="heading-<?= $task['id'] ?>">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-item-<?= $task['id'] ?>" aria-expanded="false" aria-controls="collapseOne">
                <a class="me-2" href="?id=<?=$_GET['id']?>&action=updateTaskStatus&task_id=<?=$task['id'] ?>&status=<?=!$task['status'] ?>"><i class="bi bi-check-circle<?=($task['status'] ? '-fill' : '')?>"></i></a>
                <?= $task['name'] ?>
              </button>
              </h2>
              <div id="collapse-<?= $task['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?= $task['id'] ?>" data-bs-parent="#accordionTasks">
                <div class="accordion-body">
                  <form action="" method="post" class="mb-4">
                    <input type="hidden" name="task_id" value="<?= $task['id']; ?>">
                    <div class="row mb-3">
                      <div class="col-md-4">
                        <input type="text" value="<?= htmlspecialchars($task['name']); ?>" name="name" class="form-control" placeholder="Nom de la tâche">
                      </div>
                      <div class="col-md-4">
                        <input type="date" value="<?= htmlspecialchars($task['deadline']); ?>" name="deadline" class="form-control" placeholder="Date limite">
                      </div>
                      <div class="col-md-4">
                        <select name="phase" class="form-select">
                          <?php foreach ($phases as $case): ?>
                            <option value="<?= $case->value ?>" <?= $task['phase'] === $case->value ? 'selected' : '' ?>>
                                <?= ucfirst($case->value) ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                    <div class="row mb-3">
                      <div class="col-12">
                        <textarea name="description" class="form-control" rows="3" placeholder="Description"><?= htmlspecialchars($task['description']); ?></textarea>
                      </div>
                    </div>
                    <div class="row align-items-center">
                      <div class="col text-start">
                        <input type="submit" value="Modifier" name="editTask" class="btn btn-primary">
                      </div>
                      <div class="col text-end">
                        <a href="?id=<?= $_GET['id']; ?>&action=deleteProjectTask&task_id=<?= $task['id']; ?>"
                            class="btn btn-outline-danger"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');">
                          <i class="bi bi-trash3-fill"></i> Supprimer
                        </a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>