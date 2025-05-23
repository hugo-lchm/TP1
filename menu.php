<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Sid'])) {
    // optionnel : redirection si pas connecté
    // header("Location: index.php"); exit();
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      Espace <?= ($_SESSION['Stype'] == 1) ? 'Prof' : 'Étudiant' ?>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarMenu">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <!-- Communs -->
        <li class="nav-item"><a class="nav-link" href="accueil.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="perso.php">Page Perso</a></li>

        <?php if ($_SESSION['Stype'] == 1): ?>
          <!-- Prof uniquement -->
          <li class="nav-item"><a class="nav-link" href="liste_compte_rendus.php">Compte rendus</a></li>
          <li class="nav-item"><a class="nav-link" href="commentaires.php">Commentaires</a></li>
        <?php else: ?>
          <!-- Élève uniquement -->
          <li class="nav-item"><a class="nav-link" href="liste_compte_rendus.php">Compte rendus</a></li>
          <li class="nav-item"><a class="nav-link" href="creer_compte_rendus.php">Créer CR</a></li>
        <?php endif; ?>

        <li class="nav-item"><a class="nav-link" href="logout.php">Se déconnecter</a></li>
      </ul>
    </div>
  </div>
</nav>
