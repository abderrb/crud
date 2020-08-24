<?php

require_once 'inc/header.php';

require_once 'inc/connect.php';

// On vérifie que POST n'est pas vide
if (!empty($_POST)) {
    // On vérifie que tous les champs obligatoires sont remplis
    if (
        isset($_POST['nom']) && !empty($_POST['nom'])
        && isset($_POST['email']) && !empty($_POST['email'])
        && isset($_POST['phone']) && !empty($_POST['phone'])
        && isset($_POST['pass']) && !empty($_POST['pass'])
        && isset($_POST['pass2']) && !empty($_POST['pass2'])
    ) {
        // On récupère et on nettoie les données
        $nom = strip_tags($_POST['nom']);

        // On vérifie la validité de l'e-mail
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'][] = 'email invalide';
        } else {
            $email = $_POST['email'];
        }

        $phone = strip_tags($_POST['phone']);


        // On vérifie que les mots de passe sont identiques
        if ($_POST['pass'] != $_POST['pass2']) {
            $_SESSION['message'][] = 'Mots de passe différents';
        } else {
            $pass = password_hash($_POST['pass'], PASSWORD_ARGON2ID);
        }

        // S'il y a des messsages d'erreur on redirige
        if (!empty($_SESSION['message'])) {
            header('Location: inscription.php');
            exit;
        }else{
        $_SESSION['message'][] = "Formulaire incomplet";
        }

        // On se connecte à la base de données
        require_once 'inc/connect.php';

        // On écrit la requête
        $sql = 'INSERT INTO `users`(`email`, `password`, `name`, `phone`) VALUES (:email, :password, :name, :phone);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':email', $email, PDO::PARAM_STR);
        $query->bindValue(':password', $pass, PDO::PARAM_STR);
        $query->bindValue(':name', $nom, PDO::PARAM_STR);
        $query->bindValue(':phone', $phone, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();

        // On récupère l'id de l'utilisateur dans $num
        $num = $db->lastInsertId();
        $_SESSION['message'][] = "Vous êtes inscrit(e) sous le numéro $num";

    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>

<body>
    <h1>Inscription</h1>
    <?php
    if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
        foreach ($_SESSION['message'] as $message) {
            echo "<p>$message</p>";
        }
        unset($_SESSION['message']);
    }
    ?>

    <form method="post">
        <div>
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom">
        </div>
        <div>
            <label for="email">Email :</label>
            <input type="email" name="email" id="email">
        </div>
        <div>
            <label for="phone">Téléphone :</label>
            <input type="tel" name="phone" id="phone">
        </div>
        <div>
            <label for="pass">Mot de passe :</label>
            <input type="password" name="pass" id="pass">
        </div>
        <div>
            <label for="pass2">Confirmer le mot de passe :</label>
            <input type="password" name="pass2" id="pass2">
        </div>
        <button>M'inscrire</button>
    </form>
</body>

</html>