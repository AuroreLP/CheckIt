<?php
// public/new-project.php - Contrôleur refactorisé
ob_start();
require_once __DIR__ . "/../templates/header.php";
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/project.php';
require_once __DIR__ . '/../src/domain.php';

if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

// Préparer les données pour le template
$domains = getAllDomains($pdo);
$projectStatuses = ProjectStatus::cases();
$errorsProject = [];
$messagesProject = [];

// Initialisation des valeurs par défaut
$formData = [
    'title' => '',
    'needs' => '',
    'domain_id' => '',
    'start_date' => '',
    'end_date' => '',
    'status' => 'planification'
];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['saveProject'])) {
    // Récupérer et nettoyer les valeurs soumises
    $formData = [
        'title' => trim($_POST['title'] ?? ''),
        'needs' => trim($_POST['needs'] ?? ''),
        'domain_id' => (int)($_POST['domain_id'] ?? 0),
        'start_date' => trim($_POST['start_date'] ?? ''),
        'end_date' => trim($_POST['end_date'] ?? ''),
        'status' => trim($_POST['status'] ?? 'planification')
    ];

    // Validation
    if (empty($formData['title'])) {
        $errorsProject[] = "Le titre est obligatoire";
    }
    
    if ($formData['domain_id'] <= 0) {
        $errorsProject[] = "Veuillez sélectionner un domaine";
    }
    
    // Validation des dates
    if (!empty($formData['start_date']) && !empty($formData['end_date'])) {
        try {
            $startDateTime = new DateTime($formData['start_date']);
            $endDateTime = new DateTime($formData['end_date']);
            if ($startDateTime >= $endDateTime) {
                $errorsProject[] = 'La date de fin doit être postérieure à la date de début.';
            }
        } catch (Exception $e) {
            $errorsProject[] = 'Format de date invalide.';
        }
    }
    
    // Validation du statut
    if (!empty($formData['status']) && !in_array($formData['status'], array_column(ProjectStatus::cases(), 'value'))) {
        $errorsProject[] = 'Statut invalide.';
    }

    // Si pas d'erreurs, créer le projet
    if (empty($errorsProject)) {
        $user_id = (int)$_SESSION['user']['id'];
        
        // Créer d'abord le projet avec saveProject (fonction existante)
        $projectId = saveProject($pdo, $formData['title'], $user_id, $formData['domain_id'], $formData['needs']);
        
        if ($projectId) {
            // Puis mettre à jour avec les nouvelles propriétés si elles sont renseignées
            if (!empty($formData['start_date']) || !empty($formData['end_date']) || $formData['status'] !== 'planification') {
                $updateData = [
                    'title' => $formData['title'],
                    'domain_id' => $formData['domain_id'],
                    'needs' => $formData['needs'],
                    'start_date' => $formData['start_date'] ?: null,
                    'end_date' => $formData['end_date'] ?: null,
                    'status' => $formData['status']
                ];
                updateProject($pdo, $projectId, $updateData);
            }
            
            header('Location: show-project.php?id=' . $projectId . '&success=' . urlencode('Projet créé avec succès !'));
            exit();
        } else {
            $errorsProject[] = "Le projet n'a pas été enregistré";
        }
    }
}

// Inclure le template
include __DIR__ . '/../templates/project/new.php';

require_once __DIR__ . "/../templates/footer.php";
ob_end_flush();
?>