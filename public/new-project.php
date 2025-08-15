<?php
ob_start();
require_once __DIR__ . "/../templates/header.php";
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/project.php';
require_once __DIR__ . '/../src/domain.php';

if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

$domains = getAllDomains($pdo);
$errorsProject = [];
$messagesProject = [];

$project = [
    'title' => '',
    'needs' => null,
    'domain_id' => null
];

// Soumission du formulaire
if (isset($_POST['saveProject'])) {
    if (!empty($_POST['title']) && isset($_POST['domain_id'])) {
        $domain_id = (int)$_POST['domain_id'];

        $res = saveProject($pdo, $_POST['title'], (int)$_SESSION['user']['id'], $domain_id);

        if ($res) {
            header('Location: edit-project.php?id=' . $res); // redirige vers la page d'édition
            exit;
        } else {
            $errorsProject[] = "Le projet n'a pas été enregistré";
        }
    } else {
        $errorsProject[] = "Le titre et le domaine sont obligatoires";
    }
}
?>

<div class="container py-4">
    <h1>Ajouter un projet</h1>

    <?php foreach ($errorsProject as $error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endforeach; ?>

    <form action="" method="post">
        <div class="mb-3">
            <label for="title" class="form-label">Titre</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($project['title']) ?>">
        </div>
        <div class="mb-3">
            <label for="domain_id" class="form-label">Domaine</label>
            <select name="domain_id" id="domain_id" class="form-control">
                <?php foreach ($domains as $domain): ?>
                    <option value="<?= $domain['id'] ?>"><?= htmlspecialchars($domain['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="project_needs" class="form-label">Les besoins du projet</label>
            <textarea name="needs" class="form-control" rows="3"><?= htmlspecialchars($project['needs'] ?? ''); ?></textarea>
        </div>
        <button type="submit" name="saveProject" class="btn btn-primary">Créer le projet</button>
    </form>
</div>

<?php
require_once __DIR__ . "/templates/footer.php";
ob_end_flush();
?>
