<!-- templates/project/show.php -->

<!-- Messages d'alerte -->
<?php if ($success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="container">
    <!-- En-tête du projet -->
    <div class="d-flex justify-content-between align-items-end mt-2">
        <h1><?= htmlspecialchars($project['title']) ?></h1>
        <span class="badge rounded-pill text-bg-primary fs-4 mb-2">
            <i class="bi <?= htmlspecialchars($project['domain_icon'] ?? '') ?>"></i>
            <?= htmlspecialchars($project['domain_name'] ?? 'Domaine inconnu') ?>
        </span>
    </div>

    <!-- Section éditable du projet -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-info-circle me-2"></i>
                Informations du projet
            </h5>
            <button type="button" 
                    id="editProjectBtn" 
                    class="btn btn-outline-primary btn-sm"
                    data-original-title="<?= htmlspecialchars($project['title']) ?>"
                    data-original-domain-id="<?= htmlspecialchars($project['domain_id']) ?>"
                    data-original-needs="<?= htmlspecialchars($project['needs'] ?? '') ?>">
                <i class="bi bi-pencil me-1"></i>Modifier
            </button>
        </div>
        <div class="card-body">
            <!-- Mode lecture -->
            <div id="projectDisplay">
                <div class="row gy-3 gy-md-0">
                    <!-- Colonne de gauche : Titre + Domaine -->
                    <!-- Mobile: pleine largeur, Desktop: 50% -->
                    <div class="col-12 col-md-6 project-info-left">
                        <!-- Titre -->
                        <div class="mb-3 mb-md-4">
                            <strong class="d-block mb-1">Titre :</strong>
                            <p class="mb-0 fs-5 fs-md-4 fw-medium"><?= htmlspecialchars($project['title']) ?></p>
                        </div>
                        <!-- Domaine -->
                        <div class="mb-3 mb-md-4">
                            <strong class="d-block mb-1">Domaine :</strong>
                            <p class="mb-0">
                                <i class="bi <?= htmlspecialchars($project['domain_icon'] ?? 'bi-folder') ?> me-2 text-primary"></i>
                                <span class="fw-medium"><?= htmlspecialchars($project['domain_name'] ?? 'Non défini') ?></span>
                            </p>
                        </div>
                    </div>
    
                    <!-- Colonne de droite : Besoins -->
                    <!-- Mobile: pleine largeur (en dessous), Desktop: 50% (à côté) -->
                    <div class="col-12 col-md-6 project-info-right">
                        <strong class="d-block mb-2">Besoins du projet :</strong>
                        <div class="needs-content">
                            <?php if (!empty($project['needs'])): ?>
                                <?= nl2br(htmlspecialchars($project['needs'])) ?>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Aucun besoin spécifié pour ce projet.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mode édition -->
            <div id="projectEdit" style="display: none;">
                <form action="" method="post">
                    <input type="hidden" name="updateProject" value="1">
                    
                    <div class="row gy-3 gy-md-0">
                        <!-- Colonne de gauche : Titre + Domaine -->
                        <!-- Mobile: pleine largeur, Desktop: 50% -->
                        <div class="col-12 col-md-6 project-info-left">
                            <!-- Titre -->
                            <div class="mb-3 mb-md-4">
                                <label for="projectTitle" class="form-label fw-bold">Titre *</label>
                                <input type="text" 
                                    name="title" 
                                    id="projectTitle" 
                                    class="form-control" 
                                    value="<?= htmlspecialchars($project['title']) ?>" 
                                    required
                                    placeholder="Nom du projet">
                            </div>
                            
                            <!-- Domaine -->
                            <div class="mb-3 mb-md-4">
                                <label for="projectDomain" class="form-label fw-bold">Domaine *</label>
                                <select name="domain_id" id="projectDomain" class="form-select" required>
                                    <option value="">-- Sélectionnez un domaine --</option>
                                    <?php foreach ($domains as $domain): ?>
                                        <option value="<?= $domain['id'] ?>" 
                                                <?= ($project['domain_id'] == $domain['id']) ? 'selected' : '' ?>>
                                            <i class="bi <?= htmlspecialchars($domain['icon'] ?? 'bi-folder') ?>"></i>
                                            <?= htmlspecialchars($domain['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Colonne de droite : Besoins -->
                        <!-- Mobile: pleine largeur (en dessous), Desktop: 50% (à côté) -->
                        <div class="col-12 col-md-6 project-info-right">
                            <label for="projectNeeds" class="form-label fw-bold d-block mb-2">Besoins du projet</label>
                            <textarea name="needs" 
                                    id="projectNeeds" 
                                    class="form-control needs-textarea" 
                                    rows="6"
                                    placeholder="Décrivez les besoins, objectifs et contraintes de ce projet..."><?= htmlspecialchars($project['needs'] ?? '') ?></textarea>
                            <div class="form-text mt-1">
                                <i class="bi bi-info-circle me-1"></i>
                                Détaillez les fonctionnalités attendues, les contraintes techniques, les objectifs business...
                            </div>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-start mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary">
                            Sauvegarder les modifications
                        </button>
                        <button type="button" id="cancelEditBtn" class="btn btn-secondary">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ajouter une tâche -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-task me-2"></i>
                Ajouter une tâche au projet
            </h5>
        </div>
        <div class="card-body">
            <!-- Formulaire d'ajout de tâche -->
            <form method="post" class="row gy-2 gx-3 align-items-end mb-4">
                <div class="col-md-2">
                    <label class="form-label">Nom de la tâche</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">
                        Description 
                        <span class="form-text d-inline ms-2">
                            <span id="charCountAdd">0</span>/200 caractères
                        </span>
                    </label>
                    <input type="text" name="description" class="form-control" maxlength="200" 
                        id="taskDescriptionAdd" onkeyup="updateCharCount('taskDescriptionAdd', 'charCountAdd')">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Phase</label>
                    <select name="phase" class="form-select" required>
                        <option value="">Choisir...</option>
                        <?php foreach ($phases as $case): ?>
                            <option value="<?= $case->value ?>">
                                <?= ucfirst($case->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date limite</label>
                    <input type="date" name="deadline" class="form-control" required>
                </div>
                <div class="col-md-1">
                    <button type="submit" name="saveTask" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Section liste des tâches -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-task me-2"></i>
                Tâches du projet
            </h5>
        </div>

        <div class="card-body">
            <!-- Liste des tâches -->
            <?php if ($tasks): ?>
                <div class="accordion" id="accordionTasks">
                    <?php foreach ($tasks as $task): ?>
                        <?php 
                        $today = new DateTime();
                        $deadline = new DateTime($task['deadline']);
                        $isOverdue = !$task['status'] && $deadline < $today;
                        $isDueSoon = !$task['status'] && !$isOverdue && $deadline <= (clone $today)->modify('+3 days');
                        ?>
                        
                        <!-- Item avec bordure colorée Bootstrap -->
                        <div class="accordion-item mb-3 <?= $isOverdue ? 'border-danger border-3' : ($isDueSoon ? 'border-warning border-3' : '') ?>">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed p-3" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapse-<?= $task['id'] ?>">
                                    
                                    <div class="w-100">
                                        <!-- Ligne principale : toujours visible -->
                                        <div class="d-flex align-items-center mb-2 mb-md-0">
                                            <a class="me-3 text-decoration-none" 
                                            href="?id=<?= $project_id ?>&action=updateTaskStatus&task_id=<?= $task['id'] ?>&status=<?= !$task['status'] ?>"
                                            onclick="event.stopPropagation();">
                                                <i class="bi bi-check-circle<?= ($task['status'] ? '-fill text-success' : ' text-muted') ?> fs-5"></i>
                                            </a>
                                            <div class="flex-grow-1">
                                                <div class="<?= $task['status'] ? 'text-muted' : 'fw-medium' ?>">
                                                    <?= htmlspecialchars($task['name']) ?>
                                                </div>
                                                
                                                <!-- Affichage de la description si elle existe -->
                                                <?php if (!empty(trim($task['description']))): ?>
                                                    <div class="text-muted small mt-1">
                                                        <?= htmlspecialchars($task['description']) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Desktop seulement : badges à droite -->
                                            <div class="d-none d-md-flex gap-2 ms-3 me-3">
                                                <span class="badge <?= $isOverdue ? 'bg-danger' : ($isDueSoon ? 'bg-warning text-dark' : 'bg-secondary') ?>">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    <?= date('d/m', strtotime($task['deadline'])) ?>
                                                </span>
                                                <span class="badge bg-primary">
                                                    <?= ucfirst($task['phase']) ?>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <!-- Mobile seulement : infos empilées -->
                                        <div class="d-md-none ms-5 pt-2">
                                            <div class="mb-2">
                                                <span class="badge <?= $isOverdue ? 'bg-danger' : ($isDueSoon ? 'bg-warning text-dark' : 'bg-secondary') ?>">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    <?= date('d/m/Y', strtotime($task['deadline'])) ?>
                                                    <?php if ($isOverdue): ?>
                                                        <i class="bi bi-exclamation-triangle ms-1"></i>
                                                    <?php elseif ($isDueSoon): ?>
                                                        <i class="bi bi-clock ms-1"></i>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <div class="mb-2">
                                                <span class="badge bg-info">
                                                    <i class="bi bi-gear me-1"></i>
                                                    <?= ucfirst($task['phase']) ?>
                                                </span>
                                            </div>
                                            <div class="small text-muted fst-italic">
                                                <i class="bi bi-chevron-down me-1"></i>
                                                Toucher pour modifier
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            
                            <div id="collapse-<?= $task['id'] ?>" class="accordion-collapse collapse" data-bs-parent="#accordionTasks">
                                <div class="accordion-body">
                                    <form method="post">
                                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Nom de la tâche</label>
                                                <input type="text" name="name" class="form-control" 
                                                    value="<?= htmlspecialchars($task['name']) ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Phase</label>
                                                <select name="phase" class="form-select">
                                                    <?php foreach ($phases as $case): ?>
                                                        <option value="<?= $case->value ?>" 
                                                                <?= $task['phase'] === $case->value ? 'selected' : '' ?>>
                                                            <?= ucfirst($case->value) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Date limite</label>
                                                <input type="date" name="deadline" class="form-control" 
                                                    value="<?= htmlspecialchars($task['deadline']) ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Description 
                                                <span class="form-text d-inline ms-2">
                                                    <span id="charCountEdit<?= $task['id'] ?>"><?= mb_strlen($task['description']) ?></span>/200 caractères
                                                </span>
                                            </label>
                                            <textarea name="description" class="form-control" rows="3" maxlength="200"
                                                      id="taskDescriptionEdit<?= $task['id'] ?>"
                                                      onkeyup="updateCharCount('taskDescriptionEdit<?= $task['id'] ?>', 'charCountEdit<?= $task['id'] ?>')"><?= htmlspecialchars($task['description']) ?></textarea>
                                        </div>
                                        
                                        <div class="d-flex flex-column flex-sm-row justify-content-between gap-2">
                                            <button type="submit" name="editTask" class="btn btn-primary">
                                                <i class="bi bi-check-lg me-1"></i>Modifier la tâche
                                            </button>
                                            <a href="?id=<?= $project_id ?>&action=deleteProjectTask&task_id=<?= $task['id'] ?>"
                                            class="btn btn-danger"
                                            onclick="return confirm('Supprimer cette tâche ?');">
                                                <i class="bi bi-trash3 me-1"></i>Supprimer la tâche
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

        <!-- Actions du projet -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4 flex-wrap gap-2">
        <a href="dashboard.php?tab=projets" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Retour à la liste des projets
        </a>
        
        <form method="post" class="d-inline" 
              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce projet ?');">
            <input type="hidden" name="deleteProject" value="1">
            <button type="submit" class="btn btn-danger">
                <i class="bi bi-trash3-fill me-2"></i>Supprimer le projet
            </button>
        </form>
    </div>
</div>

<!-- JAVASCRIPT POUR LE COMPTEUR --> 
 <script>
function updateCharCount(textareaId, counterId) {
    const textarea = document.getElementById(textareaId);
    const counter = document.getElementById(counterId);
    const currentLength = textarea.value.length;
    
    counter.textContent = currentLength;
    
    // Changer la couleur selon la limite
    if (currentLength > 180) {
        counter.style.color = 'red';
    } else if (currentLength > 150) {
        counter.style.color = 'orange';
    } else {
        counter.style.color = '';
    }
}

// Initialiser le compteur pour le champ d'ajout au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const addField = document.getElementById('taskDescriptionAdd');
    if (addField) {
        updateCharCount('taskDescriptionAdd', 'charCountAdd');
    }
    
    // Initialiser les compteurs pour les champs d'édition
    <?php foreach ($tasks as $task): ?>
    const editField<?= $task['id'] ?> = document.getElementById('taskDescriptionEdit<?= $task['id'] ?>');
    if (editField<?= $task['id'] ?>) {
        updateCharCount('taskDescriptionEdit<?= $task['id'] ?>', 'charCountEdit<?= $task['id'] ?>');
    }
    <?php endforeach; ?>
});
</script>
<script src="assets/js/project-edit.js"></script>