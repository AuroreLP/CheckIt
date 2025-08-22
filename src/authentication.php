<?php

// Fonctions d'authentification et de gestion des utilisateurs

/**
 * Vérifie les identifiants de connexion d'un utilisateur
 */
function verifyUserLoginPassword(PDO $pdo, string $username, string $password): bool|array
{
    $query = $pdo->prepare("SELECT * FROM user WHERE username = :username");
    $query->bindValue(':username', $username, PDO::PARAM_STR);
    $query->execute();
    
    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie
        return $user;
    } else {
        // Username ou password incorrect
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
        // Hash du mot de passe avec PASSWORD_DEFAULT
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
 * Récupère un utilisateur par son nom d'utilisateur
 */
function getUserByUsername(PDO $pdo, string $username): array|false 
{
    $query = $pdo->prepare("SELECT id, username, email, created_at, updated_at FROM user WHERE username = :username");
    $query->bindValue(':username', $username, PDO::PARAM_STR);
    $query->execute();
    
    return $query->fetch(PDO::FETCH_ASSOC);
}

/**
 * Récupère un utilisateur par son email
 */
function getUserByEmail(PDO $pdo, string $email): array|false 
{
    $query = $pdo->prepare("SELECT id, username, email, created_at, updated_at FROM user WHERE email = :email");
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->execute();
    
    return $query->fetch(PDO::FETCH_ASSOC);
}

/**
 * Met à jour la dernière connexion d'un utilisateur
 */
function updateLastLogin(PDO $pdo, int $userId): bool 
{
    try {
        $query = $pdo->prepare("UPDATE user SET last_login = NOW() WHERE id = :id");
        $query->bindValue(':id', $userId, PDO::PARAM_INT);
        return $query->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour de la dernière connexion: " . $e->getMessage());
        return false;
    }
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
    // Au moins 6 caractères (plus flexible que l'original)
    return strlen($password) >= 6;
}

/**
 * Validation stricte du mot de passe (pour les nouvelles inscriptions)
 */
function validateStrongPassword(string $password): bool 
{
    // Au moins 8 caractères, une minuscule, une majuscule, un chiffre
    return strlen($password) >= 8 
        && preg_match('/[a-z]/', $password)
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[0-9]/', $password);
}

/**
 * Valide toutes les données d'inscription
 */
function validateRegistrationData(PDO $pdo, string $username, string $email, string $password): array 
{
    $errors = [];
    
    // Validation username
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    } elseif (!validateUsername($username)) {
        $errors[] = "Le nom d'utilisateur doit contenir 3-50 caractères (lettres, chiffres, _ et - uniquement).";
    } elseif (isUsernameExists($pdo, $username)) {
        $errors[] = "Ce nom d'utilisateur est déjà pris.";
    }
    
    // Validation email
    if (empty($email)) {
        $errors[] = "L'email est requis.";
    } elseif (!validateEmail($email)) {
        $errors[] = "L'email n'est pas valide.";
    } elseif (isEmailExists($pdo, $email)) {
        $errors[] = "Cet email est déjà utilisé.";
    }
    
    // Validation mot de passe
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    } elseif (!validatePassword($password)) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
    }
    
    return $errors;
}

/**
 * Valide les données de connexion
 */
function validateLoginData(string $username, string $password): array 
{
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Le nom d'utilisateur est requis.";
    }
    
    if (empty($password)) {
        $errors[] = "Le mot de passe est requis.";
    }
    
    return $errors;
}
?>