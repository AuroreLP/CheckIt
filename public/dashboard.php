<?php
require_once __DIR__ . '/../src/session.php';

// Vérifier si l'utilisateur est connecté
if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/project.php';
require_once __DIR__ . '/../src/domain.php';
require_once __DIR__ . '/../src/task.php';

// Récupérer les données utilisateur
$userId = $_SESSION['user']['id'];
$domain_id = isset($_GET['domain']) ? (int)$_GET['domain'] : null;

// Récupérer tous les domaines
$domains = getAllDomains($pdo);

// Récupérer les projets filtrés
$projects = getProjectsByUserAndDomain($pdo, $userId, $domain_id);

// Inclure le header
require_once __DIR__ . '/../templates/header.php';
?>

<main class="container my-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle me-2"></i>
                        Dashboard
                    </h5>
                </div>
                <div class="card-body p-0">
                    <nav class="nav flex-column">
                        <a class="nav-link <?= $activeTab == 'projets' ? 'active' : '' ?>" href="#projets" data-bs-toggle="pill">
                            <i class="bi bi-folder me-2"></i>Mes Projets
                        </a>
                        <a class="nav-link <?= $activeTab == 'domaines' ? 'active' : '' ?>" href="#domaines" data-bs-toggle="pill">
                            <i class="bi bi-globe me-2"></i>Domaines
                        </a>
                        <a class="nav-link <?= $activeTab == 'profil' ? 'active' : '' ?>" href="#profil" data-bs-toggle="pill">
                            <i class="bi bi-gear me-2"></i>Paramètres
                        </a>
                        <a class="nav-link <?= $activeTab == 'statistiques' ? 'active' : '' ?>" href="#statistiques" data-bs-toggle="pill">
                            <i class="bi bi-bar-chart me-2"></i>Statistiques
                        </a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="col-md-9">
            <div class="tab-content">
        <?php 
        // Gérer l'onglet actif basé sur le paramètre URL
        $activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'projets';
        ?>
        <script>
        // Activer l'onglet correct au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            const activeTab = '<?= $activeTab ?>';
            const tabLink = document.querySelector(`[href="#${activeTab}"]`);
            const tabPane = document.querySelector(`#${activeTab}`);
            
            // Désactiver tous les onglets
            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Activer l'onglet sélectionné
            if (tabLink && tabPane) {
                tabLink.classList.add('active');
                tabPane.classList.add('show', 'active');
            }
        });

        function confirmDelete(projectId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) {
                window.location.href = `delete-project.php?id=${projectId}`;
            }
        }
        </script>
                <!-- Onglet Projets -->
                <div class="tab-pane fade <?= $activeTab == 'projets' ? 'show active' : '' ?>" id="projets">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
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
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                            <i class="bi bi-three-dots"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item" href="show-project.php?id=<?= $project['id'] ?>"><i class="bi bi-eye me-2"></i>Voir</a></li>
                                                            <li><a class="dropdown-item" href="edit-project.php?id=<?= $project['id'] ?>"><i class="bi bi-pencil me-2"></i>Modifier</a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete(<?= $project['id'] ?>)"><i class="bi bi-trash me-2"></i>Supprimer</a></li>
                                                        </ul>
                                                    </div>
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
                </div>

                <!-- Onglet Domaines -->
                <div class="tab-pane fade <?= $activeTab == 'domaines' ? 'show active' : '' ?>" id="domaines">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Mes Domaines</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDomainModal">
                            <i class="bi bi-plus me-2"></i>Nouveau Domaine
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Domaine</th>
                                    <th>Status</th>
                                    <th>Dernière vérification</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>example.com</td>
                                    <td><span class="badge bg-success">Actif</span></td>
                                    <td>Il y a 2h</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Onglet Profil -->
                <div class="tab-pane fade <?= $activeTab == 'profil' ? 'show active' : '' ?>" id="profil">
                    <h2>Paramètres du Profil</h2>
                    <div class="card">
                        <div class="card-body">
                            <form>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Nom d'utilisateur</label>
                                    <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars(getConnectedUserName()); ?>" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="user@example.com">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control" id="password">
                                </div>
                                <button type="submit" class="btn btn-primary">Sauvegarder</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Onglet Statistiques -->
                <div class="tab-pane fade <?= $activeTab == 'statistiques' ? 'show active' : '' ?>" id="statistiques">
                    <h2>Statistiques</h2>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-primary">12</h3>
                                    <p class="mb-0">Projets Total</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-success">8</h3>
                                    <p class="mb-0">Projets Actifs</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-warning">3</h3>
                                    <p class="mb-0">Domaines</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-info">156</h3>
                                    <p class="mb-0">Vérifications</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Nouveau Projet -->
<div class="modal fade" id="addProjectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau Projet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="projectName" class="form-label">Nom du projet</label>
                        <input type="text" class="form-control" id="projectName" required>
                    </div>
                    <div class="mb-3">
                        <label for="projectDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="projectDescription" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="projectDomain" class="form-label">Domaine</label>
                        <input type="url" class="form-control" id="projectDomain" placeholder="https://example.com">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Créer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouveau Domaine -->
<div class="modal fade" id="addDomainModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouveau Domaine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="domainName" class="form-label">Nom de domaine</label>
                        <input type="text" class="form-control" id="domainName" placeholder="example.com" required>
                    </div>
                    <div class="mb-3">
                        <label for="domainDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="domainDescription" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>