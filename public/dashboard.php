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
        $projects = getProjectsByUserAndDomain($pdo, $userId, $domain_id);
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