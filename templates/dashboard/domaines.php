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
                <th>Icône</th>
                <th>Projets</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($domains) && $domains): ?>
                <?php foreach ($domains as $domain): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($domain['name']) ?></strong>
                        </td>
                        <td>
                            <i class="bi <?= htmlspecialchars($domain['icon'] ?? 'bi-globe') ?>"></i>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                <?= count(getProjectsByUserAndDomain($pdo, $userId, $domain['id'])) ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editDomainModal"
                                    data-domain-id="<?= $domain['id'] ?>"
                                    data-domain-name="<?= htmlspecialchars($domain['name']) ?>">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="confirmDeleteDomain(<?= $domain['id'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        <i class="bi bi-globe display-6"></i>
                        <p class="mt-2">Aucun domaine configuré</p>
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
                <h5 class="modal-title">Nouveau Domaine</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="add-domain.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="domainName" class="form-label">Nom de domaine</label>
                        <input type="text" class="form-control" id="domainName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="domainIcon" class="form-label">Icône (Bootstrap Icons)</label>
                        <input type="text" class="form-control" id="domainIcon" name="icon" placeholder="bi-globe">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDeleteDomain(domainId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce domaine ?')) {
        window.location.href = `delete-domain.php?id=${domainId}`;
    }
}
</script>