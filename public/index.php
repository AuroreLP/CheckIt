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
      <div>
        <h2 class="text-center">Comment ça fonctionne?</h2>
        <ol class="list-group list-group-numbered">
          <li class="list-group-item lead">Créez un compte en cliquant sur le bouton "Login"</li>
          <li class="list-group-item lead">Allez dans l'onglet Dashboard - C'est là que vous pourrez organiser vos tâches</li>
          <li class="list-group-item lead">Dans la barre latérale, vous trouverez l'onglet "Mes Domaines" pour ajouter  les domaines personnels et/ou professionnels qui vous intéressent</li>
          <li class="list-group-item lead">Ensuite, allez dans l'onglet "Mes projets" pour créer vos projets en les alliant au domaine correspondant</li>
          <li class="list-group-item lead">Enfin, tout en créant le projet, vous pouvez ajouter les premières tâches. Il sera toujours possible d'en ajouter d'autres plus tard.</li>
          <li class="list-group-item lead">L'onglet "Focus" vous permet d'avoir un aperçu de toutes les tâches en retard et à venir, tous projets combinés.</li>
        </ol>
      </div>
    </div>

    <div class="container col-xxl-8 px-4 py-5">
      <div>
        <h2 class="text-center">Exemple concret</h2>
        <div class="img-container">
          <div>
            <p>Cliquer sur le bouton "Login" pour se connecter ou créer un compte:</p>
            <img src="assets/images/checkit_login.png" alt="Page de connexion de CheckIt" class="img-fluid mx-auto d-block">
          </div>
          <i class="bi bi-arrow-down arrow-icon"></i>
          <div>
            <p>Dans la barre de menu, cliquer sur "Dashboard", puis aller "Mes Domaines" de la barre latérale:</p>
            <img src="assets/images/checkit_domaines.png" alt="Liste des domaines de CheckIt" class="img-fluid mx-auto d-block">
          </div>
          <i class="bi bi-arrow-down arrow-icon"></i>
          <div>
            <p>Ensuite ajouter un nouveau projet en allant dans l'onglet "Mes Projets":</p>
            <img src="assets/images/checkit_dashboard.png" alt="Liste des projets de CheckIt" class="img-fluid mx-auto d-block">
          </div>
          <i class="bi bi-arrow-down arrow-icon"></i>
          <div>
            <p>Une fois dans le projet, ajouter les tâches:</p>
            <img src="assets/images/checkit_projet.png" alt="Page de projet de CheckIt" class="img-fluid mx-auto d-block">
          </div>
          <i class="bi bi-arrow-down arrow-icon"></i>
          <div>
            <p>Profiter de l'onglet "Focus" pour ne se concentrer que sur les tâches en retard et à venir:</p>
            <img src="assets/images/checkit_focus.png" alt="Page Focus de CheckIt" class="img-fluid mx-auto d-block">
          </div>
        </div>
      </div>
    </div>
<?php
  require_once __DIR__. "/../templates/footer.php"
?>