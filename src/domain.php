<?php
/**
 * Créer ou récupérer le domaine par défaut pour les projets orphelins
 */
function getOrCreateDefaultDomain(PDO $pdo): int {
    // Chercher s'il existe déjà un domaine "Non classé"
    $query = $pdo->prepare("SELECT id FROM domain WHERE name = 'Non classé' LIMIT 1");
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        return (int) $result['id'];
    }
    
    // Créer le domaine par défaut s'il n'existe pas
    $createQuery = $pdo->prepare("INSERT INTO domain (name, description) VALUES ('Non classé', 'Projets sans domaine spécifique')");
    $createQuery->execute();
    
    return (int) $pdo->lastInsertId();
}

/**
 * Vérifier si un nom de domaine existe déjà
 */
function domainNameExists(PDO $pdo, string $name): bool {
    try {
        $query = $pdo->prepare("SELECT COUNT(*) FROM domain WHERE name = :name");
        $query->bindValue(':name', trim($name), PDO::PARAM_STR);
        $query->execute();
        
        return $query->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification du nom de domaine : " . $e->getMessage());
        return false;
    }
}

/**
 * Ajouter un nouveau domaine
 */
function addDomain(PDO $pdo, string $name, string $description = ''): int|false {
    try {
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

function getAllDomains(PDO $pdo): array {
    try {
        // Vérifier d'abord si la table existe et contient des données
        $query = $pdo->prepare("SELECT id, name, description FROM domain ORDER BY name");
        $query->execute();
        $domains = $query->fetchAll(PDO::FETCH_ASSOC);
        
        return $domains;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des domaines : " . $e->getMessage());
        return [];
    }
}

/**
 * Récupérer un domaine par son ID
 */
function getDomainById(PDO $pdo, int $domainId): array|false {
    try {
        $query = $pdo->prepare("SELECT id, name, description FROM domain WHERE id = :id");
        $query->bindValue(':id', $domainId, PDO::PARAM_INT);
        $query->execute();
        
        return $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération du domaine : " . $e->getMessage());
        return false;
    }
}

/**
 * Mettre à jour un domaine
 */
function updateDomain(PDO $pdo, int $domainId, string $name, string $description = ''): array {
    try {
        // Vérifier que le domaine existe
        $domain = getDomainById($pdo, $domainId);
        if (!$domain) {
            return [
                'success' => false,
                'message' => "Domaine introuvable."
            ];
        }
        
        // Vérifier si le nouveau nom existe déjà SEULEMENT si le nom a changé
        if (trim($name) !== $domain['name']) {
            $checkQuery = $pdo->prepare("SELECT id FROM domain WHERE name = :name");
            $checkQuery->bindValue(':name', trim($name), PDO::PARAM_STR);
            $checkQuery->execute();
            
            if ($checkQuery->fetch()) {
                return [
                    'success' => false,
                    'message' => "Un autre domaine avec ce nom existe déjà."
                ];
            }
        }
        
        // Mettre à jour le domaine
        $updateQuery = $pdo->prepare("UPDATE domain SET name = :name, description = :description WHERE id = :id");
        $updateQuery->bindValue(':name', trim($name), PDO::PARAM_STR);
        $updateQuery->bindValue(':description', trim($description), PDO::PARAM_STR);
        $updateQuery->bindValue(':id', $domainId, PDO::PARAM_INT);
        
        if ($updateQuery->execute()) {
            return [
                'success' => true,
                'message' => "Domaine '{$name}' modifié avec succès !"
            ];
        } else {
            return [
                'success' => false,
                'message' => "Erreur lors de la modification du domaine."
            ];
        }
        
    } catch (PDOException $e) {
        error_log("Erreur lors de la mise à jour du domaine : " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Erreur technique lors de la modification."
        ];
    }
}

/**
 * Supprimer un domaine en réassignant ses projets
 */
function deleteDomainWithReassignment(PDO $pdo, int $domainId): array {
    try {
        $pdo->beginTransaction();
        
        // Récupérer les infos du domaine à supprimer
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
        
        // Vérifier s'il y a des projets liés
        $projectQuery = $pdo->prepare("SELECT COUNT(*) as project_count FROM project WHERE domain_id = :domain_id");
        $projectQuery->bindValue(':domain_id', $domainId, PDO::PARAM_INT);
        $projectQuery->execute();
        $projectResult = $projectQuery->fetch(PDO::FETCH_ASSOC);
        $projectCount = (int) $projectResult['project_count'];
        
        $reassignedMessage = '';
        
        if ($projectCount > 0) {
            // Récupérer ou créer le domaine par défaut
            $defaultDomainId = getOrCreateDefaultDomain($pdo);
            
            // Réassigner tous les projets au domaine par défaut
            $reassignQuery = $pdo->prepare("UPDATE project SET domain_id = :default_domain_id WHERE domain_id = :old_domain_id");
            $reassignQuery->bindValue(':default_domain_id', $defaultDomainId, PDO::PARAM_INT);
            $reassignQuery->bindValue(':old_domain_id', $domainId, PDO::PARAM_INT);
            
            if (!$reassignQuery->execute()) {
                $pdo->rollBack();
                return [
                    'success' => false,
                    'message' => "Erreur lors de la réassignation des projets."
                ];
            }
            
            $reassignedMessage = " {$projectCount} projet(s) ont été déplacés vers 'Non classé'.";
        }
        
        // Supprimer le domaine
        $deleteQuery = $pdo->prepare("DELETE FROM domain WHERE id = :id");
        $deleteQuery->bindValue(':id', $domainId, PDO::PARAM_INT);
        
        if ($deleteQuery->execute() && $deleteQuery->rowCount() > 0) {
            $pdo->commit();
            return [
                'success' => true,
                'message' => "Domaine '{$domain['name']}' supprimé avec succès !{$reassignedMessage}"
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
        error_log("Erreur lors de la suppression du domaine avec réassignation : " . $e->getMessage());
        return [
            'success' => false,
            'message' => "Erreur technique lors de la suppression."
        ];
    }
}

/**
 * Supprimer un domaine (nouvelle version avec choix)
 */
function deleteDomain(PDO $pdo, int $domainId, bool $forceWithReassignment = true): array {
    if ($forceWithReassignment) {
        return deleteDomainWithReassignment($pdo, $domainId);
    }
    
    // Version stricte (ancienne logique)
    try {
        $pdo->beginTransaction();
        
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
?>