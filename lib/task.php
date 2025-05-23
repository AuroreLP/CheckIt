<?php
  require_once 'lib/pdo.php';
  require_once 'lib/project.php';

  // Définition de l'énumération pour les phases
enum Phase: string {
  case Analyse = 'analyse';
  case Conception = 'conception';
  case Programmation = 'programmation';
  case Deploiement = 'deploiement';
}

  // Fonction pour récupérer les tâches d'un projet
  function getProjectTasks(PDO $pdo, int $id): array
  {
      $stmt = $pdo->prepare("SELECT * FROM task WHERE project_id = :id ORDER BY deadline ASC");
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
  
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Récupérer une tâche par son ID
  function getTaskById(PDO $pdo, int $taskId): ?array {
    $stmt = $pdo->prepare("SELECT * FROM task WHERE id = :taskId");
    $stmt->execute(['taskId' => $taskId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
  }

  // Fonction pour ajouter une nouvelle tâche
  function addTask($pdo, $name, $phase, $deadline, $description, $project_id, $status) {
    $stmt = $pdo->prepare("INSERT INTO task (name, phase, deadline, description, project_id, status) VALUES (:name, :phase, :deadline, :description, :project_id, :status)");
    $stmt->execute([
        'name' => $name,
        'phase' => $phase,
        'deadline' => $deadline,
        'description' => $description,
        'project_id' => $project_id,
        'status' => $status
    ]);
  }

// Fonction pour modifier une tâche existante
function editTask($pdo, $id, $name, $phase, $deadline, $description) {
  $stmt = $pdo->prepare("UPDATE task SET name = :name, phase = :phase, deadline = :deadline, description = :description WHERE id = :id");
  $stmt->execute([
      'id' => $id,
      'name' => $name,
      'phase' => $phase,
      'deadline' => $deadline,
      'description' => $description,
  ]);
}

// Fonction pour mettre à jour le statut d'une tâche
function updateTaskStatus(PDO $pdo, int $id, bool $status):bool
{
  $query = $pdo->prepare('UPDATE task SET status = :status WHERE id = :id');
  $query->bindValue(':id', $id, PDO::PARAM_INT);
  $query->bindValue(':status', $status, PDO::PARAM_BOOL);

  return $query->execute();
}
  
  // Fonction pour supprimer une tâche
function deleteTask(PDO $pdo, int $taskId) {
  $stmt = $pdo->prepare("DELETE FROM task WHERE id = :taskId");
  $stmt->execute(['taskId' => $taskId]);
}



