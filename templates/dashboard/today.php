<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Focus du jour - <?= date('d/m/Y') ?></h2>
    <small class="text-muted">Dernière mise à jour : <?= date('H:i') ?></small>
</div>

<!-- Tâches en retard (priorité) -->
<?php if (!empty($overdueTasks)): ?>
<div class="mb-4">
    <h5 class="text-danger mb-3">
        <i class="bi bi-exclamation-triangle me-2"></i>
        Tâches en retard (<?= count($overdueTasks) ?>)
    </h5>
    <div class="row">
        <?php foreach ($overdueTasks as $task): ?>
        <div class="col-12 col-md-6 col-lg-4 mb-3">
            <div class="card border-danger task-item h-100" data-task-id="<?= $task['id'] ?>">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-start mb-2">
                        <i class="bi bi-check-circle text-muted me-3 complete-task-btn" 
                           data-task-id="<?= $task['id'] ?>" 
                           style="font-size: 1.2rem; cursor: pointer;"
                           title="Marquer comme terminé"></i>
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-2"><?= htmlspecialchars($task['name'] ?? $task['title'] ?? 'Tâche sans nom') ?></h6>
                        </div>
                    </div>
                    
                    <!-- Badge projet mis en avant -->
                    <div class="mb-2">
                        <span class="badge bg-primary rounded-pill">
                            <i class="bi bi-folder me-1"></i>
                            <?= htmlspecialchars($task['project_title']) ?>
                        </span>
                    </div>
                    
                    <!-- Description avec hauteur fixe -->
                    <div class="flex-grow-1 mb-2" style="min-height: 3em;">
                        <?php if (!empty($task['description'])): ?>
                            <?php 
                            $maxLength = 80;
                            $shortDescription = strlen($task['description']) > $maxLength 
                                ? substr($task['description'], 0, $maxLength) . '...' 
                                : $task['description']; 
                            ?>
                            <p class="card-text text-muted small mb-0" 
                               title="<?= htmlspecialchars($task['description']) ?>">
                                <?= htmlspecialchars($shortDescription) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Statut en retard en bas -->
                    <div class="mt-auto">
                        <small class="text-danger">
                            <i class="bi bi-clock me-1"></i>
                            En retard de <?= $task['days_overdue'] ?> jour<?= $task['days_overdue'] > 1 ? 's' : '' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <!-- Aujourd'hui -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-day me-2"></i>
                    Aujourd'hui (<?= count($todayTasks) ?>)
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($todayTasks)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($todayTasks as $task): ?>
                    <div class="list-group-item task-item" data-task-id="<?= $task['id'] ?>">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-muted me-2 complete-task-btn" 
                               data-task-id="<?= $task['id'] ?>" 
                               style="font-size: 1.2rem; cursor: pointer;"
                               title="Marquer comme terminé"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small"><?= htmlspecialchars($task['name'] ?? $task['title'] ?? 'Tâche sans nom') ?></h6>
                                <span class="badge bg-secondary rounded-pill mb-1" style="font-size: 0.65rem;">
                                    <?= htmlspecialchars($task['project_title']) ?>
                                </span>
                                <?php if (!empty($task['description'])): ?>
                                    <?php 
                                    $shortDesc = strlen($task['description']) > 40 
                                        ? substr($task['description'], 0, 40) . '...' 
                                        : $task['description']; 
                                    ?>
                                    <p class="mb-0 text-muted" style="font-size: 0.75rem;" 
                                       title="<?= htmlspecialchars($task['description']) ?>">
                                        <?= htmlspecialchars($shortDesc) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="p-3 text-center text-muted">
                    <i class="bi bi-check-circle display-6"></i>
                    <p class="mt-2 mb-0 small">Aucune tâche aujourd'hui</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Demain -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-plus me-2"></i>
                    Demain (<?= count($tomorrowTasks) ?>)
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($tomorrowTasks)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($tomorrowTasks as $task): ?>
                    <div class="list-group-item task-item" data-task-id="<?= $task['id'] ?>">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-muted me-2 complete-task-btn" 
                               data-task-id="<?= $task['id'] ?>" 
                               style="font-size: 1.2rem; cursor: pointer;"
                               title="Marquer comme terminé"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small"><?= htmlspecialchars($task['name'] ?? $task['title'] ?? 'Tâche sans nom') ?></h6>
                                <span class="badge bg-secondary rounded-pill mb-1" style="font-size: 0.65rem;">
                                    <?= htmlspecialchars($task['project_title']) ?>
                                </span>
                                <?php if (!empty($task['description'])): ?>
                                    <?php 
                                    $shortDesc = strlen($task['description']) > 40 
                                        ? substr($task['description'], 0, 40) . '...' 
                                        : $task['description']; 
                                    ?>
                                    <p class="mb-0 text-muted" style="font-size: 0.75rem;" 
                                       title="<?= htmlspecialchars($task['description']) ?>">
                                        <?= htmlspecialchars($shortDesc) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="p-3 text-center text-muted">
                    <i class="bi bi-calendar-plus display-6"></i>
                    <p class="mt-2 mb-0 small">Aucune tâche demain</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Prochainement (3 jours suivants) -->
    <div class="col-12 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-range me-2"></i>
                    Prochainement (<?= count($next3DaysTasks) ?>)
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($next3DaysTasks)): ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($next3DaysTasks as $task): ?>
                    <div class="list-group-item task-item" data-task-id="<?= $task['id'] ?>">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle text-muted me-2 complete-task-btn" 
                               data-task-id="<?= $task['id'] ?>" 
                               style="font-size: 1.2rem; cursor: pointer;"
                               title="Marquer comme terminé"></i>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small"><?= htmlspecialchars($task['name'] ?? $task['title'] ?? 'Tâche sans nom') ?></h6>
                                <span class="badge bg-secondary rounded-pill mb-1" style="font-size: 0.65rem;">
                                    <?= htmlspecialchars($task['project_title']) ?>
                                </span>
                                <?php if (!empty($task['description'])): ?>
                                    <?php 
                                    $shortDesc = strlen($task['description']) > 40 
                                        ? substr($task['description'], 0, 40) . '...' 
                                        : $task['description']; 
                                    ?>
                                    <p class="mb-0 text-muted" style="font-size: 0.75rem;" 
                                       title="<?= htmlspecialchars($task['description']) ?>">
                                        <?= htmlspecialchars($shortDesc) ?>
                                    </p>
                                <?php endif; ?>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    <?= date('d/m', strtotime($task['deadline'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="p-3 text-center text-muted">
                    <i class="bi bi-calendar-range display-6"></i>
                    <p class="mt-2 mb-0 small">Rien de prévu prochainement</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/task-complete.js"></script>