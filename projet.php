<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "portfoliophp";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    exit;
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
            $sql = "SELECT * FROM projet";
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $projet = array(
                        'id_projet' => $row['id_projet'],
                        'titre' => $row['titre'],
                        'description' => $row['description'],
                        'image' => $row['image'], 
                    );
                    $projets[] = $projet;
                }
            } catch (PDOException $e) {
                echo "Erreur d'exécution de la requête SQL : " . $e->getMessage();
                exit;
            }
        }

        return $projets;
    }
}

$projetInstance = new Projet($conn);
$projets = $projetInstance->getAllProjets();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>

<div class="container-projets">
    <?php foreach ($projets as $projet) : ?>
        <div class="projet-card">
            <div class="projet-header">
                <h2 class="projet-titre"><?= $projet['titre']; ?></h2>
            </div>
            <div class="projet-body">
                <img class="projet-image" src="<?= $projet['image']; ?>" alt="Image du projet">
                <p class="projet-description"><?= $projet['description']; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>