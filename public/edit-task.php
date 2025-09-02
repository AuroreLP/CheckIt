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
    $deadline = $_POST['deadline'] ?? '';
    $description = trim($_POST['description'] ?? '');

    if (empty($name)) $errors[] = "Le nom est requis.";
    if (empty($deadline)) $errors[] = "La date limite est requise.";

    if (empty($errors)) { 
        editTask($pdo, $taskId, $name, $deadline, $description);
        $success = true;
        // recharger la tâche mise à jour
        $task = getTaskById($pdo, $taskId);
    }
}

