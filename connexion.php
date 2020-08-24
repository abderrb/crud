<?php


// Définir l'attribut 'SameSite'
setcookie('CookieSameSite', 'Contenu', [
    'Samesite' => 'Strict',
    'expires' => strtotime('+1 day')
]);



require_once 'inc/header.php';
// On se connecte à la base de données
require_once 'inc/connect.php';

// On vérifie que POST n'est pas vide
if (!empty($_POST)) {
    // On vérifie que tous les champs obligatoires sont remplis
    if (
        isset($_POST['email']) && !empty($_POST['email'])
        && isset($_POST['pass']) && !empty($_POST['pass'])
    ) {
        // On récupère et on nettoie les données
        // On vérifie la validité de l'e-mail
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            die('email invalide');
            header('Location: inscription.php');
        } else {
            $email = $_POST['email'];
        }

        // On écrit la requête
        $sql = 'SELECT * FROM `users` WHERE `email` = :email;';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':email', $email, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();

        // On récupère les données
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            die('Email et/ou mot de passe incorrect');
        }

        if (password_verify($_POST['pass'], $user['password'])) {
            // On ouvre la session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'roles' => $user['roles'],
            ];

            if(isset($_POST['remember']) && $_POST['remember'] == true){
                // La case a été cochée
                // On génère un token
                $token = md5(uniqid());

                // On le stock en base 
            $sql = "UPDATE `users` SET `remember_token` = '$token' WHERE `id` = {$user['id']};";

                // On exécute la requête
                $query = $db->query($sql);

                // On le stock dans un cookie
                setcookie('remember', $token, ['samesite' => 'Strict', 'expires' => strtotime('+1year')]) ;
                
            }      
                
            header('Location: index.php');
        } else {
            echo 'Email et/ou mot de passe incorrect';
        }
    } else {
        $erreur = "Formulaire incomplet";
    }
}


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>

<body>
      <?php
      require_once 'inc/nav.php';
      ?>
    <h1>Connexion</h1>
    <form method="post">
        <div>
            <label for="email">Email :</label>
            <input type="email" name="email" id="email">
        </div>
        <div>
            <label for="pass">Mot de passe :</label>
            <input type="password" name="pass" id="pass">
        </div>
        <div>
            <input type="checkbox" id="remember" name="remember" checked>
            <label for="scales">Je reste connecté</label>
        </div>
        <button>Me connecter</button>
        <a href="formulaire.php">j'ai oublié mon mot de passe</a>
    </form>

</body>

</html>