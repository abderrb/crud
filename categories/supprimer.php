<?php
require_once '../inc/header.php';

// On se connecte à la base de données
require_once '../inc/connect.php';

// On écrit la requête
$sql = "DELETE FROM `categories` WHERE `id` = :id";

// On prépare la requête
$query = $db->prepare($sql);

// On injecte les valeurs dans les paramètres
$query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);

// On exécute la requête
$query->execute();

// On redirige
header('Location: index.php');