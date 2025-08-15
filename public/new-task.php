<?php
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/task.php';

$errors = [];
$success = false;

// Vérifier que l'ID du projet est fourni dans l'URL
if (!isset($_GET['project_id']) || !is_numeric($_GET['project_id'])) {
    die('ID de projet manquant ou invalide.');
}

$projectId = (int)$_GET['project_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phase = $_POST['phase'] ?? null;
    $deadline = $_POST['deadline'] ?? '';
    $description = trim($_POST['description'] ?? '');

    // Validation simple
    if (empty($name)) $errors[] = "Le nom est requis.";
    if (!in_array($phase, array_column(Phase::cases(), 'value'))) $errors[] = "Phase invalide.";
    if (empty($deadline)) $errors[] = "La date limite est requise.";

    if (empty($errors)) {
        addTask($pdo, $name, $phase, $deadline, $description, $projectId);
        $success = true;
    }
}