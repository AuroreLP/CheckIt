<?php
ob_start();
require_once __DIR__ . "/../templates/header.php";
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/project.php';
require_once __DIR__ . '/../src/domain.php';
require_once __DIR__ . '/../src/task.php';

if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

$id = (int)($_GET['id'] ?? 0);
$project = getProjectById($pdo, $id);
$domains = getAllDomains($pdo);
$errorsProject = [];
$messagesProject = [];
$phases = [
    ['id' => 'analyse', 'name' => 'Analyse'],
    ['id' => 'conception', 'name' => 'Conception'],
    ['id' => 'programmation', 'name' => 'Programmation'],
    ['id' => 'deploiement', 'name' => 'Déploiement']
];

// Mise à jour du projet
if (isset($_POST['saveProject'])) {
    if (!empty($_POST['title']) && isset($_POST['domain_id'])) {
        $res = saveProject($pdo, $_POST['title'], $_SESSION['user']['id'], (int)$_POST['domain_id'], $id);
        $project = getProjectById($pdo, $id);
        if ($res) $messagesProject[] = "Le projet a bien été mis à jour";
        else $errorsProject[] = "Le projet n'a pas été enregistré";
    } else {
        $errorsProject[] = "Le titre et le domaine sont obligatoires";
    }
}

// Ajout d'une tâche
if (isset($_POST['saveTask'])) {
    $errorsProjectTask = [];
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $phaseInput = $_POST['phase_id'] ?? '';
    $deadlineInput = $_POST['deadline'] ?? '';
    $status = isset($_POST['status']);

    if (empty($name)) $errorsProjectTask[] = "Le nom de la tâche est obligatoire.";
    try {
        $phase = Phase::from($phaseInput);
    } catch (ValueError $e) {
        $errorsProjectTask[] = "Phase invalide.";
    }

    if (!empty($deadlineInput)) {
        try {
            $deadline = new DateTime($deadlineInput);
        } catch (Exception $e) {
            $errorsProjectTask[] = "Date limite invalide.";
        }
    } else {
        $errorsProjectTask[] = "La date limite est obligatoire.";
    }

    if (empty($errorsProjectTask)) {
        $res = addTask($pdo, $name, $phase, $deadline, $description, $id, $status);
        if (!$res) $errorsProjectTask[] = "Erreur lors de l'enregistrement.";
    }
}

$tasks = getProjectTasks($pdo, $id);
?>

<div class="container col-xxl-8 py-2 mb-4">
  <h1>Modifier le projet : <?= htmlspecialchars($project['title']) ?></h1>

  <?php foreach ($errorsProject as $error): ?>
    <div class="alert alert-danger"><?= $error; ?></div>
  <?php endforeach; ?>
  <?php foreach ($messagesProject as $message): ?>
    <div class="alert alert-success"><?= $message; ?></div>
  <?php endforeach; ?>

  <!-- Formulaire modification projet -->
  <form action="" method="post">
    <div class="mb-3">
      <label for="title" class="form-label">Titre</label>
      <input type="text" name="title" id="title" class="form-control" value="<?= $project['title'] ?>">
    </div>

    <div class="mb-3">
      <label for="domain_id" class="form-label">Domaine</label>
      <select name="domain_id" id="domain_id" class="form-control">
        <?php foreach ($domains as $domain): ?>
          <option value="<?= $domain['id'] ?>" <?= ($domain['id'] === $project['domain_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($domain['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <input type="submit" name="saveProject" value="Enregistrer les modifications" class="btn btn-primary">
  </form>

  <h2 class="border-bottom pt-3 mt-5">Tâches</h2>
  <form method="post" class="row gy-2 gx-3 align-items-center">

    <div class="col-md-3"><input type="text" name="name" placeholder="Tâche" class="form-control" required></div>
    <div class="col-md-4"><input type="text" name="description" placeholder="Description" class="form-control"></div>
    <div class="col-md-3">
      <select name="phase_id" class="form-select">
        <option value="">Phase</option>
        <?php foreach ($phases as $phase): ?>
          <option value="<?= $phase['id'] ?>"><?= $phase['name'] ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-auto"><input type="date" name="deadline" class="form-control"></div>
    <div class="col-md-auto"><input type="submit" name="saveTask" value="Ajouter tâche" class="btn btn-primary"></div>

  </form>

  <ul class="list-group mt-3">
    <?php foreach ($tasks as $task): ?>
      <li class="list-group-item"><?= htmlspecialchars($task['name']) ?> – <?= $task['phase'] ?></li>
    <?php endforeach; ?>
  </ul>
</div>

<?php
require_once __DIR__ . "/../templates/footer.php";
ob_end_flush();
?>
