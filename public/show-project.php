<?php
// public/show-project.php - Version mise à jour avec nouvelles fonctionnalités
require_once __DIR__ . '/../templates/header.php';
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/project.php';
require_once __DIR__ . '/../src/domain.php';
require_once __DIR__ . '/../src/task.php';

// Vérifier l'existence du projet
if (!isset($_GET['id'])) {
    header('Location: dashboard.php?tab=projets&error=' . urlencode('ID du projet manquant.'));
    exit();
}

// Variables pour le template
$project_id = (int)$_GET['id'];
$project = getProjectById($pdo, $project_id);
$domains = getAllDomains($pdo); // Pour le formulaire d'édition
$tasks = getProjectTasks($pdo, $project_id);
$phases = Phase::cases();
$projectProgress = getProjectProgress($pdo, $project_id);
$projectStatuses = ProjectStatus::cases();
$errors = [];
$success = false;

// Vérifier que le projet existe
if (!$project) {
    header('Location: dashboard.php?tab=projets&error=' . urlencode('Projet introuvable.'));
    exit();
}

// Traitement des actions sur les tâches
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout de tâche
    if (isset($_POST['saveTask'])) {
        $name = trim($_POST['name'] ?? '');
        $phase = $_POST['phase'] ?? '';
        $deadline = $_POST['deadline'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) $errors[] = "Le nom est requis.";
        if (!in_array($phase, array_column(Phase::cases(), 'value'))) $errors[] = "Phase invalide.";
        if (empty($deadline)) $errors[] = "La date limite est requise.";
        
        // Validation de la longueur de la description
        if (mb_strlen($description) > 200) {
            $errors[] = "La description ne peut pas dépasser 200 caractères (" . mb_strlen($description) . " caractères saisis).";
        }

        if (empty($errors)) {
            addTask($pdo, $name, $phase, $deadline, $description, $project_id, 0);
            $success = 'Tâche ajoutée avec succès !';
            $tasks = getProjectTasks($pdo, $project_id);
            $projectProgress = getProjectProgress($pdo, $project_id); // Rafraîchir progression
        }
    }

    // Modification de tâche
    if (isset($_POST['editTask'])) {
        $taskId = (int)$_POST['task_id'];
        $name = trim($_POST['name'] ?? '');
        $phase = $_POST['phase'] ?? '';
        $deadline = $_POST['deadline'] ?? '';
        $description = trim($_POST['description'] ?? '');

        if (empty($name)) $errors[] = "Le nom est requis.";
        if (!in_array($phase, array_column(Phase::cases(), 'value'))) $errors[] = "Phase invalide.";
        if (empty($deadline)) $errors[] = "La date limite est requise.";
        
        // Validation de la longueur de la description
        if (mb_strlen($description) > 200) {
            $errors[] = "La description ne peut pas dépasser 200 caractères (" . mb_strlen($description) . " caractères saisis).";
        }

        if (empty($errors)) {
            editTask($pdo, $taskId, $name, $phase, $deadline, $description);
            $success = 'Tâche modifiée avec succès !';
            $tasks = getProjectTasks($pdo, $project_id);
            $projectProgress = getProjectProgress($pdo, $project_id); // Rafraîchir progression
        }
    }
    
    // Suppression de projet
    if (isset($_POST['deleteProject'])) {
        if (deleteProject($pdo, $project_id)) {
            header('Location: dashboard.php?tab=projets&success=' . urlencode('Projet supprimé avec succès.'));
            exit();
        } else {
            $errors[] = 'Erreur lors de la suppression du projet.';
        }
    }
}

// Actions GET
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'updateTaskStatus':
            if (isset($_GET['task_id']) && isset($_GET['status'])) {
                $taskId = (int)$_GET['task_id'];
                $newStatus = (int)$_GET['status'];
                updateTaskStatus($pdo, $taskId, $newStatus);
                $tasks = getProjectTasks($pdo, $project_id);
                $projectProgress = getProjectProgress($pdo, $project_id); // Rafraîchir progression
            }
            break;
            
        case 'deleteProjectTask':
            if (isset($_GET['task_id'])) {
                $taskId = (int)$_GET['task_id'];
                deleteTask($pdo, $taskId);
                $tasks = getProjectTasks($pdo, $project_id);
                $projectProgress = getProjectProgress($pdo, $project_id); // Rafraîchir progression
                $success = 'Tâche supprimée avec succès !';
            }
            break;
    }
}

include __DIR__ . '/../templates/project/show.php';

require_once __DIR__ . '/../templates/footer.php';
?>