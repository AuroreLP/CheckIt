<?php
require_once __DIR__ . '/../src/session.php';
require_once __DIR__ . '/../src/pdo.php';

// Ajouter les headers pour JSON
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isUserConnected()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $taskId = (int) $_POST['task_id'];
    $userId = $_SESSION['user']['id'];
    
    // DEBUG: Log des données reçues
    error_log("DEBUG complete-task: taskId=$taskId, userId=$userId");
    
    try {
        // Vérifier que la tâche appartient à l'utilisateur
        $checkQuery = $pdo->prepare("
            SELECT t.id FROM task t 
            INNER JOIN project p ON t.project_id = p.id 
            WHERE t.id = ? AND p.user_id = ?
        ");
        $checkQuery->execute([$taskId, $userId]);
        
        if (!$checkQuery->fetch()) {
            error_log("DEBUG: Tâche introuvable - taskId=$taskId, userId=$userId");
            echo json_encode(['success' => false, 'message' => 'Tâche introuvable']);
            exit();
        }
        
        // Marquer la tâche comme terminée
        $updateQuery = $pdo->prepare("UPDATE task SET status = 1 WHERE id = ?");
        $success = $updateQuery->execute([$taskId]);
        
        error_log("DEBUG: Update result = " . ($success ? 'true' : 'false'));
        
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Tâche terminée']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
        
    } catch (PDOException $e) {
        error_log("Erreur PDO complete-task: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Erreur technique: ' . $e->getMessage()]);
    }
} else {
    error_log("DEBUG: Données manquantes - METHOD=" . $_SERVER['REQUEST_METHOD'] . ", POST=" . print_r($_POST, true));
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
}
?>