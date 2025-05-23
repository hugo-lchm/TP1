<?php
session_start();
include '_conf.php';

// Fonction pour générer un mot de passe aléatoire sécurisé
function genererMotDePasse($longueur = 12) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!+()*-/';
    $motdepasse = '';
    $max = strlen($caracteres) - 1;
    for ($i = 0; $i < $longueur; $i++) {
        $motdepasse .= $caracteres[random_int(0, $max)];
    }
    return $motdepasse;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $lemail = trim($_POST['email']);

    // Connexion BDD
    $connexion = mysqli_connect($serveurBDD, $userBDD, $mdpBDD, $nomBDD);
    if (!$connexion) {
        $message = "<div class='alert alert-danger'>Erreur de connexion à la base de données.</div>";
    } else {
        // Préparer la requête pour éviter injection SQL
        $stmt = $connexion->prepare("SELECT login FROM utilisateur WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $lemail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $donnees = $result->fetch_assoc();
            $login = $donnees['login'];

            // Générer un nouveau mot de passe
            $newmdp = genererMotDePasse(12);
            $mdphache = password_hash($newmdp, PASSWORD_DEFAULT);

            // Mettre à jour le mot de passe dans la BDD
            $update_stmt = $connexion->prepare("UPDATE utilisateur SET motdepasse = ? WHERE email = ?");
            $update_stmt->bind_param('ss', $mdphache, $lemail);

            if ($update_stmt->execute()) {
                // Préparer le mail
                $sujet = "Votre nouveau mot de passe sur le site Hlachaume";
                $contenu = "Bonjour $login,\n\nVoici vos identifiants de connexion mis à jour :\n\nLogin : $login\nNouveau mot de passe : $newmdp\n\nMerci de vous connecter et de changer ce mot de passe rapidement.\n\nCordialement,\nL'équipe Hlachaume";

                // Envoi du mail
                $headers = "From: no-reply@hlachaume.fr\r\n";
                if (mail($lemail, $sujet, $contenu, $headers)) {
                    $message = "<div class='alert alert-success'>Un email avec votre nouveau mot de passe a été envoyé à $lemail.</div>";
                } else {
                    $message = "<div class='alert alert-warning'>Le mot de passe a été réinitialisé, mais l'email n'a pas pu être envoyé.</div>";
                }
            } else {
                $message = "<div class='alert alert-danger'>Erreur lors de la mise à jour du mot de passe.</div>";
            }
        } else {
            $message = "<div class='alert alert-danger'>Email non trouvé dans la base.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mot de passe oublié</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4" style="max-width: 400px;">
    <h2>Mot de passe oublié</h2>
    <?= $message ?>
    <form method="post" action="">
        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input type="email" id="email" name="email" required class="form-control" placeholder="Votre email">
        </div>
        <button type="submit" class="btn btn-primary w-100">Réinitialiser le mot de passe</button>
    </form>
</div>
</body>
</html>
