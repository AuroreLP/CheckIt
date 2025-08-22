<h2>Statistiques</h2>

<!-- Cartes de statistiques principales -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-primary"><?= $totalProjects ?? 0 ?></h3>
                <p class="mb-0">Projets Total</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-success"><?= count($activeProjects ?? []) ?></h3>
                <p class="mb-0">Projets Actifs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-warning"><?= $totalDomains ?? 0 ?></h3>
                <p class="mb-0">Domaines</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card text-center">
            <div class="card-body">
                <h3 class="text-info"><?= $totalChecks ?? 0 ?></h3>
                <p class="mb-0">Vérifications</p>
            </div>
        </div>
    </div>
</div>

<!-- Progression globale -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Progression Globale</h5>
            </div>
            <div class="card-body">
                <?php 
                $progress = $overallProgress ?? 0;
                $completedTasks = $completedTasksCount ?? 0;
                $pendingTasks = $pendingTasksCount ?? 0;
                ?>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tâches terminées</span>
                    <span><?= $completedTasks ?> / <?= $totalChecks ?? 0 ?></span>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?= $progress ?>%" 
                         aria-valuenow="<?= $progress ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <?= $progress ?>%
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col">
                        <small class="text-success">
                            <i class="bi bi-check-circle-fill"></i>
                            <?= $completedTasks ?> Terminées
                        </small>
                    </div>
                    <div class="col">
                        <small class="text-warning">
                            <i class="bi bi-clock"></i>
                            <?= $pendingTasks ?> En cours
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Activité récente -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Projets Récents</h5>
            </div>
            <div class="card-body">
                <?php if (isset($recentProjects) && $recentProjects): ?>
                    <ul class="list-unstyled mb-0">
                        <?php foreach ($recentProjects as $project): ?>
                            <li class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong><?= htmlspecialchars($project['title']) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="bi <?= htmlspecialchars($project['domain_icon'] ?? 'bi-folder') ?>"></i>
                                        <?= htmlspecialchars($project['domain_name'] ?? 'Sans domaine') ?>
                                    </small>
                                </div>
                                <small class="text-muted">
                                    <?= date('d/m', strtotime($project['created_at'])) ?>
                                </small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted text-center">Aucun projet récent</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>