<?php
require_once 'inc/header.php';

require_once 'inc/connect.php';

require_once 'inc/functions.php';

require_once 'inc/nav.php';

$sql = 'SELECT
a.*,
c.`name` as categories_name,
u.`name` as users_name, 
d.`name` as departements_name
FROM `annonces` a 
LEFT JOIN `categories` c ON c.`id` = a.`categories_id`
LEFT JOIN `users` u ON u.`id` = a.`users_id`
LEFT JOIN `departements` d ON d.`number` = a.`departement_number`
ORDER BY  a.`created_at` DESC;';

$query = $db->query($sql);

$annonces = $query->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Les annonces du moment</h1>
    <?php
    if (isset($_SESSION['message']) && !empty($_SESSION['message'])) :
        foreach ($_SESSION['message'] as $message) :
    ?>
            <p><?= $message ?></p>
    <?php
        endforeach;
        unset($_SESSION['message']);
    endif;
    ?>

    <?php foreach ($annonces as $annonce) : ?>
        <h1><a href="annonces/details.php?id=<?= $annonce['id'] ?>"><?= $annonce['title'] ?></a></h1>

        <?php if (!is_null($annonce['featured_image'])) :
            // On fabrique le nom de l'image
            $nomImage = pathinfo($annonce['featured_image'], PATHINFO_FILENAME);
            $extension = pathinfo($annonce['featured_image'], PATHINFO_EXTENSION);
            $miniature = "$nomImage-300x300.$extension";
        ?>
            <p><img src="<?= URL . '/uploads/' . $miniature ?>" alt="<?= $annonce['title'] ?>"></p>
        <?php endif; ?>

        <p>Publié par <?= $annonce['users_name'] ?> dans la catégorie <?= $annonce['categories_name'] ?> le <?= $annonce['created_at'] ?> pour le département<?=$annonce['content']?></p>
        <p><?= extrait($annonce['content'], 300) ?></p>
    <?php endforeach; ?>
</body>

</html>