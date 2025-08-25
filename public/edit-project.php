<?php
// public/edit-project.php - Version refactorisée
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

// Variables
$id = (int)($_GET['id'] ?? 0);
$project = getProjectById($pdo, $id);
$domains = getAllDomains($pdo);
$projectStatuses = ProjectStatus::cases();
$projectProgress = getProjectProgress($pdo, $id);
$errorsProject = [];
$messagesProject = [];

if (!$project) {
    header('Location: dashboard.php?tab=projets&error=' . urlencode('Projet introuvable.'));
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveProject'])) {
    $title = trim($_POST['title'] ?? '');
    $domain_id = (int)($_POST['domain_id'] ?? 0);
    $needs = trim($_POST['needs'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $status = trim($_POST['status'] ?? '');

    // Validation
    if (empty($title)) $errorsProject[] = "Le titre est obligatoire.";
    if ($domain_id <= 0) $errorsProject[] = "Le domaine est obligatoire.";
    
    // Validation des dates
    if (!empty($start_date) && !empty($end_date)) {
        try {
            $startDateTime = new DateTime($start_date);
            $endDateTime = new DateTime($end_date);
            if ($startDateTime >= $endDateTime) {
                $errorsProject[] = 'La date de fin doit être postérieure à la date de début.';
            }
        } catch (Exception $e) {
            $errorsProject[] = 'Format de date invalide.';
        }
    }
    
    // Validation du statut
    if (!empty($status) && !in_array($status, array_column(ProjectStatus::cases(), 'value'))) {
        $errorsProject[] = 'Statut invalide.';
    }

    // Sauvegarde si pas d'erreurs
    if (empty($errorsProject)) {
        $updateData = [
            'title' => $title,
            'domain_id' => $domain_id,
            'needs' => $needs,
            'start_date' => $start_date ?: null,
            'end_date' => $end_date ?: null,
            'status' => $status ?: 'planification'
        ];
        
        if (updateProject($pdo, $id, $updateData)) {
            $messagesProject[] = "Le projet a été mis à jour avec succès !";
            $project = getProjectById($pdo, $id); // Rafraîchir
            $projectProgress = getProjectProgress($pdo, $id); // Rafraîchir progression
        } else {
            $errorsProject[] = "Erreur lors de la mise à jour du projet.";
        }
    }
}

?>

<div class="container col-xxl-8 py-4">
    <!-- Messages d'alerte -->
    <?php foreach ($errorsProject as $error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
    
    <?php foreach ($messagesProject as $message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-pencil-square me-2"></i>Modifier le projet</h1>
        <a href="show-project.php?id=<?= $id ?>" class="btn btn-outline-secondary">
            <i class="bi bi-eye me-1"></i>Voir le projet
        </a>
    </div>

    <!-- Progression actuelle -->
    <?php if ($projectProgress['total_tasks'] > 0): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-bar-chart me-2"></i>Progression actuelle
                </h6>
                <div class="d-flex align-items-center">
                    <div class="progress flex-grow-1 me-3" style="height: 20px;">
                        <div class="progress-bar" 
                             style="width: <?= $projectProgress['percentage'] ?>%"
                             aria-valuenow="<?= $projectProgress['percentage'] ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            <?= $projectProgress['percentage'] ?>%
                        </div>
                    </div>
                    <span class="text-muted">
                        <?= $projectProgress['completed_tasks'] ?>/<?= $projectProgress['total_tasks'] ?> tâches
                    </span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Formulaire d'édition -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-gear me-2"></i>Informations du projet
            </h5>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="row">
                    <!-- Colonne de gauche -->
                    <div class="col-md-6">
                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre *</label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($project['title']) ?>" 
                                   required>
                        </div>

                        <!-- Domaine -->
                        <div class="mb-3">
                            <label for="domain_id" class="form-label">Domaine *</label>
                            <select name="domain_id" id="domain_id" class="form-select" required>
                                <option value="">-- Sélectionnez un domaine --</option>
                                <?php foreach ($domains as $domain): ?>
                                    <option value="<?= $domain['id'] ?>" 
                                            <?= ($domain['id'] == $project['domain_id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($domain['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dates -->
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="start_date" class="form-label">Date de début</label>
                                <input type="date" 
                                       name="start_date" 
                                       id="start_date" 
                                       class="form-control"
                                       value="<?= htmlspecialchars($project['start_date'] ?? '') ?>">
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="end_date" class="form-label">Date de fin</label>
                                <input type="date" 
                                       name="end_date" 
                                       id="end_date" 
                                       class="form-control"
                                       value="<?= htmlspecialchars($project['end_date'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- Statut -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut du projet</label>
                            <select name="status" id="status" class="form-select">
                                <?php foreach ($projectStatuses as $statusOption): ?>
                                    <option value="<?= $statusOption->value ?>" 
                                            <?= ($project['status'] ?? 'planification') === $statusOption->value ? 'selected' : '' ?>>
                                        <?= $statusOption->getLabel() ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Colonne de droite -->
                    <div class="col-md-6">
                        <!-- Besoins -->
                        <div class="mb-3">
                            <label for="needs" class="form-label">Besoins du projet</label>
                            <textarea name="needs" 
                                      id="needs" 
                                      class="form-control" 
                                      rows="10"
                                      placeholder="Décrivez les besoins, objectifs et contraintes de ce projet..."><?= htmlspecialchars($project['needs'] ?? '') ?></textarea>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Détaillez les fonctionnalités attendues, contraintes techniques, objectifs business...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-between flex-wrap gap-2 pt-3 border-top">
                    <div>
                        <button type="submit" name="saveProject" class="btn btn-primary me-2">
                            <i class="bi bi-check-lg me-1"></i>Sauvegarder les modifications
                        </button>
                        <a href="show-project.php?id=<?= $id ?>" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i>Annuler
                        </a>
                    </div>
                    
                    <a href="dashboard.php?tab=projets" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Retour à la liste
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . "/../templates/footer.php";
ob_end_flush();
?>