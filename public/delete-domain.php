<?php

require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/domain.php';

// Vérifier si l'utilisateur est connecté
if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

// Récupérer l'ID du domaine
$domainId = (int) ($_GET['id'] ?? 0);

if ($domainId <= 0) {
    $errorMessage = "ID de domaine invalide.";
    header('Location: dashboard.php?tab=domaines&error=' . urlencode($errorMessage));
    exit();
}

// Vérifier que le domaine existe
$domain = getDomainById($pdo, $domainId);
if (!$domain) {
    $errorMessage = "Domaine introuvable.";
    header('Location: dashboard.php?tab=domaines&error=' . urlencode($errorMessage));
    exit();
}

// Supprimer le domaine (la fonction gère maintenant la vérification des projets liés)
$result = deleteDomain($pdo, $domainId);

if ($result['success']) {
    header('Location: dashboard.php?tab=domaines&success=' . urlencode($result['message']));
    exit();
} else {
    header('Location: dashboard.php?tab=domaines&error=' . urlencode($result['message']));
    exit();
}
?>