<?php
require_once '../inc/header.php';
// On se connecte à la base de données
require_once '../inc/connect.php';

// On vérifie que POST n'est pas vide
if(!empty($_POST)){
    // On vérifie que tous les champs obligatoires sont remplis
    if(isset($_POST['nom']) && !empty($_POST['nom'])){
        // On récupère et on nettoie les données
        $nom = strip_tags($_POST['nom']);

        // On écrit la requête
        $sql = 'INSERT INTO `categories`(`name`) VALUES (:nom);';

        // On prépare la requête
        $query = $db->prepare($sql);

        // On injecte les valeurs dans les paramètres
        $query->bindValue(':nom', $nom, PDO::PARAM_STR);

        // On exécute la requête
        $query->execute();
    }else{
        $erreur = "Formulaire incomplet";
    }
}

// On écrit la requête
$sql = "SELECT * FROM `categories`;";

// On exécute la requête
$query = $db->query($sql);

// On récupère les données
$categories = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des catégories</title>
</head>
<body>
    <h1>Liste des catégories</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($categories as $categorie): ?>
                <tr>
                    <td><?= $categorie['id'] ?></td>
                    <td><?= $categorie['name'] ?></td>
                    <td>
                        <a href="modifier.php?id=<?= $categorie['id'] ?>">Modifier</a>
                        <a href="supprimer.php?id=<?= $categorie['id'] ?>">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h2>Ajouter une catégorie</h2>
    <form method="post">
        <div>
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom">
        </div>
        <button>Ajouter</button>
    </form>
</body>
</html>