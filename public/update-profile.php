<?php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/profile.php';

// Vérifier si l'utilisateur est connecté
if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation avec la fonction dédiée
    $errors = validateProfileUpdateData($pdo, $userId, $email, $password, $confirmPassword);
    
    // Mise à jour si pas d'erreurs
    if (empty($errors)) {
        $success = updateUserProfile($pdo, $userId, $email, $password ?: null);
        
        if ($success) {
            // Mettre à jour la session si nécessaire
            $_SESSION['user']['email'] = $email;
            
            $successMessage = 'Profil mis à jour avec succès !';
            header('Location: dashboard.php?tab=profil&success=' . urlencode($successMessage));
            exit();
        } else {
            $errors[] = 'Erreur lors de la mise à jour du profil.';
        }
    }
    
    // En cas d'erreurs, rediriger avec les erreurs
    if (!empty($errors)) {
        $errorString = implode('|', $errors);
        header('Location: dashboard.php?tab=profil&errors=' . urlencode($errorString));
        exit();
    }
}

// Si accès direct au fichier, rediriger vers le dashboard
header('Location: dashboard.php?tab=profil');
exit();
?>&errors=' . urlencode($errorString));
        exit();
    }
}

// Si accès direct au fichier, rediriger vers le dashboard
header('Location: dashboard.php?tab=profil');
exit();
?>&errors=' . urlencode($errorString));
        exit();
    }
}

// Si accès direct au fichier, rediriger vers le dashboard
header('Location: dashboard.php?tab=profil');
exit();
?>