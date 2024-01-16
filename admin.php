<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: connexion.php');
    exit;
}

$admin_email = 'admin@admin.fr';

if ($_SESSION['user_email'] !== $admin_email) {
    header('Location: erreur.php');
    exit;
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'add':
                if (isset($_POST['newCompetence']) && isset($_POST['newNiveau'])) {
                    $newCompetence = $_POST['newCompetence'];
                    $newNiveau = $_POST['newNiveau'];

                    $stmt = $conn->prepare("INSERT INTO competence (nom_competence, niv_competence) VALUES (:nom, :niveau)");
                    $stmt->bindParam(':nom', $newCompetence);
                    $stmt->bindParam(':niveau', $newNiveau);
                    $stmt->execute();
                }
                break;

            case 'edit':
                if (isset($_POST['editCompetence']) && isset($_POST['editNiveau'])) {
                    $editedCompetence = $_POST['editCompetence'];
                    $editedNiveau = $_POST['editNiveau'];

                    $stmt = $conn->prepare("UPDATE competence SET niv_competence = :niveau WHERE nom_competence = :nom");
                    $stmt->bindParam(':niveau', $editedNiveau);
                    $stmt->bindParam(':nom', $editedCompetence);
                    $stmt->execute();
                }
                break;

            case 'delete':
                if (isset($_POST['deleteCompetence'])) {
                    $deletedCompetence = $_POST['deleteCompetence'];

                    $stmt = $conn->prepare("DELETE FROM competence WHERE nom_competence = :nom");
                    $stmt->bindParam(':nom', $deletedCompetence);
                    $stmt->execute();
                }
                break;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Portfolio Rolland Dylan</title>
    <link rel="stylesheet" href="./style/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
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
                    <img src="./assets/instagram.png" alt="Mon instagram" class="icon"
                        onclick="location.href='https://www.instagram.com/dylxnrld/?next=%2F'" />
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
                <input class="admininput" type="hidden" name="action" value="add">
                <label class="adminlabel" for="newCompetence">Nouvelle Compétence :</label>
                <input class="admininput" type="text" name="newCompetence" required>
                <label class="adminlabel" ²for="newNiveau">Niveau :</label>
                <input class="admininput" type="text" name="newNiveau" required>
                <button class="adminbutton" type="submit">Ajouter</button>
            </form>

            <form class="adminform" action="admin.php" method="post">
                <input class="admininput" type="hidden" name="action" value="edit">
                <label class="adminlabel" for="editCompetence">Modifier Compétence :</label>
                <input class="admininput" type="text" name="editCompetence" required>
                <label class="adminlabel" for="editNiveau">Nouveau Niveau :</label>
                <input class="admininput" type="text" name="editNiveau" required>
                <button class="adminbutton" type="submit">Modifier</button>
            </form>

            <form class="adminform" action="admin.php" method="post">
                <input class="admininput" type="hidden" name="action" value="delete">
                <label class="adminlabel" for="deleteCompetence">Supprimer Compétence :</label>
                <input class="admininput" type="text" name="deleteCompetence" required>
                <button class="adminbutton" type="submit">Supprimer</button>
            </form>
        </div>
        
    </section>
    <br>
        <hr>
        <div class="txt-competence">
            <h1>Projets</h1>
        </div>
</body>

</html>