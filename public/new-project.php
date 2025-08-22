<?php
ob_start();
require_once __DIR__ . "/../templates/header.php";
require_once __DIR__ . '/../src/pdo.php';
require_once __DIR__ . '/../src/project.php';
require_once __DIR__ . '/../src/domain.php';

if (!isUserConnected()) {
    header('Location: login.php');
    exit();
}

$domains = getAllDomains($pdo);
$errorsProject = [];

// Initialisation des valeurs par défaut
$title = '';
$needs = '';
$selected_domain_id = '';

// Soumission du formulaire
if (isset($_POST['saveProject'])) {
    // Récupérer les valeurs soumises
    $title = trim($_POST['title'] ?? '');
    $needs = trim($_POST['needs'] ?? '');
    $domain_id = (int)($_POST['domain_id'] ?? 0);
    $selected_domain_id = $domain_id;
    
    // Validation
    if (empty($title)) {
        $errorsProject[] = "Le titre est obligatoire";
    }
    
    if ($domain_id <= 0) {
        $errorsProject[] = "Veuillez sélectionner un domaine";
    }
    
    // Si pas d'erreurs, créer le projet
    if (empty($errorsProject)) {
        $user_id = (int)$_SESSION['user']['id'];
        $res = saveProject($pdo, $title, $user_id, $domain_id, $needs);
        
        if ($res) {
            header('Location: show-project.php?id=' . $res . '&success=' . urlencode('Projet créé avec succès !'));
            exit();
        } else {
            $errorsProject[] = "Le projet n'a pas été enregistré";
        }
    }
}
?>

<div class="container py-4">
    <h1>Ajouter un projet</h1>
    
    <?php foreach ($errorsProject as $error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    
    <form action="" method="post">
        <div class="mb-3">
            <label for="title" class="form-label">Titre *</label>
            <input type="text" 
                   name="title" 
                   id="title" 
                   class="form-control" 
                   value="<?= htmlspecialchars($title) ?>" 
                   placeholder="Nom de votre projet"
                   required>
        </div>
        
        <div class="mb-3">
            <label for="domain_id" class="form-label">Domaine *</label>
            <select name="domain_id" id="domain_id" class="form-control" required>
                <option value="">-- Sélectionnez un domaine --</option>
                <?php foreach ($domains as $domain): ?>
                    <option value="<?= $domain['id'] ?>" 
                            <?= ($selected_domain_id == $domain['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($domain['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="mb-3">
            <label for="needs" class="form-label">Les besoins du projet</label>
            <textarea name="needs" 
                      id="needs" 
                      class="form-control" 
                      rows="4" 
                      placeholder="Décrivez les besoins et objectifs de ce projet..."><?= htmlspecialchars($needs) ?></textarea>
            <div class="form-text">Optionnel : détaillez les fonctionnalités, contraintes, ou objectifs spécifiques.</div>
        </div>
        
        <div class="d-flex gap-2">
            <button type="submit" name="saveProject" class="btn btn-primary">
                <i class="bi bi-plus-lg me-2"></i>Créer le projet
            </button>
            <a href="dashboard.php?tab=projets" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Annuler
            </a>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . "/../templates/footer.php";
ob_end_flush();
?>