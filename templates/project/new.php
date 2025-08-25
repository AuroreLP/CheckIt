<div class="container col-xxl-8 py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="bi bi-plus-square me-2"></i>Créer un nouveau projet</h1>
        <a href="dashboard.php?tab=projets" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Retour à la liste
        </a>
    </div>

    <!-- Messages d'alerte -->
    <?php foreach ($errorsProject as $error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>
    
    <?php foreach ($messagesProject as $message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endforeach; ?>

    <!-- Formulaire de création -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-gear me-2"></i>Informations du nouveau projet
            </h5>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="row">
                    <!-- Colonne de gauche -->
                    <div class="col-md-6">
                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre du projet *</label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($formData['title']) ?>" 
                                   placeholder="Nom de votre projet"
                                   required>
                        </div>

                        <!-- Domaine -->
                        <div class="mb-3">
                            <label for="domain_id" class="form-label">Domaine *</label>
                            <select name="domain_id" id="domain_id" class="form-select" required>
                                <option value="">-- Sélectionnez un domaine --</option>
                                <?php foreach ($domains as $domain): ?>
                                    <option value="<?= $domain['id'] ?>" 
                                            <?= ($formData['domain_id'] == $domain['id']) ? 'selected' : '' ?>>
                                        <i class="bi <?= htmlspecialchars($domain['icon'] ?? 'bi-folder') ?>"></i>
                                        <?= htmlspecialchars($domain['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Dates -->
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <label for="start_date" class="form-label">Date de début</label>
                                <input type="date" 
                                       name="start_date" 
                                       id="start_date" 
                                       class="form-control"
                                       value="<?= htmlspecialchars($formData['start_date']) ?>">
                                <div class="form-text">Optionnel</div>
                            </div>
                            <div class="col-sm-6 mb-3">
                                <label for="end_date" class="form-label">Date de fin prévue</label>
                                <input type="date" 
                                       name="end_date" 
                                       id="end_date" 
                                       class="form-control"
                                       value="<?= htmlspecialchars($formData['end_date']) ?>">
                                <div class="form-text">Optionnel</div>
                            </div>
                        </div>

                        <!-- Statut -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut initial</label>
                            <select name="status" id="status" class="form-select">
                                <?php foreach ($projectStatuses as $statusOption): ?>
                                    <option value="<?= $statusOption->value ?>" 
                                            <?= $formData['status'] === $statusOption->value ? 'selected' : '' ?>>
                                        <i class="bi <?= $statusOption->getIcon() ?>"></i>
                                        <?= $statusOption->getLabel() ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Par défaut "Planification" - vous pourrez le modifier plus tard
                            </div>
                        </div>
                    </div>

                    <!-- Colonne de droite -->
                    <div class="col-md-6">
                        <!-- Besoins -->
                        <div class="mb-3">
                            <label for="needs" class="form-label">Besoins et objectifs du projet</label>
                            <textarea name="needs" 
                                      id="needs" 
                                      class="form-control" 
                                      rows="12"
                                      placeholder="Décrivez les besoins, objectifs et contraintes de ce projet..."><?= htmlspecialchars($formData['needs']) ?></textarea>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Optionnel : Détaillez les fonctionnalités attendues, contraintes techniques, objectifs business...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="d-flex justify-content-between flex-wrap gap-2 pt-3 border-top">
                    <div>
                        <button type="submit" name="saveProject" class="btn btn-primary me-2">
                            <i class="bi bi-plus-lg me-1"></i>Créer le projet
                        </button>
                        <a href="dashboard.php?tab=projets" class="btn btn-secondary">
                            <i class="bi bi-x-lg me-1"></i>Annuler
                        </a>
                    </div>
                    
                    <div class="text-muted small align-self-center">
                        <i class="bi bi-lightbulb me-1"></i>
                        Vous pourrez ajouter des tâches après la création
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>