<?php
require_once __DIR__ . '/../src/session.php';

// Vérifier si l'utilisateur est connecté
if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

// Inclure les fichiers nécessaires
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/authentication.php';
require_once __DIR__ . '/../src/project.php';
require_once __DIR__ . '/../src/domain.php';
require_once __DIR__ . '/../src/task.php';
require_once __DIR__ . '/../src/statistics.php';
require_once __DIR__ . '/../src/profile.php';

// Variables globales pour les templates
$userId = $_SESSION['user']['id'];
$domain_id = isset($_GET['domain']) ? (int)$_GET['domain'] : null;
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'projets';

// Récupérer les données selon l'onglet actif
switch ($activeTab) {
    case 'projets':
        $domains = getAllDomains($pdo);
        $allProjects = getProjectsByUserAndDomain($pdo, $userId, $domain_id);
        
        // Séparer les projets actifs et terminés avec toutes les données préparées
        $activeProjects = [];
        $completedProjects = [];
        
        foreach ($allProjects as $project) {
            // Calculer la progression
            $project['progress'] = getProjectProgress($pdo, $project['id']);
            
            // Récupérer le statut
            $project['status_enum'] = ProjectStatus::tryFrom($project['status'] ?? 'planification');
            
            // Récupérer et préparer les prochaines tâches
            $allTasks = getProjectTasks($pdo, $project['id']);
            $pendingTasks = array_filter($allTasks, function($task) {
                return !$task['status']; // Tâches non terminées
            });
            
            // Trier par deadline (les plus proches en premier)
            usort($pendingTasks, function($a, $b) {
                return strtotime($a['deadline']) - strtotime($b['deadline']);
            });
            
            $project['next_tasks'] = array_slice($pendingTasks, 0, 2);
            
            // Calculer les alertes de dates pour les tâches
            foreach ($project['next_tasks'] as &$task) {
                $today = new DateTime();
                $today->setTime(0, 0, 0);
                $deadline = new DateTime($task['deadline']);
                $deadline->setTime(0, 0, 0);
                
                $task['is_overdue'] = $deadline < $today;
                $task['is_due_soon'] = !$task['is_overdue'] && $deadline <= (clone $today)->modify('+3 days');
            }
            
            // Calculer les alertes de dates pour le projet
            $project['date_alerts'] = [];
            if (!empty($project['end_date'])) {
                $today = new DateTime();
                $endDate = new DateTime($project['end_date']);
                
                if ($endDate < $today && $project['status_enum'] !== ProjectStatus::Termine) {
                    $project['date_alerts'] = [
                        'type' => 'danger',
                        'text' => 'En retard',
                        'icon' => 'bi-exclamation-triangle'
                    ];
                } elseif ($endDate <= (clone $today)->modify('+7 days') && $project['status_enum'] !== ProjectStatus::Termine) {
                    $project['date_alerts'] = [
                        'type' => 'warning',
                        'text' => 'Échéance proche',
                        'icon' => 'bi-clock'
                    ];
                }
            }
            
            // Classer le projet (actif ou terminé)
            if ($project['status_enum'] === ProjectStatus::Termine && 
                $project['progress']['percentage'] == 100) {
                $completedProjects[] = $project;
            } else {
                $activeProjects[] = $project;
            }
        }
        break;
        
    case 'domaines':
        $domains = getAllDomains($pdo);
        // Ajouter ici la logique pour récupérer les statistiques des domaines
        break;
        
    case 'statistiques':
        // Calculer les statistiques
        $totalProjects = count(getProjectsByUserAndDomain($pdo, $userId, null));
        $activeProjects = getActiveProjectsByUser($pdo, $userId);
        $totalDomains = count(getAllDomains($pdo));
        $totalChecks = getTotalChecks($pdo, $userId);
        $completedTasksCount = getCompletedTasks($pdo, $userId);
        $pendingTasksCount = getPendingTasks($pdo, $userId);
        $overallProgress = getOverallProgress($pdo, $userId);
        $domainStats = getStatisticsByDomain($pdo, $userId);
        $recentProjects = getRecentProjects($pdo, $userId);
        break;
        
    case 'profil':
        // Récupérer les données du profil utilisateur
        $userProfile = getUserProfile($pdo, $userId);
        // Récupérer aussi les statistiques pour l'affichage
        $totalProjects = count(getProjectsByUserAndDomain($pdo, $userId, null));
        $completedTasksCount = getCompletedTasks($pdo, $userId);
        $totalChecks = getTotalChecks($pdo, $userId);
        $overallProgress = getOverallProgress($pdo, $userId);
        break;
}

// Inclure le header
require_once __DIR__ . '/../templates/header.php';
?>

<main class="container my-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <?php require_once __DIR__ . '/../templates/dashboard/sidebar.php'; ?>
        </div>
        
        <!-- Contenu principal -->
        <div class="col-md-9">
            <div class="tab-content">
                <?php
                // Inclure le template correspondant à l'onglet actif
                switch ($activeTab) {
                    case 'projets':
                        require_once __DIR__ . '/../templates/dashboard/projets.php';
                        break;
                    case 'domaines':
                        require_once __DIR__ . '/../templates/dashboard/domaines.php';
                        break;
                    case 'profil':
                        require_once __DIR__ . '/../templates/dashboard/profil.php';
                        break;
                    case 'statistiques':
                        require_once __DIR__ . '/../templates/dashboard/statistiques.php';
                        break;
                    default:
                        require_once __DIR__ . '/../templates/dashboard/projets.php';
                }
                ?>
            </div>
        </div>
    </div>
</main>

<script>
function confirmDelete(projectId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce projet ?')) {
        window.location.href = `delete-project.php?id=${projectId}`;
    }
}
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>