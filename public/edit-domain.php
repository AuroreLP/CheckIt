<?php

require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/domain.php';

// Vérifier si l'utilisateur est connecté
if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domainId = (int) ($_POST['domain_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validation
    $errors = [];
    
    if ($domainId <= 0) {
        $errors[] = "ID de domaine invalide.";
    }
    
    if (empty($name)) {
        $errors[] = "Le nom du domaine est obligatoire.";
    }
    
    // Validation de la longueur de la description
    if (mb_strlen($description) > 100) {
        $errors[] = "La description ne peut pas dépasser 100 caractères (" . mb_strlen($description) . " caractères saisis).";
    }
    
    // Vérifier si le nom existe déjà (en excluant le domaine actuel)
    if (!empty($name) && domainNameExists($pdo, $name, $domainId)) {
        $errors[] = "Un autre domaine avec ce nom existe déjà.";
    }
    
    if (empty($errors)) {
        $success = updateDomain($pdo, $domainId, $name, $description);
        
        if ($success) {
            $successMessage = "Domaine '{$name}' modifié avec succès !";
            header('Location: dashboard.php?tab=domaines&success=' . urlencode($successMessage));
            exit();
        } else {
            $errorMessage = "Erreur lors de la modification du domaine.";
            header('Location: dashboard.php?tab=domaines&error=' . urlencode($errorMessage));
            exit();
        }
    } else {
        $errorMessage = implode(' ', $errors);
        header('Location: dashboard.php?tab=domaines&error=' . urlencode($errorMessage));
        exit();
    }
} else {
    // Redirection si accès direct sans POST
    header('Location: dashboard.php?tab=domaines');
    exit();
}
?>