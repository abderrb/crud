<?php
require_once '../inc/header.php';
require_once '../inc/connect.php';

// On vérifie si on a un id non vide dans l'URL
if(isset($_GET['id']) && !empty($_GET['id'])){
    // On a un id
    // On va chercher l'article dans la base
    $sql = 'SELECT a.*, u.`nickname`, c.`name` FROM `articles` a LEFT JOIN `users` u ON u.`id` = a.`users_id` LEFT JOIN `categories` c ON a.`categories_id` = c.`id` WHERE a.`id` = :id';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On injecte l'id
    $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère les données
    $article = $query->fetch(PDO::FETCH_ASSOC);

    // On vérifie si l'article n'existe pas
    if(!$article){
        header('Location: '. URL);   
    }

}else{
    // On n'a pas d'id ou il est vide, retour à l'accueil
    header('Location: '. URL);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1><?= $article['title'] ?></h1>
    <p>Publié par <?= $article['nickname'] ?> dans la catégorie <?= $article['name'] ?> le <?= formatDate($article['created_at']) ?></p>
    <p><?= htmlspecialchars_decode($article['content']) ?></p>
</body>
</html> 