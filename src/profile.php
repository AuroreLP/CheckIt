<?php

// Fonctions liées à la gestion du profil utilisateur personnel

/**
 * Récupère les informations complètes du profil utilisateur
 */
function getUserProfile($pdo, $userId) {
    try {
        $sql = "SELECT id, username, email, created_at, updated_at, last_login 
                FROM user 
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getUserProfile: " . $e->getMessage());
        return null;
    }
}

/**
 * Met à jour le profil utilisateur (email et/ou mot de passe)
 */
function updateUserProfile($pdo, $userId, $email, $password = null) {
    try {
        if ($password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE user SET email = ?, password = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$email, $hashedPassword, $userId]);
        } else {
            $sql = "UPDATE user SET email = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$email, $userId]);
        }
    } catch (PDOException $e) {
        error_log("Erreur dans updateUserProfile: " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifie si un email existe déjà pour un autre utilisateur (pour les mises à jour)
 */
function emailExistsForOtherUser($pdo, $email, $excludeUserId) {
    try {
        $sql = "SELECT id FROM user WHERE email = ? AND id != ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $excludeUserId]);
        
        return $stmt->fetch() !== false;
    } catch (PDOException $e) {
        error_log("Erreur dans emailExistsForOtherUser: " . $e->getMessage());
        return true; // En cas d'erreur, on considère que l'email existe pour éviter les doublons
    }
}

/**
 * Vérifie le mot de passe actuel de l'utilisateur
 */
function verifyCurrentPassword($pdo, $userId, $password) {
    try {
        $sql = "SELECT password FROM user WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            return password_verify($password, $user['password']);
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Erreur dans verifyCurrentPassword: " . $e->getMessage());
        return false;
    }
}

/**
 * Supprime complètement un utilisateur et toutes ses données
 */
function deleteUserAccount($pdo, $userId) {
    try {
        // Commencer une transaction
        $pdo->beginTransaction();
        
        // Supprimer les tâches de l'utilisateur
        $stmt = $pdo->prepare("
            DELETE t FROM tasks t 
            INNER JOIN projects p ON t.project_id = p.id 
            WHERE p.user_id = ?
        ");
        $stmt->execute([$userId]);
        
        // Supprimer les projets de l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM projects WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        // Supprimer l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM user WHERE id = ?");
        $stmt->execute([$userId]);
        
        // Valider la transaction
        $pdo->commit();
        
        return true;
        
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        error_log("Erreur dans deleteUserAccount: " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère les statistiques du profil utilisateur
 */
function getUserProfileStats($pdo, $userId) {
    try {
        $stats = [];
        
        // Nombre total de projets
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM projects WHERE user_id = ?");
        $stmt->execute([$userId]);
        $stats['total_projects'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Nombre de tâches terminées
        $stmt = $pdo->prepare("
            SELECT COUNT(t.id) as completed 
            FROM tasks t 
            INNER JOIN projects p ON t.project_id = p.id 
            WHERE p.user_id = ? AND t.status = 1
        ");
        $stmt->execute([$userId]);
        $stats['completed_tasks'] = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];
        
        // Nombre total de tâches
        $stmt = $pdo->prepare("
            SELECT COUNT(t.id) as total 
            FROM tasks t 
            INNER JOIN projects p ON t.project_id = p.id 
            WHERE p.user_id = ?
        ");
        $stmt->execute([$userId]);
        $stats['total_tasks'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Calcul du pourcentage
        $stats['completion_percentage'] = $stats['total_tasks'] > 0 
            ? round(($stats['completed_tasks'] / $stats['total_tasks']) * 100, 1) 
            : 0;
        
        return $stats;
        
    } catch (PDOException $e) {
        error_log("Erreur dans getUserProfileStats: " . $e->getMessage());
        return [
            'total_projects' => 0,
            'completed_tasks' => 0,
            'total_tasks' => 0,
            'completion_percentage' => 0
        ];
    }
}

/**
 * Valide les données du profil pour la mise à jour
 */
function validateProfileUpdateData($pdo, $userId, $email, $password = null, $confirmPassword = null) {
    $errors = [];
    
    // Validation de l'email
    if (empty($email)) {
        $errors[] = 'L\'email est requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'email n\'est pas valide.';
    } elseif (emailExistsForOtherUser($pdo, $email, $userId)) {
        $errors[] = 'Cet email est déjà utilisé par un autre compte.';
    }
    
    // Validation du mot de passe (si fourni)
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        } elseif ($password !== $confirmPassword) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }
    }
    
    return $errors;
}
?>