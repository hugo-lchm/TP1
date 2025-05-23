<?php
session_start();
if (!isset($_SESSION['Sid']) || $_SESSION['Stype'] != 0) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Interface Élève</title>
</head>
<body>
<?php include("menu.php"); ?>

<div class="container mt-4">
    <h2>Bienvenue <?= htmlspecialchars($_SESSION['Sprenom']) ?></h2>
    <p>Vous pouvez voir vos comptes rendus, créer un compte rendu, voir les commentaires, etc.</p>
</div>

</body>
</html>
