<?php

/**
 * Récupère le nombre de projets actifs pour un utilisateur
 * Un projet est considéré comme actif s'il a des tâches non terminées
 */
function getActiveProjectsByUser($pdo, $userId) {
    try {
        $sql = "SELECT DISTINCT p.id, p.title 
                FROM projects p 
                LEFT JOIN task t ON p.id = t.project_id 
                WHERE p.user_id = ? 
                AND (t.status = 0 OR t.status IS NULL)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getActiveProjectsByUser: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère le nombre total de vérifications/tâches pour un utilisateur
 */
function getTotalChecks($pdo, $userId) {
    try {
        $sql = "SELECT COUNT(t.id) as total_checks 
                FROM task t 
                INNER JOIN projects p ON t.project_id = p.id 
                WHERE p.user_id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_checks'] ?? 0;
    } catch (PDOException $e) {
        error_log("Erreur dans getTotalChecks: " . $e->getMessage());
        return 0;
    }
}

/**
 * Récupère le nombre de tâches terminées pour un utilisateur
 */
function getCompletedTasks($pdo, $userId) {
    try {
        $sql = "SELECT COUNT(t.id) as completed_tasks 
                FROM task t 
                INNER JOIN projects p ON t.project_id = p.id 
                WHERE p.user_id = ? AND t.status = 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['completed_tasks'] ?? 0;
    } catch (PDOException $e) {
        error_log("Erreur dans getCompletedTasks: " . $e->getMessage());
        return 0;
    }
}

/**
 * Récupère le nombre de tâches en cours pour un utilisateur
 */
function getPendingTasks($pdo, $userId) {
    try {
        $sql = "SELECT COUNT(t.id) as pending_tasks 
                FROM task t 
                INNER JOIN projects p ON t.project_id = p.id 
                WHERE p.user_id = ? AND t.status = 0";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['pending_tasks'] ?? 0;
    } catch (PDOException $e) {
        error_log("Erreur dans getPendingTasks: " . $e->getMessage());
        return 0;
    }
}

/**
 * Récupère les statistiques par domaine pour un utilisateur
 */
function getStatisticsByDomain($pdo, $userId) {
    try {
        $sql = "SELECT 
                    d.name as domain_name,
                    d.icon as domain_icon,
                    COUNT(DISTINCT p.id) as project_count,
                    COUNT(t.id) as total_tasks,
                    SUM(CASE WHEN t.status = 1 THEN 1 ELSE 0 END) as completed_tasks
                FROM domains d
                LEFT JOIN projects p ON d.id = p.domain_id AND p.user_id = ?
                LEFT JOIN task t ON p.id = t.project_id
                GROUP BY d.id, d.name, d.icon
                ORDER BY project_count DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getStatisticsByDomain: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les projets récemment créés (derniers 7 jours)
 */
function getRecentProjects($pdo, $userId, $days = 7) {
    try {
        $sql = "SELECT p.*, d.name as domain_name, d.icon as domain_icon
                FROM projects p
                LEFT JOIN domains d ON p.domain_id = d.id
                WHERE p.user_id = ? 
                AND p.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY p.created_at DESC
                LIMIT 5";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getRecentProjects: " . $e->getMessage());
        return [];
    }
}

/**
 * Calcule le pourcentage de progression global de l'utilisateur
 */
function getOverallProgress($pdo, $userId) {
    try {
        $totalTasks = getTotalChecks($pdo, $userId);
        $completedTasks = getCompletedTasks($pdo, $userId);
        
        if ($totalTasks == 0) {
            return 0;
        }
        
        return round(($completedTasks / $totalTasks) * 100, 1);
    } catch (Exception $e) {
        error_log("Erreur dans getOverallProgress: " . $e->getMessage());
        return 0;
    }
}
