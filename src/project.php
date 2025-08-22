<?php

function getProjectsByUserId(PDO $pdo, int $userId ):array
{
  $query = $pdo->prepare("SELECT project.*, domain.name as domain_name, 
                          domain.icon as domain_icon FROM project
                          JOIN domain ON domain.id = project.domain_id 
                          WHERE user_id = :user_id");
  $query->bindValue(':user_id', $userId, PDO::PARAM_INT);
  $query->execute();
  // fecth nous permet de récupérer une seule ligne
  $projects = $query->fetchAll(PDO::FETCH_ASSOC);

  return $projects;
}

// Fonction pour récupérer les projets en fonction du domaine
function getProjectsByDomain(PDO $pdo, $domain_id = null): array
{
    $sql = 'SELECT * FROM project'; // Requête de base pour récupérer tous les projets
    
    if ($domain_id) {
        $sql .= ' WHERE domain_id = :domain_id'; // Filtrer par domaine si un ID est spécifié
    }

    $stmt = $pdo->prepare($sql);

    if ($domain_id) {
        $stmt->bindValue(':domain_id', $domain_id, PDO::PARAM_INT); // Bind la valeur du domaine si nécessaire
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère tous les projets
}

function getProjectsByUserAndDomain(PDO $pdo, int $userId, $domain_id = null): array
{
    $sql = 'SELECT project.*, domain.name as domain_name, domain.icon as domain_icon 
            FROM project 
            JOIN domain ON project.domain_id = domain.id 
            WHERE project.user_id = :user_id';

    if ($domain_id) {
        $sql .= ' AND domain_id = :domain_id';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);

    if ($domain_id) {
        $stmt->bindValue(':domain_id', $domain_id, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupère un projet par son ID
 */
function getProjectById($pdo, $projectId) {
    try {
        $sql = "SELECT p.*, d.name as domain_name, d.icon as domain_icon 
                FROM project p 
                LEFT JOIN domain d ON p.domain_id = d.id 
                WHERE p.id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getProjectById: " . $e->getMessage());
        return false;
    }
}

/**
 * Version simple pour debug
 */
function getProjectByIdSimple($pdo, $projectId) {
    try {
        $sql = "SELECT * FROM project WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$projectId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur dans getProjectByIdSimple: " . $e->getMessage());
        return false;
    }
}

function saveProject(PDO $pdo, string $title, int $user_id, int $domain_id, ?string $needs=null, ?int $id=null):int|false
{
  if ($needs === null) {
    $needs = '';
  }

  if ($id) {
    // update
    $query = $pdo->prepare("UPDATE project SET title = :title,
                                                domain_id = :domain_id,
                                                needs = :needs,
                                                user_id = :user_id
                            WHERE id = :id");
    $query->bindValue(':id', $id, PDO::PARAM_INT);
  } else {
    // insert
    $query = $pdo->prepare("INSERT INTO project (title, domain_id, needs, user_id)
                            VALUES (:title, :domain_id, :needs, :user_id)");

  }
  $query->bindValue(':title', $title, PDO::PARAM_STR);
  $query->bindValue(':domain_id', $domain_id, PDO::PARAM_INT);
  $query->bindValue(':needs', $needs, PDO::PARAM_STR);
  $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);

  $res = $query->execute();

  if ($res) {
    return $id ?? (int) $pdo->lastInsertId();
  } else {
    return false;
  }
}

function updateProject(PDO $pdo, int $project_id, array $data): bool {
  if (!isset($data['title']) || !isset($data['domain_id'])) {
      return false; // Données incomplètes
  }

  $query = $pdo->prepare("UPDATE project SET title = :title, needs = :needs, domain_id = :domain_id WHERE id = :id");
  $query->bindValue(':title', $data['title'], PDO::PARAM_STR);
  $query->bindValue(':domain_id', $data['domain_id'], PDO::PARAM_INT);
  $query->bindValue(':needs', $data['needs'] ?? '', PDO::PARAM_STR);
  $query->bindValue(':id', $project_id, PDO::PARAM_INT);

  return $query->execute();
}

/**
 * Supprime un projet et toutes ses tâches associées - VERSION CORRIGÉE
 */
function deleteProject($pdo, $projectId) {
    try {
        error_log("DEBUG deleteProject: Début suppression projet ID = $projectId");
        
        // Vérifier d'abord si le projet existe
        $checkStmt = $pdo->prepare("SELECT id, title FROM project WHERE id = ?");
        $checkStmt->execute([$projectId]);
        $project = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$project) {
            error_log("DEBUG deleteProject: Projet $projectId n'existe pas dans la table");
            return false;
        }
        
        error_log("DEBUG deleteProject: Projet trouvé - " . $project['title']);
        
        // Commencer une transaction
        $pdo->beginTransaction();
        error_log("DEBUG deleteProject: Transaction démarrée");
        
        // Vérifier s'il y a des tâches associées (TABLE = task, pas tasks)
        $taskCheckStmt = $pdo->prepare("SELECT COUNT(*) as task_count FROM task WHERE project_id = ?");
        $taskCheckStmt->execute([$projectId]);
        $taskCount = $taskCheckStmt->fetch(PDO::FETCH_ASSOC)['task_count'];
        error_log("DEBUG deleteProject: $taskCount tâches à supprimer");
        
        // Supprimer d'abord toutes les tâches du projet (TABLE = task, pas tasks)
        $deleteTasksStmt = $pdo->prepare("DELETE FROM task WHERE project_id = ?");
        $tasksDeleted = $deleteTasksStmt->execute([$projectId]);
        
        if (!$tasksDeleted) {
            error_log("DEBUG deleteProject: ÉCHEC suppression des tâches");
            $pdo->rollBack();
            return false;
        }
        
        $deletedTasksCount = $deleteTasksStmt->rowCount();
        error_log("DEBUG deleteProject: $deletedTasksCount tâches supprimées avec succès");
        
        // Puis supprimer le projet
        $deleteProjectStmt = $pdo->prepare("DELETE FROM project WHERE id = ?");
        $projectDeleted = $deleteProjectStmt->execute([$projectId]);
        
        if (!$projectDeleted) {
            error_log("DEBUG deleteProject: ÉCHEC suppression du projet");
            $pdo->rollBack();
            return false;
        }
        
        $deletedProjectCount = $deleteProjectStmt->rowCount();
        error_log("DEBUG deleteProject: $deletedProjectCount projet(s) supprimé(s)");
        
        if ($deletedProjectCount === 0) {
            error_log("DEBUG deleteProject: Aucun projet supprimé (peut-être déjà supprimé?)");
            $pdo->rollBack();
            return false;
        }
        
        // Valider la transaction
        $pdo->commit();
        error_log("DEBUG deleteProject: Transaction validée - SUCCÈS");
        
        return true;
        
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("DEBUG deleteProject: EXCEPTION PDO - " . $e->getMessage());
        error_log("DEBUG deleteProject: Code erreur: " . $e->getCode());
        return false;
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur générale
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("DEBUG deleteProject: EXCEPTION générale - " . $e->getMessage());
        return false;
    }
}