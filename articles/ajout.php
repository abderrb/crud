<?php


require_once '../inc/header.php';
if(!isset($_SESSION['user'])){
    header('Location: '.URL.'/connexion.php');
    exit;
}
// Transforme une chaine de caractères "json" en tableau PHP
$roles = json_decode($_SESSION['user']['roles']);

// On vérifie si on a le rôle admin dans $roles
if(!in_array('ROLE_ADMIN', $roles)){
    header('Location: '.URL);
    exit; 
}

require_once '../inc/connect.php';

$sql = 'SELECT * FROM `categories` ORDER BY `name` ASC;';

$query = $db->query($sql);

$categories = $query->fetchAll(PDO::FETCH_ASSOC);

//requete sql pour les départements
$sql = 'SELECT * FROM `departements` WHERE `number` = :number;';

// On prépare la requête
$query = $db->prepare($sql);

// On injecte les valeurs
$query->bindValue(':number', $number, PDO::PARAM_STR);

 // On exécute la requête
 $query->execute();


// On vérifie que POST n'est pas vide
if(!empty($_POST)){
    // echo '<pre>';
    // var_dump($_FILES);
    // echo '</pre>';
    // die;
    // On vérifie que tous les champs obligatoires sont remplis
    if(
        isset($_POST['titre']) && !empty($_POST['titre'])
        && isset($_POST['contenu']) && !empty($_POST['contenu'])
        && isset($_POST['categorie']) && !empty($_POST['categorie'])
    ){
        // On récupère et on nettoie les données
        $titre = strip_tags($_POST['titre']);
        $contenu = htmlspecialchars($_POST['contenu']);
        $nomImage = null;

        // On récupère et on stocke l'image si elle existe
        if(
            isset($_FILES['image']) && !empty($_FILES['image'])
            && $_FILES['image']['error'] != UPLOAD_ERR_NO_FILE
        ){
            // On vérifie qu'on n'a pas d'erreur
            if($_FILES['image']['error'] != UPLOAD_ERR_OK){
                header('Location: ajout.php');
                exit;
            }

            // On génère un nouveau nom de fichier
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $nomImage = md5(uniqid()).'.'.$extension;

            $extensions = ['png', 'jpg', 'jpeg', 'jfif', 'pjpeg', 'pjp'];
            $types = ['image/png', 'image/jpeg'];

            // On vérifie si l'extension et le type sont absents des tableaux
            if(
                !in_array(strtolower($extension), $extensions)
                || !in_array($_FILES['image']['type'], $types)
            ){
                header('Location: ajout.php');
            }

            $tailleMax = 1048576; // 1024*1024

            // On vérifie si la taille dépasse le maximum
            if($_FILES['image']['size'] > $tailleMax){
                header('Location: ajout.php');
            }

            // On transfère le fichier
            if(!move_uploaded_file(
                    $_FILES['image']['tmp_name'],
                    __DIR__.'/../uploads/'.$nomImage
                )
            ){
                // Transfert échoué
                header('Location: ajout.php');
                exit;
            }
            
            mini(__DIR__.'/../uploads/'.$nomImage, 200);
            mini(__DIR__.'/../uploads/'.$nomImage, 300);

        }

        // On écrit la requête
        $sql = 'INSERT INTO `articles`(`title`,`content`,`users_id`,`categories_id`, `featured_image`) VALUES (:titre, :contenu, :user, :categorie, :nomimage);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':titre', $titre, PDO::PARAM_STR);
        $query->bindValue(':contenu', $contenu, PDO::PARAM_STR);
        $query->bindValue(':user', $_SESSION['user']['id'], PDO::PARAM_INT);
        $query->bindValue(':categorie', $_POST['categorie'], PDO::PARAM_INT);
        $query->bindValue(':nomimage', $nomImage, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();

        require_once '../inc/config-mail.php';

        header('Location: '.URL);
        exit;
    }else{
        $erreur = "Formulaire incomplet";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un article</title>
</head>
<body>
    <?
    require_once '../inc/nav.php';
?>
    <h1>Ajouter un article</h1>
    <form method="post" enctype="multipart/form-data">
        <div>
            <label for="titre">Titre : </label>
            <input type="text" name="titre" id="titre">
        </div>
        <div>
            <label for="contenu">Contenu : </label>
            <textarea name="contenu" id="contenu"></textarea>
        </div>
        <div>
            <label for="categorie">Catégorie : </label>
            <select name="categorie" id="categorie" required>
                <option value="">-- Choisir une catégorie --</option>
                <?php foreach($categories as $categorie): ?>
                    <option value="<?= $categorie['id'] ?>">
                        <?= $categorie['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="departements">Département : </label>
            <select name="departements" number="departements" required>
                <option value="">-- Choisir un département --</option>
                <?php foreach($departements as $departement): ?>
                    <option value="<?= $departement['number'] ?>">
                        <?= $departement['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="image">Image : </label>
            <input type="file" name="image" id="image" accept="image/png, image/jpeg">
        </div>
        <button>Ajouter l'article</button>
    </form>
</body>
</html>