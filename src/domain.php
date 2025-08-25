<?php
/**
 * Récupérer tous les domaines
 */
function getAllDomains(PDO $pdo): array {
    $query = $pdo->prepare("SELECT * FROM domain ORDER BY name ASC");
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Récupérer un domaine par son ID
 */
function getDomainById(PDO $pdo, int $domainId): ?array {
    $query = $pdo->prepare("SELECT * FROM domain WHERE id = :id");
    $query->bindValue(':id', $domainId, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

/**
 * Ajouter un nouveau domaine
 */
function addDomain(PDO $pdo, string $name, string $description = ''): int|false {
    try {
        // Validation de la longueur de la description
        if (mb_strlen($description) > 100) {
            return false;
        }
        
        $query = $pdo->prepare("INSERT INTO domain (name, description) VALUES (:name, :description)");
        $query->bindValue(':name', trim($name), PDO::PARAM_STR);
        $query->bindValue(':description', trim($description), PDO::PARAM_STR);
        
        if ($query->execute()) {
            return (int) $pdo->lastInsertId();
        }
        return false;
    } catch (PDOException $e) {
        error_log("Erreur lors de l'ajout du domaine : " . $e->getMessage());
        return false;
    }
}

/**
 * Mettre à jour un domaine
 */
function updateDomain(PDO $pdo, int $domainId, string $name, string $description = ''): bool {
    try {
        // Validation de la longueur de la description
        if (mb_strlen($description) > 100) {
            return false;
        }
        
        $query = $pdo->prepare("UPDATE domain SET name = :name, description = :description WHERE id = :id");
        $query->bindValue(':id', $domainId, PDO::PARAM_INT);
        $query->bindValue(':name', trim($name), PDO::PARAM_STR);
        $query->bindValue(':description', trim($description), PDO::PARAM_STR);
        
        return $query->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour du domaine : " . $e->getMessage());
        return false;
    }
}

/**
 * Supprimer un domaine (avec vérification des projets liés)
 */
function deleteDomain(PDO $pdo, int $domainId): array {
    try {
        // Commencer une transaction
        $pdo->beginTransaction();
        
        // Vérifier s'il y a des projets liés à ce domaine
        $checkQuery = $pdo->prepare("SELECT COUNT(*) as project_count FROM project WHERE domain_id = :domain_id");
        $checkQuery->bindValue(':domain_id', $domainId, PDO::PARAM_INT);
        $checkQuery->execute();
        $result = $checkQuery->fetch(PDO::FETCH_ASSOC);
        
        if ($result['project_count'] > 0) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => "Impossible de supprimer : {$result['project_count']} projet(s) sont liés à ce domaine."
            ];
        }
        
        // Récupérer le nom du domaine avant suppression
        $domainQuery = $pdo->prepare("SELECT name FROM domain WHERE id = :id");
        $domainQuery->bindValue(':id', $domainId, PDO::PARAM_INT);
        $domainQuery->execute();
        $domain = $domainQuery->fetch(PDO::FETCH_ASSOC);
        
        if (!$domain) {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => "Domaine introuvable."
            ];
        }
        
        // Supprimer le domaine
        $deleteQuery = $pdo->prepare("DELETE FROM domain WHERE id = :id");
        $deleteQuery->bindValue(':id', $domainId, PDO::PARAM_INT);
        
        if ($deleteQuery->execute() && $deleteQuery->rowCount() > 0) {
            $pdo->commit();
            return [
                'success' => true,
                'message' => "Domaine '{$domain['name']}' supprimé avec succès !"
            ];
        } else {
            $pdo->rollBack();
            return [
                'success' => false,
                'message' => "Erreur lors de la suppression du domaine."
            ];
        }
        
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Erreur lors de la suppression du domaine : " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Erreur technique lors de la suppression."
        ];
    }
}

/**
 * Vérifier si un domaine est utilisé par des projets
 */
function isDomainUsed(PDO $pdo, int $domainId): int {
    $query = $pdo->prepare("SELECT COUNT(*) as project_count FROM project WHERE domain_id = :domain_id");
    $query->bindValue(':domain_id', $domainId, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    return (int) $result['project_count'];
}

/**
 * Vérifier si un nom de domaine existe déjà
 */
function domainNameExists(PDO $pdo, string $name, int $excludeId = null): bool {
    $sql = "SELECT COUNT(*) as count FROM domain WHERE LOWER(name) = LOWER(:name)";
    $params = [':name' => trim($name)];
    
    if ($excludeId !== null) {
        $sql .= " AND id != :exclude_id";
        $params[':exclude_id'] = $excludeId;
    }
    
    $query = $pdo->prepare($sql);
    foreach ($params as $param => $value) {
        $query->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $query->execute();
    
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return $result['count'] > 0;
}
?>