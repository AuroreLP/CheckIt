<!-- Messages d'alerte -->
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?= htmlspecialchars($_GET['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Mes Domaines</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDomainModal">
        <i class="bi bi-plus me-2"></i>Nouveau Domaine
    </button>
</div>

<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Domaine</th>
                <th>Description</th>
                <th>Projets</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($domains) && $domains): ?>
                <?php foreach ($domains as $domain): ?>
                    <?php $projectCount = count(getProjectsByUserAndDomain($pdo, $userId, $domain['id'])); ?>
                    <tr>
                        <td>
                            <strong class="text-primary"><?= htmlspecialchars($domain['name']) ?></strong>
                        </td>
                        <td>
                            <?php if (!empty($domain['description'])): ?>
                                <span class="text-muted"><?= htmlspecialchars($domain['description']) ?></span>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Aucune description</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge <?= $projectCount > 0 ? 'bg-primary' : 'bg-secondary' ?>">
                                <?= $projectCount ?> projet<?= $projectCount > 1 ? 's' : '' ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editDomainModal"
                                    data-domain-id="<?= $domain['id'] ?>"
                                    data-domain-name="<?= htmlspecialchars($domain['name']) ?>"
                                    data-domain-description="<?= htmlspecialchars($domain['description'] ?? '') ?>"
                                    title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </button>
                            
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="confirmDeleteDomain(<?= $domain['id'] ?>, '<?= htmlspecialchars($domain['name']) ?>', <?= $projectCount ?>)"
                                    title="Supprimer<?= $projectCount > 0 ? ' (projets seront déplacés vers Non classé)' : '' ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-folder-x display-6 text-muted"></i>
                        <h5 class="mt-3">Aucun domaine configuré</h5>
                        <p class="small">Créez votre premier domaine pour organiser vos projets par catégories</p>
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addDomainModal">
                            <i class="bi bi-plus me-1"></i>Créer un domaine
                        </button>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Nouveau Domaine -->
<div class="modal fade" id="addDomainModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2"></i>Nouveau Domaine
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="new-domain.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="domainName" class="form-label">Nom du domaine *</label>
                        <input type="text" 
                               class="form-control" 
                               id="domainName" 
                               name="name" 
                               required
                               placeholder="Ex: Développement Web, Design, Marketing..."
                               maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label for="domainDescription" class="form-label">
                            Description 
                            <span class="form-text d-inline ms-2">
                                <span id="charCountAdd">0</span>/100 caractères
                            </span>
                        </label>
                        <textarea class="form-control" 
                                  id="domainDescription" 
                                  name="description" 
                                  rows="3"
                                  maxlength="100"
                                  placeholder="Décrivez brièvement ce domaine d'activité..."
                                  onkeyup="updateCharCount('domainDescription', 'charCountAdd')"></textarea>
                        <div class="form-text">
                            Optionnel : Expliquez en quoi consiste ce domaine d'activité
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus me-1"></i>Créer le domaine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Modifier Domaine -->
<div class="modal fade" id="editDomainModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2"></i>Modifier le domaine
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="edit-domain.php" method="post">
                <input type="hidden" id="editDomainId" name="domain_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editDomainName" class="form-label">Nom du domaine *</label>
                        <input type="text" 
                               class="form-control" 
                               id="editDomainName" 
                               name="name" 
                               required
                               maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label for="editDomainDescription" class="form-label">
                            Description 
                            <span class="form-text d-inline ms-2">
                                <span id="charCountEdit">0</span>/100 caractères
                            </span>
                        </label>
                        <textarea class="form-control" 
                                  id="editDomainDescription" 
                                  name="description" 
                                  rows="3"
                                  maxlength="100"
                                  placeholder="Décrivez brièvement ce domaine d'activité..."
                                  onkeyup="updateCharCount('editDomainDescription', 'charCountEdit')"></textarea>
                        <div class="form-text">
                            Optionnel : Expliquez en quoi consiste ce domaine d'activité
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check me-1"></i>Sauvegarder
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Fonction pour le compteur de caractères
function updateCharCount(textareaId, counterId) {
    const textarea = document.getElementById(textareaId);
    const counter = document.getElementById(counterId);
    const currentLength = textarea.value.length;
    
    counter.textContent = currentLength;
    
    // Changer la couleur selon la limite
    if (currentLength > 90) {
        counter.style.color = 'red';
    } else if (currentLength > 75) {
        counter.style.color = 'orange';
    } else {
        counter.style.color = '';
    }
}

// Fonction pour confirmer la suppression
function confirmDeleteDomain(domainId, domainName, projectCount) {
    let message = `Êtes-vous sûr de vouloir supprimer le domaine "${domainName}" ?`;
    
    if (projectCount > 0) {
        message += `\n\n⚠️ ATTENTION : ${projectCount} projet(s) utilisent ce domaine.\nIls seront automatiquement déplacés vers le domaine "Non classé".`;
    }
    
    message += `\n\nCette action est irréversible.`;
    
    if (confirm(message)) {
        window.location.href = `delete-domain.php?id=${domainId}`;
    }
}

// Remplir le modal de modification
document.getElementById('editDomainModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const domainId = button.getAttribute('data-domain-id');
    const domainName = button.getAttribute('data-domain-name');
    const domainDescription = button.getAttribute('data-domain-description');
    
    document.getElementById('editDomainId').value = domainId;
    document.getElementById('editDomainName').value = domainName;
    document.getElementById('editDomainDescription').value = domainDescription || '';
    
    // Mettre à jour le compteur
    updateCharCount('editDomainDescription', 'charCountEdit');
});

// Réinitialiser le modal d'ajout
document.getElementById('addDomainModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('domainName').value = '';
    document.getElementById('domainDescription').value = '';
    updateCharCount('domainDescription', 'charCountAdd');
});

// Initialiser les compteurs au chargement
document.addEventListener('DOMContentLoaded', function() {
    updateCharCount('domainDescription', 'charCountAdd');
});
</script>