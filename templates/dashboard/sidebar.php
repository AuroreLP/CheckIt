
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-person-circle me-2"></i>
            Dashboard
        </h5>
    </div>
    <div class="card-body p-0">
        <nav class="nav flex-column">
            <a class="nav-link <?= $activeTab == 'projets' ? 'active' : '' ?>" href="dashboard.php?tab=projets">
                <i class="bi bi-folder me-2"></i>Mes Projets
            </a>
            <a class="nav-link <?= $activeTab == 'domaines' ? 'active' : '' ?>" href="dashboard.php?tab=domaines">
                <i class="bi bi-globe me-2"></i>Domaines
            </a>
            <a class="nav-link <?= $activeTab == 'settings' ? 'active' : '' ?>" href="dashboard.php?tab=settings">
                <i class="bi bi-gear me-2"></i>Paramètres
            </a>
            <a class="nav-link <?= $activeTab == 'statistiques' ? 'active' : '' ?>" href="dashboard.php?tab=statistiques">
                <i class="bi bi-bar-chart me-2"></i>Statistiques
            </a>
        </nav>
    </div>
</div>