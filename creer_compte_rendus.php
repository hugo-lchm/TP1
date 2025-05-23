<?php
session_start();
include("menu.php");
include("_conf.php");

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['Sid'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['Sid'];
$conn = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

$date = $_GET['date'] ?? date("Y-m-d");
$titre = $_GET['titre'] ?? '';
$description = "";
$message = "";

// Pré-remplir s'il existe déjà un CR pour cette date + titre
if (!empty($titre)) {
    $stmt = $conn->prepare("SELECT description FROM cr WHERE num_utilisateur = ? AND date = ? AND titre = ?");
    $stmt->bind_param("iss", $user_id, $date, $titre);
    $stmt->execute();
    $stmt->bind_result($description);
    $stmt->fetch();
    $stmt->close();
}

// Formulaire soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $titre = $_POST['titre'];
    $description = $_POST['description'];

    // Vérifie si ce CR existe déjà
    $stmt = $conn->prepare("SELECT id FROM cr WHERE num_utilisateur = ? AND date = ? AND titre = ?");
    $stmt->bind_param("iss", $user_id, $date, $titre);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Mise à jour
        $stmt->close();
        $stmt = $conn->prepare("UPDATE cr SET description = ?, vu = 0 WHERE num_utilisateur = ? AND date = ? AND titre = ?");
        $stmt->bind_param("siss", $description, $user_id, $date, $titre);
        $stmt->execute();
        $message = "Compte rendu mis à jour.";
    } else {
        // Insertion
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO cr (num_utilisateur, date, titre, description, vu) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("isss", $user_id, $date, $titre, $description);
        $stmt->execute();
        $message = "Compte rendu ajouté.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<body>
<div class="container mt-4">
    <h3>Créer un Compte Rendu</h3>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="card p-4 mt-3">
        <div class="mb-3">
            <label for="date" class="form-label">Date :</label>
            <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($date) ?>" required>
        </div>
        <div class="mb-3">
            <label for="titre" class="form-label">Titre :</label>
            <input type="text" class="form-control" name="titre" value="<?= htmlspecialchars($titre) ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Descriptif :</label>
            <textarea class="form-control" name="description" rows="6"><?= htmlspecialchars($description) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </form>
</div>
</body>
</html>
