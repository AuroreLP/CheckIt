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


function getProjectById(PDO $pdo, int $id): array|bool
{
    $query = $pdo->prepare('
        SELECT project.*, domain.name AS domain_name
        FROM project
        JOIN domain ON project.domain_id = domain.id
        WHERE project.id = :id
    ');
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    return $query->fetch(PDO::FETCH_ASSOC);
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









