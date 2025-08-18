<?php

function verifyUserLoginPassword(PDO $pdo, string $username, string $password):bool|array
{
  $query = $pdo->prepare("SELECT * FROM user WHERE username = :username");
  $query->bindValue(':username', $username, PDO::PARAM_STR);
  $query->execute();
  // fecth nous permet de récupérer une seule ligne
  $user = $query->fetch(PDO::FETCH_ASSOC);

 if ($user && password_verify($password, $user['password'])) {
  // verify ok
  return $user;

 } else {
  // username ou password incorrect
  return false;
 }
}

/**
 * Vérifie si un nom d'utilisateur existe déjà
 */
function isUsernameExists(PDO $pdo, string $username): bool 
{
    $query = $pdo->prepare("SELECT COUNT(*) FROM user WHERE username = :username");
    $query->bindValue(':username', $username, PDO::PARAM_STR);
    $query->execute();
    
    return $query->fetchColumn() > 0;
}

/**
 * Vérifie si un email existe déjà
 */
function isEmailExists(PDO $pdo, string $email): bool 
{
    $query = $pdo->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->execute();
    
    return $query->fetchColumn() > 0;
}

/**
 * Crée un nouvel utilisateur
 */
function createUser(PDO $pdo, string $username, string $email, string $password): int|false 
{
    try {
        // Hash du mot de passe avec PASSWORD_DEFAULT (actuellement Argon2i/Argon2id ou bcrypt)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $query = $pdo->prepare("
            INSERT INTO user (username, email, password, created_at, updated_at) 
            VALUES (:username, :email, :password, NOW(), NOW())
        ");
        
        $query->bindValue(':username', $username, PDO::PARAM_STR);
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        $query->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
        
        if ($query->execute()) {
            return $pdo->lastInsertId();
        }
        
        return false;
        
    } catch (PDOException $e) {
        // Log l'erreur (vous pouvez utiliser error_log)
        error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
        return false;
    }
}

/**
 * Récupère un utilisateur par son ID
 */
function getUserById(PDO $pdo, int $userId): array|false 
{
    $query = $pdo->prepare("SELECT id, username, email, created_at, updated_at FROM user WHERE id = :id");
    $query->bindValue(':id', $userId, PDO::PARAM_INT);
    $query->execute();
    
    return $query->fetch(PDO::FETCH_ASSOC);
}

/**
 * Met à jour les informations d'un utilisateur
 */
function updateUser(PDO $pdo, int $userId, array $data): bool 
{
    $allowedFields = ['username', 'email'];
    $updateFields = [];
    $params = [':id' => $userId];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updateFields[] = "$field = :$field";
            $params[":$field"] = $data[$field];
        }
    }
    
    if (empty($updateFields)) {
        return false;
    }
    
    $sql = "UPDATE user SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = :id";
    
    try {
        $query = $pdo->prepare($sql);
        return $query->execute($params);
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour de l'utilisateur: " . $e->getMessage());
        return false;
    }
}

/**
 * Change le mot de passe d'un utilisateur
 */
function changeUserPassword(PDO $pdo, int $userId, string $oldPassword, string $newPassword): bool 
{
    // Vérifier l'ancien mot de passe
    $query = $pdo->prepare("SELECT password FROM user WHERE id = :id");
    $query->bindValue(':id', $userId, PDO::PARAM_INT);
    $query->execute();
    
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($oldPassword, $user['password'])) {
        return false;
    }
    
    // Mettre à jour avec le nouveau mot de passe
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $updateQuery = $pdo->prepare("UPDATE user SET password = :password, updated_at = NOW() WHERE id = :id");
    $updateQuery->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
    $updateQuery->bindValue(':id', $userId, PDO::PARAM_INT);
    
    return $updateQuery->execute();
}

/**
 * Validation du format email
 */
function validateEmail(string $email): bool 
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && strlen($email) <= 255;
}

/**
 * Validation du nom d'utilisateur
 */
function validateUsername(string $username): bool 
{
    return preg_match('/^[a-zA-Z0-9_-]{3,50}$/', $username);
}

/**
 * Validation du mot de passe
 */
function validatePassword(string $password): bool 
{
    // Au moins 8 caractères, une minuscule, une majuscule, un chiffre et un caractère spécial
    return strlen($password) >= 8 
        && preg_match('/[a-z]/', $password)
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[0-9]/', $password)
        && preg_match('/[@$!%*?&]/', $password);
}

?>