<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Rolland Dylan</title>
    <link rel="stylesheet" href="./style/style.css">
</head>

<body>
<nav id="desktop-nav">
        <div class="logo">Dylan Rolland</div>
        <div>
            <ul class="nav-links">
                <?php
                // Vérifie si l'utilisateur est admin
                if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
                    echo '<li><a href="admin.php">Admin</a></li>';
                } else {
                    echo '<li><a href="index.php">Portfolio</a></li>';
                }
                ?>
                <li><a href="blog.php">Blog</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
    </nav>
    <section id=profile>
        <div class="profil">
            <div class="section-pic">
                <img class="imgpdp" src="./assets/photodeprofildylan.png" alt="Rolland Dylan Photo de profil">
            </div>
            <div class="section-text">
                <p class="txtp1">Bonjour c'est</p>
                <h1 class="title">Rolland Dylan</h1>
                <p class="txtp2">B2 Informatique</p>
                <div class="btn-container">
                    <button class="btn buttoncv" onclick="window.open('./assets/CV-RollandDylan.pdf')">Mon CV</button>
                </div>
                <div id="socials-container">
                    <img src="./assets/linkedin.png" alt="Mon compte Linkedin" class="icon"
                        onclick="location.href='https://www.linkedin.com/in/dylan-rolland-115871293/'" />
                    <img src="./assets/github.png" alt="Mon Github" class="icon"
                        onclick="location.href='https://github.com/DydyRld'" />
                </div>
            </div>
        </div>
        </div>
    </section>
    <section id="competences">
        <br>
        <hr>
        <div class="txt-competence">
            <h1>Compétences</h1>
        </div>
        <div class="container-competences">
            <?php include 'competence.php'; ?>
        </div>
    </section>
    <br>
    <hr>
    <div class="txt-competence">
        <h1>Projets</h1>
    </div>
    <?php
        include 'projet.php';
    
    ?>
</body>

</html>