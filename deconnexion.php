<?php
session_start();

if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

header("Location: index.php");
exit();
//Page qui permet de déconnecter l'utilisateur ou admin