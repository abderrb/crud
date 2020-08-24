<?php
require_once '../inc/header.php';

// On vérifie si on a un id dans l'url
if(isset($_GET['id']) && !empty($_GET['id'])){
    // On a un id, on va chercher la catégorie dans la base
    // On se connecte
    require_once '../inc/connect.php';

    // On écrit la requête
    $sql = 'SELECT * FROM `categories` WHERE `id` = :id;';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On accroche les valeurs aux paramètres
    $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère les données
    $categorie = $query->fetch(PDO::FETCH_ASSOC);

    if(!$categorie){
        // Pas de catégorie
        header('Location: index.php');
    }

    // On vérifie que POST contient des données
    if(!empty($_POST)){
        // On vérifie que tous les champs obligatoires sont remplis
        if(isset($_POST['nom']) && !empty($_POST['nom'])){
            // Le formulaire est complet
            // On récupère et on nettoie les données
            $nom = strip_tags($_POST['nom']);

            // On stocke les données en base
            // On écrit la requête
            $sql = "UPDATE `categories` SET `name` = :nom WHERE `id` = {$categorie['id']}";

            // On prépare la requête
            $query = $db->prepare($sql);

            // On injecte les paramètres
            $query->bindValue(':nom', $nom, PDO::PARAM_STR);

            // On exécute
            $query->execute();

            // On redirige l'utilisateur vers index
            header('Location: index.php');
        }
    }

}else{
    header('Location: index.php');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une catégorie</title>
</head>
<body>
    <h1>Modifier une catégorie</h1>
    <form method="post">
        <div>
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" value="<?= $categorie['name'] ?>">
        </div>
        <button>Modifier</button>
    </form>
</body>
</html>