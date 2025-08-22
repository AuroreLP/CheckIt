<?php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/project.php';

// Vérifier si l'utilisateur est connecté
if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

// DEBUG : Afficher les paramètres reçus
error_log("DEBUG delete-project: GET params = " . print_r($_GET, true));
error_log("DEBUG delete-project: User ID = " . $_SESSION['user']['id']);

// Vérifier si l'ID du projet est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    error_log("DEBUG delete-project: ID invalide ou manquant");
    header('Location: dashboard.php?tab=projets&error=' . urlencode('ID de projet invalide.'));
    exit();
}

$projectId = (int)$_GET['id'];
$userId = $_SESSION['user']['id'];

error_log("DEBUG delete-project: Tentative suppression projet $projectId par user $userId");

try {
    // Vérifier que le projet appartient bien à l'utilisateur connecté
    $project = getProjectById($pdo, $projectId);
    
    // DEBUG : Afficher le résultat
    if ($project) {
        error_log("DEBUG delete-project: Projet trouvé - " . print_r($project, true));
    } else {
        error_log("DEBUG delete-project: Projet NOT FOUND pour ID $projectId");
        
        // Essayer une recherche simple pour debug
        $simpleProject = getProjectByIdSimple($pdo, $projectId);
        if ($simpleProject) {
            error_log("DEBUG delete-project: Mais projet trouvé avec requête simple - " . print_r($simpleProject, true));
        } else {
            error_log("DEBUG delete-project: Projet vraiment introuvable même avec requête simple");
        }
    }
    
    if (!$project) {
        header('Location: dashboard.php?tab=projets&error=' . urlencode('Projet introuvable.'));
        exit();
    }
    
    if ($project['user_id'] != $userId) {
        error_log("DEBUG delete-project: User non autorisé - projet user_id: " . $project['user_id'] . " vs current user: $userId");
        header('Location: dashboard.php?tab=projets&error=' . urlencode('Vous n\'êtes pas autorisé à supprimer ce projet.'));
        exit();
    }
    
    // Supprimer le projet
    $success = deleteProject($pdo, $projectId);
    
    if ($success) {
        error_log("DEBUG delete-project: Suppression réussie");
        header('Location: dashboard.php?tab=projets&success=' . urlencode('Projet supprimé avec succès.'));
    } else {
        error_log("DEBUG delete-project: Échec de la suppression");
        header('Location: dashboard.php?tab=projets&error=' . urlencode('Erreur lors de la suppression du projet.'));
    }
    
} catch (Exception $e) {
    error_log("DEBUG delete-project: Exception - " . $e->getMessage());
    header('Location: dashboard.php?tab=projets&error=' . urlencode('Une erreur est survenue lors de la suppression.'));
}

exit();
?>