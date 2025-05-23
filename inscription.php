<?php
session_start();
include '_conf.php';

$errors = [];
$success = false;

if (isset($_POST['inscrire'])) {
    // Récupération et nettoyage des données
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $tel = trim($_POST['tel']);
    $login = trim($_POST['login']);
    $motdepasse = $_POST['motdepasse'];
    $type = intval($_POST['type']);
    $email = trim($_POST['email']);

    // Validation simple
    if (empty($nom) || empty($prenom) || empty($tel) || empty($login) || empty($motdepasse) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Merci de remplir tous les champs correctement.";
    } else {
        // Connexion BDD
        $connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
        if (!$connexion) {
            $errors[] = "Erreur de connexion à la base de données.";
        } else {
            // Échapper les données
            $nom = mysqli_real_escape_string($connexion, $nom);
            $prenom = mysqli_real_escape_string($connexion, $prenom);
            $tel = mysqli_real_escape_string($connexion, $tel);
            $login = mysqli_real_escape_string($connexion, $login);
            $email = mysqli_real_escape_string($connexion, $email);

            // Vérifier si login ou email existe déjà
            $query_check = "SELECT * FROM utilisateur WHERE login='$login' OR email='$email'";
            $res_check = mysqli_query($connexion, $query_check);
            if (mysqli_num_rows($res_check) > 0) {
                $errors[] = "Login ou email déjà utilisé.";
            } else {
                // Hachage du mot de passe
                $hash_mdp = password_hash($motdepasse, PASSWORD_DEFAULT);

                // Insertion en base
                $query_insert = "INSERT INTO utilisateur (nom, prenom, tel, login, motdepasse, type, email) 
                                 VALUES ('$nom', '$prenom', '$tel', '$login', '$hash_mdp', $type, '$email')";

                if (mysqli_query($connexion, $query_insert)) {
                    $success = true;
                } else {
                    $errors[] = "Erreur lors de l'inscription : " . mysqli_error($connexion);
                }
            }
            mysqli_close($connexion);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2>Créer un compte</h2>

    <?php if ($success): ?>
        <div class="alert alert-success">Inscription réussie ! Vous pouvez maintenant vous connecter.</div>
    <?php else: ?>
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="tel" class="form-label">Téléphone</label>
                <input type="tel" class="form-control" id="tel" name="tel" required value="<?= htmlspecialchars($_POST['tel'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="login" class="form-label">Login</label>
                <input type="text" class="form-control" id="login" name="login" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="motdepasse" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="motdepasse" name="motdepasse" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type d'utilisateur</label>
                <select class="form-select" id="type" name="type" required>
                    <option value="0" <?= (isset($_POST['type']) && $_POST['type'] == '0') ? 'selected' : '' ?>>Élève</option>
                    <option value="1" <?= (isset($_POST['type']) && $_POST['type'] == '1') ? 'selected' : '' ?>>Professeur</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary" name="inscrire">S'inscrire</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
