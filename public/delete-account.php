<?php
require_once __DIR__ . '/src/session.php';
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/profile.php'; // Nouveau fichier

// Vérifier si l'utilisateur est connecté
if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];

// Supprimer le compte avec la fonction dédiée
if (deleteUserAccount($pdo, $userId)) {
    // Détruire la session
    session_destroy();
    
    // Rediriger vers la page d'accueil avec message
    header('Location: index.php?message=' . urlencode('Votre compte a été supprimé avec succès.'));
    exit();
} else {
    // Rediriger avec erreur
    header('Location: dashboard.php?tab=profil&error=' . urlencode('Erreur lors de la suppression du compte.'));
    exit();
}
?>