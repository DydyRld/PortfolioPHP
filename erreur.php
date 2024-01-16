<?php
// Déconnexion de l'utilisateur
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<style>
    *{
        background-color: black;
    }
    </style>
<body>
    <p>Vous n'avez pas l'autorisation d'accéder à cette page. Vous serez redirigé vers une vidéo YouTube.</p>

    <!-- Redirection automatique vers une vidéo YouTube après 5 secondes -->
    <meta http-equiv="refresh" content="0;url=https://www.youtube.com/watch?v=dQw4w9WgXcQ">

</body>

</html>