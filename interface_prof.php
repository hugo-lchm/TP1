<?php
session_start();
if (!isset($_SESSION['Sid']) || $_SESSION['Stype'] != 1) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Interface Professeur</title>
</head>
<body>
<?php include("menu.php"); ?>

<div class="container mt-4">
    <h2>Bienvenue Professeur <?= htmlspecialchars($_SESSION['Sprenom']) ?></h2>
    <p>Vous pouvez gérer les comptes rendus, voir la liste des élèves, ajouter des commentaires, etc.</p>
</div>

</body>
</html>
