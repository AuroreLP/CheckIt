<h2>Aperçu des projets</h2>
<div class="row mb-4">
    <div class="col-12 col-md-6 col-lg-3 mb-3">
        <div class="card text-center h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h3 class="text-primary"><?= $totalProjects ?? 0 ?></h3>
                <p class="mb-0">projets au total</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 mb-3">
        <div class="card text-center h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <?php 
                $completedProjectsCount = count($completedProjects ?? []);
                $completionRate = $totalProjects > 0 ? round(($completedProjectsCount / $totalProjects) * 100, 1) : 0;
                ?>
                <h3 class="text-success"><?= $completionRate ?>%</h3>
                <p class="mb-0">de projets terminés</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 mb-3">
        <div class="card text-center h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <?php 
                $totalTasks = getTotalTasks($pdo, $userId);
                $completedTasks = getCompletedTasks($pdo, $userId);
                $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                ?>
                <h3 class="text-info"><?= $taskCompletionRate ?>%</h3>
                <p class="mb-0">de tâches complétées</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3 mb-3">
        <div class="card text-center h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <?php 
                $overdueTasksCount = count(getOverdueTasks($pdo, $userId));
                $colorClass = $overdueTasksCount > 0 ? 'text-danger' : 'text-success';
                ?>
                <h3 class="<?= $colorClass ?>"><?= $overdueTasksCount ?></h3>
                <p class="mb-0">tâches en retard</p>
                <?php if ($overdueTasksCount == 0): ?>
                    <small class="text-success">Tout à jour</small>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <!-- Messages d'alerte -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <h2>Mes Projets</h2>
    <div class="d-flex flex-column flex-md-row gap-3">
        <a href="new-project.php" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Ajouter un projet
        </a>
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
    <?php if (!empty($activeProjects) || !empty($completedProjects)): ?>
        
        <!-- PROJETS ACTIFS -->
        <?php if (!empty($activeProjects)): ?>
            <div class="col-12 mb-4">
                <h3 class="text-primary mb-3">
                    <i class="bi bi-play-circle me-2"></i>Projets actifs 
                    <span class="badge bg-primary ms-2"><?= count($activeProjects) ?></span>
                </h3>
            </div>
            
            <?php foreach ($activeProjects as $project): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-truncate">
                                    <?= htmlspecialchars($project['title']) ?>
                                </h5>
                                <span class="badge rounded-pill bg-primary px-3 py-2">
                                    <?= htmlspecialchars($project['domain_name'] ?? 'Domaine inconnu') ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <!-- Statut et domaine -->
                            <div class="mb-3">                                
                                <!-- Dates du projet -->
                                <?php if (!empty($project['start_date']) || !empty($project['end_date'])): ?>
                                    <div class="small text-muted mb-2">               
                                        <?php if (!empty($project['end_date'])): ?>
                                            <i class="bi bi-calendar-check me-1"></i>
                                            Fin prévue le <strong><?= date('d/m/Y', strtotime($project['end_date'])) ?></strong> 
                                            
                                            <!-- Alertes de dates -->
                                            <?php if (!empty($project['date_alerts'])): ?>
                                                <span class="badge bg-<?= $project['date_alerts']['type'] ?> ms-1 <?= $project['date_alerts']['type'] === 'warning' ? 'text-dark' : '' ?>">
                                                    <i class="bi <?= $project['date_alerts']['icon'] ?>"></i>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Barre de progression -->
                            <?php if ($project['progress']['total_tasks'] > 0): ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted fw-medium">Progression</small>
                                        <small class="text-muted">
                                            <?= $project['progress']['completed_tasks'] ?>/<?= $project['progress']['total_tasks'] ?> tâches
                                        </small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar <?= $project['progress']['percentage'] == 100 ? 'progress-complete' : ($project['progress']['percentage'] >= 75 ? 'progress-high' : ($project['progress']['percentage'] >= 50 ? 'progress-medium' : 'progress-low')) ?>" 
                                             style="width: <?= $project['progress']['percentage'] ?>%"
                                             title="<?= $project['progress']['percentage'] ?>% complété">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Prochaines tâches -->
                            <?php if (!empty($project['next_tasks'])): ?>
                                <div class="mb-3">
                                    <small class="text-muted fw-medium d-block mb-2">
                                        Les deux prochaines tâches:
                                    </small>
                                    <ul class="list-unstyled mb-0">
                                        <?php foreach ($project['next_tasks'] as $task): ?>
                                            <li class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center flex-grow-1">
                                                    <a class="me-2 text-decoration-none"
                                                       href="?id=<?= $task['id'] ?>&action=updateTaskStatus&redirect=dashboard&task_id=<?= $task['id'] ?>&status=1&tab=projets<?= $domain_id ? '&domain=' . $domain_id : '' ?>">
                                                        <i class="bi bi-check-circle text-muted"></i>
                                                    </a>
                                                    <div class="flex-grow-1">
                                                        <div class="small fw-medium">
                                                            <?= htmlspecialchars($task['name']) ?>
                                                        </div>
                                                        <?php if (!empty(trim($task['description']))): ?>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column align-items-end gap-1">
                                                    <span class="badge <?= $task['is_overdue'] ? 'bg-danger' : ($task['is_due_soon'] ? 'bg-warning text-dark' : 'bg-secondary') ?>" 
                                                          style="font-size: 0.65rem;">
                                                        <?= date('d/m', strtotime($task['deadline'])) ?>
                                                    </span>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php elseif ($project['progress']['total_tasks'] > 0): ?>
                                <div class="mb-3">
                                    <small class="text-muted fw-medium d-block mb-2">
                                        <i class="bi bi-check-all me-1"></i>Tâches
                                    </small>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-check-circle-fill text-success me-1"></i>
                                        Toutes les tâches sont terminées !
                                    </p>
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <small class="text-muted fw-medium d-block mb-2">
                                        <i class="bi bi-list-task me-1"></i>Tâches
                                    </small>
                                    <p class="text-muted small mb-0 fst-italic">Aucune tâche créée</p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Boutons d'action -->
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="show-project.php?id=<?= $project['id'] ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>Voir le projet
                                    </a>
                                    
                                    <?php if ($project['progress']['total_tasks'] > 2): ?>
                                        <small class="text-muted">
                                            +<?= $project['progress']['total_tasks'] - 2 ?> autres tâches
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- PROJETS TERMINÉS -->
        <?php if (!empty($completedProjects)): ?>
            <div class="col-12 mb-3 mt-5">
                <h3 class="text-primary mb-3">
                    <i class="bi bi-check-circle me-2"></i>Projets terminés 
                    <span class="badge bg-primary ms-2"><?= count($completedProjects) ?></span>
                </h3>
            </div>
            
            <?php foreach ($completedProjects as $project): ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 border-success">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-truncate">
                                    <?= htmlspecialchars($project['title']) ?>
                                </h5>
                                <span class="badge rounded-pill bg-primary px-3 py-2">
                                    <i class="bi <?= htmlspecialchars($project['domain_icon'] ?? 'bi-folder') ?>"></i>
                                    <?= htmlspecialchars($project['domain_name'] ?? 'Domaine inconnu') ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <!-- Barre de progression (100%) -->
                            <?php if ($project['progress']['total_tasks'] > 0): ?>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-info fw-medium">Progression</small>
                                        <small class="text-info fw-medium">
                                            <?= $project['progress']['completed_tasks'] ?>/<?= $project['progress']['total_tasks'] ?> tâches
                                        </small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-info" 
                                             style="width: 100%"
                                             title="Projet terminé !">
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Message de félicitations -->
                            <div class="mb-3 text-center">
                                <i class="bi bi-trophy text-warning" style="font-size: 1.5rem;"></i>
                                <p class="small mb-0 mt-1 fw-medium">
                                    Projet terminé avec succès !
                                </p>
                            </div>
                            
                            <!-- Bouton d'action -->
                            <div class="mt-auto">
                                <div class="d-flex justify-content-center">
                                    <a href="show-project.php?id=<?= $project['id'] ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i>Voir le projet
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-folder-x display-1 text-muted"></i>
                <h4 class="mt-3">Aucun projet trouvé</h4>
                <p class="text-muted">
                    <?php if ($domain_id): ?>
                        Aucun projet dans ce domaine.
                    <?php else: ?>
                        Créez votre premier projet pour commencer !
                    <?php endif; ?>
                </p>
                <a href="new-project.php" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Créer un projet
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>