<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <?php
    // Gestion des messages de succès et d'erreur
    if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif;

    if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <h2>Mes Projets</h2>
    <div class="d-flex flex-column flex-md-row gap-3">
        <?php if (isUserConnected()) { ?>
            <a href="new-project.php" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Ajouter un projet
            </a>
        <?php } ?>
        <form method="get" action="dashboard.php" class="d-inline-block">
            <select name="domain" id="domain" onchange="this.form.submit()" class="form-select" style="width: auto;">
                <option value="">Filtrer par domaine</option>
                <?php foreach ($domains as $domain): ?>
                    <option value="<?= htmlspecialchars($domain['id']) ?>" <?= ($domain['id'] == $domain_id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($domain['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="tab" value="projets">
        </form>
    </div>
</div>

<div class="row">
    <?php if (isUserConnected()) {
        if ($projects) {
            foreach ($projects as $project) { ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-truncate">
                                    <?= htmlspecialchars($project['title']) ?>
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php
                                $tasks = getProjectTasks($pdo, $project['id']);
                                if ($tasks) {
                                    $tasks = array_slice($tasks, 0, 3); // Limiter à 3 tâches
                            ?>
                            <ul class="list-unstyled mb-3">
                                <?php foreach ($tasks as $task) { ?>
                                    <li class="d-flex align-items-center mb-2">
                                        <a class="me-2 text-decoration-none"
                                          href="?id=<?= $task['id'] ?>&action=updateTaskStatus&redirect=dashboard&task_id=<?= $task['id'] ?>&status=<?= !$task['status'] ?>&tab=projets<?= $domain_id ? '&domain=' . $domain_id : '' ?>">
                                          <i class="bi bi-check-circle<?= ($task['status'] ? '-fill text-success' : ' text-muted') ?>"></i>
                                        </a>
                                        <span class="<?= $task['status'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                            <?= htmlspecialchars($task['name']) ?>
                                        </span>
                                    </li>
                                <?php } ?>
                            </ul>
                            <?php 
                                $totalTasks = count(getProjectTasks($pdo, $project['id']));
                                if ($totalTasks > 3) {
                                    echo '<small class="text-muted">... et ' . ($totalTasks - 3) . ' autres tâches</small>';
                                }
                            } ?>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="show-project.php?id=<?= $project['id'] ?>" class="btn btn-primary btn-sm">Voir le projet</a>
                                    <span class="badge rounded-pill bg-primary">
                                        <i class="bi <?= htmlspecialchars($project['domain_icon'] ?? 'bi-folder') ?>"></i>
                                        <?= htmlspecialchars($project['domain_name'] ?? 'Domaine inconnu') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
        } else { ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-folder-x display-1 text-muted"></i>
                    <h4 class="mt-3">Aucun projet trouvé</h4>
                    <p class="text-muted">
                        <?php if ($domain_id) { ?>
                            Aucun projet dans ce domaine.
                        <?php } else { ?>
                            Créez votre premier projet pour commencer !
                        <?php } ?>
                    </p>
                    <a href="new-project.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-2"></i>Créer un projet
                    </a>
                </div>
            </div>
        <?php }
    } else { ?>
        <div class="col-12">
            <div class="alert alert-warning">
                <p>Pour consulter vos projets, veuillez vous connecter</p>
                <a href="login.php" class="btn btn-outline-primary">Login</a>
            </div>
        </div>
    <?php } ?>
</div>