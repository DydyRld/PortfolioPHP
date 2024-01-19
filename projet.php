<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "portfoliophp";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

class Projet
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllProjets()
    {
        $projets = array();
        $checkTableQuery = "SHOW TABLES LIKE 'projet'";
        $tableExists = $this->conn->query($checkTableQuery);

        if ($tableExists->rowCount() > 0) {
            $sql = "SELECT projet.id_projet, projet.titre, projet.description, imgprojet.img
                FROM projet INNER JOIN imgprojet ON projet.id_img = imgprojet.id_img";
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $projet = array(
                        'id_projet' => $row['id_projet'],
                        'titre' => $row['titre'],
                        'description' => $row['description'],
                        'image' => $row['img'],
                    );
                    $projets[] = $projet;
                }
            } catch (PDOException $e) {
                die("Erreur d'exécution de la requête SQL : " . $e->getMessage());
            }
        }

        return $projets;
    }
    public function getImageId($nomImage)
    {
        $sql = "SELECT id_img FROM imgprojet WHERE nom_image = :nomImage";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':nomImage', $nomImage);
            $stmt->execute();
            $idImage = $stmt->fetchColumn();
            return $idImage;
        } catch (PDOException $e) {
            die("Erreur d'exécution de la requête SQL : " . $e->getMessage());
        }
    }

    public function getAvailableImages()
    {
        $images = array();

        $sql = "SELECT DISTINCT nom_image FROM imgprojet";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $images[] = $row['nom_image'];
            }
        } catch (PDOException $e) {
            die("Erreur d'exécution de la requête SQL : " . $e->getMessage());
        }

        return $images;
    }

    public function addProjet($titre, $description, $nomImage)
    {
        try {

            $stmtImg = $this->conn->prepare("SELECT id_img FROM imgprojet WHERE nom_image = :nomImage");
            $stmtImg->bindParam(':nomImage', $nomImage);
            $stmtImg->execute();
            $idImage = $stmtImg->fetchColumn();
            $stmt = $this->conn->prepare("INSERT INTO projet (titre, description, id_img) VALUES (:titre, :description, :idImage)");
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':idImage', $idImage);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Erreur d'exécution de la requête SQL : " . $e->getMessage());
        }
    }

    public function editProjet($id_projet, $titre, $description)
    {
        try {
            $stmt = $this->conn->prepare("UPDATE projet SET titre = :titre, description = :description WHERE id_projet = :id_projet");
            $stmt->bindParam(':id_projet', $id_projet);
            $stmt->bindParam(':titre', $titre);
            $stmt->bindParam(':description', $description);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Erreur d'exécution de la requête SQL : " . $e->getMessage());
        }
    }

    public function deleteProjet($id_projet)
    {
        try {
            $stmt = $this->conn->prepare("DELETE FROM projet WHERE id_projet = :id_projet");
            $stmt->bindParam(':id_projet', $id_projet);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Erreur d'exécution de la requête SQL : " . $e->getMessage());
        }
    }
}

