<?php
// Gestion des messages d'erreur et de succès
$errors = [];
$success = '';

if (isset($_GET['errors'])) {
    $errors = explode('|', $_GET['errors']);
}

if (isset($_GET['success'])) {
    $success = $_GET['success'];
}

if (isset($_GET['error'])) {
    $errors[] = $_GET['error'];
}
?>

<h2>Paramètres du Profil</h2>

<!-- Messages d'alerte -->
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Formulaire de modification -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-person-gear me-2"></i>
                    Informations personnelles
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Membre depuis le</small>
                    <strong class="ms-1">
                        <?php if (isset($userProfile['created_at'])): ?>
                            <?= date('d/m/Y', strtotime($userProfile['created_at'])) ?>
                        <?php else: ?>
                            date inconnue
                        <?php endif; ?>
                    </strong>
                </div>
                <form action="edit-settings.php" method="post" id="profileForm">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="username" 
                                   value="<?= htmlspecialchars(getConnectedUserName()); ?>" readonly>
                        </div>
                        <div class="form-text">Le nom d'utilisateur ne peut pas être modifié</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?= htmlspecialchars($userProfile['email'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h6 class="text-muted mb-3">Modification du mot de passe</h6>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   minlength="6" autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text">Laissez vide pour conserver le mot de passe actuel. Minimum 6 caractères.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" id="confirm_password" 
                                   name="confirm_password" autocomplete="new-password">
                            <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" class="form-text"></div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            Sauvegarder les modifications
                        </button>
                        <button type="reset" class="btn btn-outline-primary">
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Informations du compte -->
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-header bg-danger">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Zone de danger
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">
                    Une fois votre compte supprimé, toutes vos données seront définitivement perdues.
                </p>
                <button type="button" class="btn btn-outline-danger btn-sm w-100" 
                        data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="bi bi-trash me-2"></i>Supprimer le compte
                </button>
            </div>
        </div>
    </div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger" id="deleteAccountModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Supprimer le compte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Attention !</strong> Cette action est irréversible.
                </div>
                <p>Êtes-vous sûr de vouloir supprimer votre compte ? Toutes vos données seront définitivement perdues :</p>
                <ul class="text-muted">
                    <li>Tous vos projets (<?= $totalProjects ?? 0 ?>)</li>
                    <li>Toutes vos tâches (<?= $totalChecks ?? 0 ?>)</li>
                    <li>Vos paramètres personnalisés</li>
                    <li>Votre historique de progression</li>
                </ul>
                <div class="mb-3">
                    <label for="confirmUsername" class="form-label">
                        Pour confirmer, tapez votre nom d'utilisateur : 
                        <strong class="text-danger"><?= htmlspecialchars(getConnectedUserName()); ?></strong>
                    </label>
                    <input type="text" class="form-control" id="confirmUsername"
                           data-expected-username="<?= htmlspecialchars(getConnectedUserName()); ?>"
                           placeholder="Nom d'utilisateur" autocomplete="off">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled onclick="deleteAccount()">
                    <i class="bi bi-trash me-2"></i>Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>