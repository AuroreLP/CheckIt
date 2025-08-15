<?php
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/task.php';

$errors = [];
$success = false;

// Vérifie que l'ID de la tâche est fourni
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID de tâche manquant ou invalide.');
}

$taskId = (int)$_GET['id'];
$task = getTaskById($pdo, $taskId);

if (!$task) {
    die('Tâche introuvable.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phase = $_POST['phase'] ?? '';
    $deadline = $_POST['deadline'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) $errors[] = "Le nom est requis.";
    if (!in_array($phase, array_column(Phase::cases(), 'value'))) $errors[] = "Phase invalide.";
    if (empty($deadline)) $errors[] = "La date limite est requise.";

    if (empty($errors)) {
        // Vérification si la phase a bien changé
        echo "Phase avant modification: " . htmlspecialchars($task['phase']); // Debug
        echo "Phase après modification: " . htmlspecialchars($phase); // Debug
        
        editTask($pdo, $taskId, $name, $phase, $deadline, $description);
        $success = true;
        // recharger la tâche mise à jour
        $task = getTaskById($pdo, $taskId);
    }
}

