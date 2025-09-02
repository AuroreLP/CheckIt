<?php
  require_once __DIR__. "/../templates/header.php"
?>

    <div class="container col-xxl-8">
      <div class="row flex-lg-row-reverse align-items-center g-5 py-2">
        <div class="col-10 col-sm-8 col-lg-6">
          <img src="assets/images/Logo-Checkit.png" alt="logo checkit" width="400" loading="lazy">
        </div>
        <div class="col-lg-6">
          <h1 class="display-5 fw-bold lh-1 mb-3">Suivez vos projets de partout!</h1>
          <p class="lead">Bienvenue sur CheckIt, votre nouvelle plateforme de création de listes de tâches en ligne. Avec CheckIt, vous pouvez facilement créer des projets et des tâches pour tous les aspects de votre vie. Que vous planifiez votre prochain voyage, votre travail ou vos courses, CheckIt vous aide à rester organisé et à suivre vos projets en toute simplicité.</p>
        </div>
      </div>
    </div>
    
    <div class="container col-xxl-8 px-4 py-5">
      <div class="text-center">
        <h2>Découvrez les fonctionnalités principales</h2>
        <div class="row row-cols-1 row-cols-md-3 g-4">
          <div class="col">
            <div class="card h-100 text-center">
              <div class="card-header">
                <i class="bi bi-card-checklist"></i>
              </div>
              <div class="card-body d-flex align-items-center">
                <h3 class="card-title">Créez vos listes de projets</h3>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card h-100 text-center">
              <div class="card-header">
                <i class="bi bi-tags-fill"></i>
              </div>
              <div class="card-body d-flex align-items-center">
                <h3 class="card-title">Classez les projets par domaine</h3>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card h-100 text-center">
              <div class="card-header">
                <i class="bi bi-search"></i>
              </div>
              <div class="card-body d-flex align-items-center">
                <h3 class="card-title">Retrouvez vos tâches par projet</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

<?php
  require_once __DIR__. "/../templates/footer.php"
?>