$projetInstance = new Projet($conn);
$projets = $projetInstance->getAllProjets();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'addProject':
                if (isset($_POST['newProjectTitle']) && isset($_POST['newProjectDescription']) && isset($_POST['newProjectImage'])) {
                    $newProjectTitle = $_POST['newProjectTitle'];
                    $newProjectDescription = $_POST['newProjectDescription'];
                    $newProjectImage = $_POST['newProjectImage'];

                    $projetInstance->addProjet($newProjectTitle, $newProjectDescription, $newProjectImage);
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit();
                }
                break;

            case 'editProject':
                if (isset($_POST['editProjectId']) && isset($_POST['editProjectTitle']) && isset($_POST['editProjectDescription'])) {
                    $editProjectId = $_POST['editProjectId'];
                    $editProjectTitle = $_POST['editProjectTitle'];
                    $editProjectDescription = $_POST['editProjectDescription'];
                    $editProjectImage = $_POST['editProjectImage'];
                    $idImage = $projetInstance->getImageId($editProjectImage);
                    $stmtUpdateProject = $conn->prepare("UPDATE projet SET titre = :titre, description = :description, id_img = :idImage WHERE id_projet = :id_projet");
                    $stmtUpdateProject->bindParam(':id_projet', $editProjectId);
                    $stmtUpdateProject->bindParam(':titre', $editProjectTitle);
                    $stmtUpdateProject->bindParam(':description', $editProjectDescription);
                    $stmtUpdateProject->bindParam(':idImage', $idImage);
                    $stmtUpdateProject->execute();

                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit();
                }
                break;

            case 'deleteProject':
                if (isset($_POST['deleteProjectId'])) {
                    $deleteProjectId = $_POST['deleteProjectId'];
                    $projetInstance->deleteProjet($deleteProjectId);
                    header("Location: {$_SERVER['PHP_SELF']}");
                    exit();
                }
                break;
        }
    }
}

function uploadImage($file)
{
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($file['name']);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (isset($_POST["submit"])) {
        $check = getimagesize($file['tmp_name']);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            die("Le fichier n'est pas une image.");
        }
    }

    if (file_exists($target_file)) {
        die("Désolé, le fichier existe déjà.");
    }

    if ($file['size'] > 500000) {
        die("Désolé, le fichier est trop volumineux.");
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        die("Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.");
    }

    if ($uploadOk == 0) {
        die("Désolé, votre fichier n'a pas été téléchargé.");
    } else {
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return $target_file;
        } else {
            die("Une erreur s'est produite lors du téléchargement de votre fichier.");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <hr>
    <div class="txt-competence">
        <h1>Projets</h1>
    </div>
    <div class="container-projets">
        <?php foreach ($projets as $projet): ?>
            <div class="projet-card">
                <div class="projet-header">
                    <h2 class="projet-titre">
                        <?= $projet['titre']; ?>
                    </h2>
                </div>
                <div class="projet-body">
                    <img class="projet-image" src="<?= $projet['image']; ?>" alt="Image du projet">
                    <p class="projet-description">
                        <?= $projet['description']; ?>
                    </p>
                    <form method="post" action="">
                        <input type="hidden" name="deleteProjectId" value="<?= $projet['id_projet']; ?>">
                        <button type="submit" name="action" value="deleteProject">Supprimer</button>
                    </form>
                    <form method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="editProjectId" value="<?= $projet['id_projet']; ?>">
                        <label for="editProjectTitle">Nouveau Titre:</label>
                        <input type="text" name="editProjectTitle" value="<?= $projet['titre']; ?>" required>
                        <label for="editProjectDescription">Nouvelle Description:</label>
                        <textarea name="editProjectDescription" required><?= $projet['description']; ?></textarea>

                        <!-- Ajoutez cette partie pour la modification de l'image -->
                        <label for="editProjectImage">Nouvelle Image:</label>
                        <select name="editProjectImage">
                            <?php
                            $availableImages = $projetInstance->getAvailableImages();
                            foreach ($availableImages as $image) {
                                $selected = ($image == $projet['image']) ? "selected" : "";
                                echo "<option value='$image' $selected>$image</option>";
                            }
                            ?>
                        </select>

                        <button type="submit" name="action" value="editProject">Éditer</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <form method="post" action="" enctype="multipart/form-data">
        <label for="newProjectTitle">Titre:</label>
        <input type="text" name="newProjectTitle" required>
        <label for="newProjectDescription">Description:</label>
        <textarea name="newProjectDescription" required></textarea>
        <label for="newProjectImage">Image:</label>
        <select name="newProjectImage">
            <?php
            $availableImages = $projetInstance->getAvailableImages();
            foreach ($availableImages as $image) {
                echo "<option value='$image'>$image</option>";
            }
            ?>
        </select>
        <button type="submit" name="action" value="addProject">Ajouter Projet</button>
    </form>
</body>

</html>