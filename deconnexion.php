<?php
require_once 'inc/header.php';

unset($_SESSION['user']);

// Effacer un cookie
setcookie('remember', '', 1);


if(isset($_SERVER['HTTP_REFERER'])){
    header('Location: '.$_SERVER['HTTP_REFERER']);
}else{
    header('Location: '.URL);
}