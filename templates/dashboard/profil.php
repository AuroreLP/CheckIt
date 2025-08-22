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
                <form action="update-profile.php" method="post" id="profileForm">
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
                        <button type="reset" class="btn btn-outline-secondary me-md-2">
                            <i class="bi bi-arrow-clockwise me-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-2"></i>Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Informations du compte -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Informations du compte
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Membre depuis</small>
                    <p class="mb-1 fw-bold">
                        <?php if (isset($userProfile['created_at'])): ?>
                            <?= date('d/m/Y', strtotime($userProfile['created_at'])) ?>
                        <?php else: ?>
                            Date inconnue
                        <?php endif; ?>
                    </p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Projets créés</small>
                    <p class="mb-1 fw-bold text-primary"><?= $totalProjects ?? 0 ?></p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Tâches terminées</small>
                    <p class="mb-1 fw-bold text-success"><?= $completedTasksCount ?? 0 ?></p>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Progression générale</small>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: <?= $overallProgress ?? 0 ?>%" 
                             aria-valuenow="<?= $overallProgress ?? 0 ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted"><?= $overallProgress ?? 0 ?>% complété</small>
                </div>
            </div>
        </div>
        
        <!-- Section Danger Zone -->
        <div class="card border-danger mt-3">
            <div class="card-header bg-danger text-white">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            password.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
    
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmPassword = document.getElementById('confirm_password');
        const icon = this.querySelector('i');
        
        if (confirmPassword.type === 'password') {
            confirmPassword.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            confirmPassword.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    });
    
    // Validation des mots de passe en temps réel
    function validatePasswords() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const matchDiv = document.getElementById('passwordMatch');
        
        if (password && confirmPassword) {
            if (password === confirmPassword) {
                matchDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Les mots de passe correspondent</span>';
                matchDiv.className = 'form-text text-success';
            } else {
                matchDiv.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Les mots de passe ne correspondent pas</span>';
                matchDiv.className = 'form-text text-danger';
            }
        } else {
            matchDiv.innerHTML = '';
        }
    }
    
    document.getElementById('password').addEventListener('input', validatePasswords);
    document.getElementById('confirm_password').addEventListener('input', validatePasswords);
    
    // Validation du formulaire
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password && password !== confirmPassword) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
            return false;
        }
        
        if (password && password.length < 6) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 6 caractères.');
            return false;
        }
    });
    
    // Validation pour la suppression du compte
    document.getElementById('confirmUsername').addEventListener('input', function() {
        const expectedUsername = '<?= htmlspecialchars(getConnectedUserName()); ?>';
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        
        if (this.value === expectedUsername) {
            confirmBtn.disabled = false;
            confirmBtn.classList.remove('disabled');
        } else {
            confirmBtn.disabled = true;
            confirmBtn.classList.add('disabled');
        }
    });
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const alertInstance = new bootstrap.Alert(alert);
            alertInstance.close();
        });
    }, 5000);
});

function deleteAccount() {
    if (confirm('Êtes-vous absolument sûr ? Cette action ne peut pas être annulée.')) {
        window.location.href = 'delete-account.php';
    }
}
</script>