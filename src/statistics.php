<?php

/**
 * Récupère le nombre de projets actifs pour un utilisateur
 * Un projet est considéré comme actif s'il a des tâches non terminées
 */
function getActiveProjectsByUser($pdo, $userId) {
    try {
        $sql = "SELECT DISTINCT p.id, p.title 
                FROM project p 
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
function getTotalTasks($pdo, $userId) {
    try {
        $sql = "SELECT COUNT(t.id) as total_checks 
                FROM task t 
                INNER JOIN project p ON t.project_id = p.id 
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
                INNER JOIN project p ON t.project_id = p.id 
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
                INNER JOIN project p ON t.project_id = p.id 
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
                    COUNT(DISTINCT p.id) as project_count,
                    COUNT(t.id) as total_tasks,
                    SUM(CASE WHEN t.status = 1 THEN 1 ELSE 0 END) as completed_tasks
                FROM domain d
                LEFT JOIN project p ON d.id = p.domain_id AND p.user_id = ?
                LEFT JOIN task t ON p.id = t.project_id
                GROUP BY d.id, d.name
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
 * Récupère les tâches dues aujourd'hui
 */
function getTasksDueToday($pdo, $userId) {
    try {
        $sql = "SELECT t.*, p.title as project_title, p.id as project_id, d.name as domain_name
                FROM task t 
                INNER JOIN project p ON t.project_id = p.id 
                LEFT JOIN domain d ON p.domain_id = d.id
                WHERE p.user_id = ? 
                AND t.status = 0 
                AND DATE(t.deadline) = CURDATE()
                ORDER BY t.deadline ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getTasksDueToday: " . $e->getMessage());
        return [];
    }
}
/**
 * Récupère les tâches en retard
 */
function getOverdueTasks($pdo, $userId) {
    try {
        $sql = "SELECT t.*, p.title as project_title, p.id as project_id, d.name as domain_name,
                DATEDIFF(CURDATE(), DATE(t.deadline)) as days_overdue
                FROM task t 
                INNER JOIN project p ON t.project_id = p.id 
                LEFT JOIN domain d ON p.domain_id = d.id
                WHERE p.user_id = ? 
                AND t.status = 0 
                AND DATE(t.deadline) < CURDATE()
                ORDER BY t.deadline ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getOverdueTasks: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les tâches dues demain
 */
function getTasksDueTomorrow($pdo, $userId) {
    try {
        $sql = "SELECT t.*, p.title as project_title, p.id as project_id, d.name as domain_name
                FROM task t 
                INNER JOIN project p ON t.project_id = p.id 
                LEFT JOIN domain d ON p.domain_id = d.id
                WHERE p.user_id = ? 
                AND t.status = 0 
                AND DATE(t.deadline) = CURDATE() + INTERVAL 1 DAY
                ORDER BY t.deadline ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getTasksDueTomorrow: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les tâches des 3 jours suivants (après demain)
 */
function getTasksNext3Days($pdo, $userId) {
    try {
        $sql = "SELECT t.*, p.title as project_title, p.id as project_id, d.name as domain_name
                FROM task t 
                INNER JOIN project p ON t.project_id = p.id 
                LEFT JOIN domain d ON p.domain_id = d.id
                WHERE p.user_id = ? 
                AND t.status = 0 
                AND DATE(t.deadline) BETWEEN CURDATE() + INTERVAL 2 DAY AND CURDATE() + INTERVAL 4 DAY
                ORDER BY t.deadline ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getTasksNext3Days: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les projets récemment créés (derniers 7 jours)
 */
function getRecentProjects($pdo, $userId, $days = 7) {
    try {
        $sql = "SELECT p.*, d.name as domain_name
                FROM project p
                LEFT JOIN domain d ON p.domain_id = d.id
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
 * Récupère les projets avec échéances proches
 */
function getProjectsWithUpcomingDeadlines($pdo, $userId, $days = 7) {
    try {
        $sql = "SELECT p.*, d.name as domain_name,
                DATEDIFF(DATE(p.end_date), CURDATE()) as days_until_deadline
                FROM project p 
                LEFT JOIN domain d ON p.domain_id = d.id
                WHERE p.user_id = ? 
                AND p.end_date IS NOT NULL
                AND DATE(p.end_date) BETWEEN CURDATE() AND CURDATE() + INTERVAL ? DAY
                AND p.status != 'termine'
                ORDER BY p.end_date ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getProjectsWithUpcomingDeadlines: " . $e->getMessage());
        return [];
    }
}

/**
 * Récupère les tâches récemment terminées
 */
function getRecentCompletedTasks($pdo, $userId, $limit = 5) {
    try {
        $sql = "SELECT t.*, p.title as project_title, p.id as project_id, d.name as domain_name,
                t.updated_at as completed_at
                FROM task t 
                INNER JOIN project p ON t.project_id = p.id 
                LEFT JOIN domain d ON p.domain_id = d.id
                WHERE p.user_id = ? 
                AND t.status = 1
                ORDER BY t.updated_at DESC
                LIMIT ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getRecentCompletedTasks: " . $e->getMessage());
        return [];
    }
}

/**
 * Calcule le pourcentage de progression global de l'utilisateur
 */
function getOverallProgress($pdo, $userId) {
    try {
        $totalTasks = getTotalTasks($pdo, $userId);
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
?>