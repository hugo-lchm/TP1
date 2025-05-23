<?php
session_start();

// Si non connecté, rediriger vers connexion (index.php)
if (!isset($_SESSION['Sid'])) {
    header("Location: index.php");
    exit();
}

include("menu.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Accueil - Hlachaume</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">

    <h2>Bienvenue <?= htmlspecialchars($_SESSION['Sprenom']) ?> !</h2>
    <p>Connecté en tant que : <strong><?= htmlspecialchars($_SESSION['Slogin']) ?></strong></p>
    <hr>

    <?php if ($_SESSION['Stype'] == 1): ?>
        <div class="alert alert-primary"><strong>Interface PROFESSEUR</strong></div>
        <ul class="list-group">
            <li class="list-group-item"><a href="gerer_compte_rendus.php">Gérer les comptes rendus</a></li>
            <li class="list-group-item"><a href="voir_eleves.php">Voir la liste des élèves</a></li>
            <li class="list-group-item"><a href="ajouter_commentaire.php">Ajouter un commentaire</a></li>
        </ul>
    <?php else: ?>
        <div class="alert alert-secondary"><strong>Interface ÉLÈVE</strong></div>
        <ul class="list-group">
            <li class="list-group-item"><a href="liste_compte_rendus.php">Voir mes comptes rendus</a></li>
            <li class="list-group-item"><a href="creer_compte_rendus.php">Créer un compte rendu</a></li>
            <li class="list-group-item"><a href="commentaires.php">Voir les commentaires</a></li>
        </ul>
    <?php endif; ?>

</div>
</body>
</html>
