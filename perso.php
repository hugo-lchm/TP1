<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes informations</title>
</head>

<?php
session_start();
include("menu.php");


// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['Sid'])) {
    header("Location: index.php");  // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    exit();
}

include "_conf.php";

// Connexion à la base de données
$connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
if (!$connexion) {
    die("Connexion échouée: " . mysqli_connect_error());
}

// Récupérer les informations actuelles de l'utilisateur
$user_id = $_SESSION['Sid'];
$requete = "SELECT * FROM utilisateur WHERE num='$user_id'";
$resultat = mysqli_query($connexion, $requete);

if (!$resultat) {
    die("Erreur de la requête de récupération des données : " . mysqli_error($connexion));
}

$utilisateur = mysqli_fetch_assoc($resultat);

// Mise à jour des informations de l'utilisateur
if (isset($_POST['update'])) {
    // Récupération des données envoyées par le formulaire
    $nouveau_email = isset($_POST['email']) ? $_POST['email'] : '';
    $nouveau_mdp = isset($_POST['mdp']) ? $_POST['mdp'] : '';
    $ancien_mdp = isset($_POST['ancien_mdp']) ? $_POST['ancien_mdp'] : '';
    $nouveau_login = isset($_POST['login']) ? $_POST['login'] : '';
    $nouveau_nom = isset($_POST['nom']) ? $_POST['nom'] : '';
    $nouveau_prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
    $nouveau_type = isset($_POST['type']) ? $_POST['type'] : $utilisateur['type']; // Valeur par défaut si vide

    // Vérification de l'ancien mot de passe
    if (!empty($ancien_mdp)) {
        // Vérification du mot de passe avec password_verify
        if (!password_verify($ancien_mdp, $utilisateur['motdepasse'])) {
            echo "Ancien mot de passe incorrect.";
        } else {
            if (!empty($nouveau_mdp)) {
                // Crypter le nouveau mot de passe
                $nouveau_mdp = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
                // Mise à jour du mot de passe dans la base de données
                $update_mdp = "UPDATE utilisateur SET motdepasse='$nouveau_mdp' WHERE num='$user_id'";
                $result_mdp = mysqli_query($connexion, $update_mdp);
                if (!$result_mdp) {
                    echo "Erreur lors de la mise à jour du mot de passe : " . mysqli_error($connexion);
                } else {
                    echo "Mot de passe mis à jour avec succès!";
                }
            }
        }
    }

    // Mise à jour de l'email
    if (!empty($nouveau_email) && $nouveau_email != $utilisateur['email']) {
        $update_email = "UPDATE utilisateur SET email='$nouveau_email' WHERE num='$user_id'";
        $result_email = mysqli_query($connexion, $update_email);
        if (!$result_email) {
            echo "Erreur lors de la mise à jour de l'email : " . mysqli_error($connexion);
        } else {
            echo "Email mis à jour avec succès!";
        }
    }

    // Mise à jour du login, nom, prénom
    if (!empty($nouveau_login) && $nouveau_login != $utilisateur['login']) {
        $update_login = "UPDATE utilisateur SET login='$nouveau_login' WHERE num='$user_id'";
        $result_login = mysqli_query($connexion, $update_login);
        if (!$result_login) {
            echo "Erreur lors de la mise à jour du login : " . mysqli_error($connexion);
        }
    }

    if (!empty($nouveau_nom) && $nouveau_nom != $utilisateur['nom']) {
        $update_nom = "UPDATE utilisateur SET nom='$nouveau_nom' WHERE num='$user_id'";
        $result_nom = mysqli_query($connexion, $update_nom);
        if (!$result_nom) {
            echo "Erreur lors de la mise à jour du nom : " . mysqli_error($connexion);
        }
    }

    if (!empty($nouveau_prenom) && $nouveau_prenom != $utilisateur['prenom']) {
        $update_prenom = "UPDATE utilisateur SET prenom='$nouveau_prenom' WHERE num='$user_id'";
        $result_prenom = mysqli_query($connexion, $update_prenom);
        if (!$result_prenom) {
            echo "Erreur lors de la mise à jour du prénom : " . mysqli_error($connexion);
        }
    }

    // Mise à jour du type (0 = élève, 1 = professeur)
    if ($nouveau_type != $utilisateur['type']) {
        $update_type = "UPDATE utilisateur SET type='$nouveau_type' WHERE num='$user_id'";
        $result_type = mysqli_query($connexion, $update_type);
        if (!$result_type) {
            echo "Erreur lors de la mise à jour du type : " . mysqli_error($connexion);
        } else {
            echo "Type mis à jour avec succès!";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations Personnelles</title>
</head>
<body>
    <h1>Informations Personnelles</h1>

    <p><strong>Login : </strong><?php echo $utilisateur['login']; ?></p>
    <p><strong>Nom : </strong><?php echo $utilisateur['nom']; ?></p>
    <p><strong>Prénom : </strong><?php echo $utilisateur['prenom']; ?></p>
    <p><strong>Email : </strong><?php echo $utilisateur['email']; ?></p>
    <p><strong>Type : </strong><?php echo $utilisateur['type'] == 1 ? "Professeur" : "Élève"; ?></p>

    <form method="POST" action="perso.php">
        <h2>Mettre à jour mes informations</h2>
        
        <!-- Changement du login -->
        <label for="login">Nouveau login :</label>
        <input type="text" name="login" value="<?php echo $utilisateur['login']; ?>" required><br><br>

        <!-- Changement du nom -->
        <label for="nom">Nouveau nom :</label>
        <input type="text" name="nom" value="<?php echo $utilisateur['nom']; ?>" required><br><br>

        <!-- Changement du prénom -->
        <label for="prenom">Nouveau prénom :</label>
        <input type="text" name="prenom" value="<?php echo $utilisateur['prenom']; ?>" required><br><br>

        <!-- Changement de l'email -->
        <label for="email">Nouvel email :</label>
        <input type="email" name="email" value="<?php echo $utilisateur['email']; ?>" required><br><br>

        <!-- Changement du type -->
        <label for="type">Type d'utilisateur :</label>
        <select name="type">
            <option value="1" <?php echo $utilisateur['type'] == 1 ? 'selected' : ''; ?>>Professeur</option>
            <option value="0" <?php echo $utilisateur['type'] == 0 ? 'selected' : ''; ?>>Élève</option>
        </select><br><br>

        <!-- Changement de mot de passe -->
        <label for="ancien_mdp">Ancien mot de passe :</label>
        <input type="password" name="ancien_mdp" placeholder="Ancien mot de passe"><br><br>

        <label for="mdp">Nouveau mot de passe :</label>
        <input type="password" name="mdp" placeholder="Nouveau mot de passe"><br><br>

        <input type="submit" name="update" value="Mettre à jour">
    </form>

    <hr>
</body>
</html>
