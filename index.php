<?php
session_start();
include "_conf.php";

$message = "";

// Connexion BDD
$connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
if (!$connexion) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

// Si formulaire soumis
if (isset($_POST['send_con'])) {
    $login = trim($_POST['login'] ?? '');
    $mdp = $_POST['mdp'] ?? '';

    if ($login === '' || $mdp === '') {
        $message = "<div class='alert alert-danger'>Veuillez remplir tous les champs.</div>";
    } else {
        $stmt = $connexion->prepare("SELECT * FROM utilisateur WHERE login = ? LIMIT 1");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $resultat = $stmt->get_result();

        if ($resultat && $resultat->num_rows === 1) {
            $donnees = $resultat->fetch_assoc();
            if (password_verify($mdp, $donnees['motdepasse'])) {
                $_SESSION['Sid'] = $donnees['num'];
                $_SESSION['Slogin'] = $donnees['login'];
                $_SESSION['Stype'] = $donnees['type'];
                $_SESSION['Sprenom'] = $donnees['prenom'];
                header('Location: accueil.php');
                exit();
            } else {
                $message = "<div class='alert alert-danger'>Login ou mot de passe incorrect.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Login ou mot de passe incorrect.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Connexion</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5" style="max-width: 400px;">
  <h2 class="mb-4 text-center">Connexion</h2>

  <?= $message ?>

  <form method="post" action="">
    <div class="mb-3">
      <label for="login" class="form-label">Login</label>
      <input type="text" class="form-control" id="login" name="login" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label for="mdp" class="form-label">Mot de passe</label>
      <input type="password" class="form-control" id="mdp" name="mdp" required>
    </div>
    <button type="submit" name="send_con" class="btn btn-primary w-100">Se connecter</button>
  </form>

  <div class="mt-3 d-flex justify-content-between">
    <a href="inscription.php">Créer un compte</a>
    <a href="oubli.php">Mot de passe oublié ?</a>
  </div>
</div>

</body>
</html>
