<?php
session_start();
include("menu.php");
include("_conf.php");

if (!isset($_SESSION['Sid'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['Sid'];
$conn = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

$sql = "SELECT * FROM cr WHERE num_utilisateur = ? ORDER BY date DESC, titre ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<body>
<div class="container mt-4">
    <h3>Mes Comptes Rendus</h3>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Titre</th>
                    <th>Descriptif</th>
                    <th>Vu</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['date']) ?></td>
                        <td><?= htmlspecialchars($row['titre']) ?></td>
                        <td><?= nl2br(htmlspecialchars(substr($row['description'], 0, 100))) ?>...</td>
                        <td><?= $row['vu'] ? 'Oui' : 'Non' ?></td>
                        <td>
                            <a href="creer_compte_rendus.php?date=<?= urlencode($row['date']) ?>&titre=<?= urlencode($row['titre']) ?>" class="btn btn-sm btn-primary">Modifier</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">Aucun compte rendu trouvé.</div>
    <?php endif; ?>
    </div>
</body>
</html>
