<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "portfoliophp";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

if (!isset($_SESSION['user_email'])) {
    header('Location: connexion.php');
    exit;
}

$admin_email = 'admin@admin.fr';

if ($_SESSION['user_email'] !== $admin_email) {
    header('Location: erreur.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Portfolio Rolland Dylan</title>
    <link rel="stylesheet" href="./style/style.css">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
</head>

<body>
    <nav id="desktop-nav">
        <div class="logo">Dylan Rolland</div>
        <div>
            <ul class="nav-links">
                <?php
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

    <h1 class="h1admin">Tableau de bord Admin</h1>
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
    </section>
    <section id="competences">
        <br>
        <hr>
        <div class="txt-competence">
            <h1>Compétences</h1>
        </div>
        <div class="container-competences">
            <?php include 'competence.php'; ?>
            <form class="adminform" action="admin.php" method="post">
                <input class="admininput" type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input class="admininput" type="hidden" name="action" value="add">
                <label class="adminlabel" for="newCompetence">Nouvelle Compétence :</label>
                <input class="admininput" type="text" name="newCompetence" required>
                <label class="adminlabel" for="newNiveau">Niveau :</label>
                <input class="admininput" type="text" name="newNiveau" required>
                <button class="adminbutton" type="submit">Ajouter</button>
            </form>

            <form class="adminform" action="admin.php" method="post">
                <input class="admininput" type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input class="admininput" type="hidden" name="action" value="edit">
                <label class="adminlabel" for="editCompetence">Modifier Compétence :</label>
                <input class="admininput" type="text" name="editCompetence" required>
                <label class="adminlabel" for="editNiveau">Nouveau Niveau :</label>
                <input class="admininput" type="text" name="editNiveau" required>
                <button class="adminbutton" type="submit">Modifier</button>
            </form>

            <form class="adminform" action="admin.php" method="post">
                <input class="admininput" type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input class="admininput" type="hidden" name="action" value="delete">
                <label class="adminlabel" for="deleteCompetence">Supprimer Compétence :</label>
                <input class="admininput" type="text" name="deleteCompetence" required>
                <button class="adminbutton" type="submit">Supprimer</button>
            </form>
        </div>
    </section>
    <section id="projets" class="projects-section">
        <?php
        include 'projet.php';
        ?>

    </section>

    <br>
    <hr>

</body>

</html>