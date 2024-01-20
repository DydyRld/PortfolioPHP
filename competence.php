<?php

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

class Competence
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllCompetences()
    {
        $competences = array();

        $sql = "SELECT competence.*, niv_competence.num_competence
            FROM competence
            INNER JOIN niv_competence ON competence.niv_competence = niv_competence.id_niveau";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $competence = array(
                'id_competence' => $row['id_competence'],
                'nom_competence' => $row['nom_competence'],
                'niv_competence' => $row['niv_competence'],
            );
            $competences[] = $competence;
        }

        return $competences;
    }

    public function addCompetence($nom, $niveau)
    {
        $sql = "INSERT INTO competence (nom_competence, niv_competence) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$nom, $niveau]);
    }

    public function editCompetence($nom, $niveau)
{
    // Recherche de l'ID de la compétence en fonction du nom
    $sqlSelectId = "SELECT id_competence FROM competence WHERE nom_competence = ?";
    $stmtSelectId = $this->conn->prepare($sqlSelectId);
    $stmtSelectId->execute([$nom]);

    $row = $stmtSelectId->fetch(PDO::FETCH_ASSOC);
    $id_competence = $row['id_competence'];

    // Mise à jour de la compétence en utilisant l'ID
    $sqlUpdate = "UPDATE competence SET niv_competence = ? WHERE id_competence = ?";
    $stmtUpdate = $this->conn->prepare($sqlUpdate);
    $stmtUpdate->execute([$niveau, $id_competence]);
}
public function deleteCompetence($nom)
{
    // Recherche de l'ID de la compétence en fonction du nom
    $sqlSelectId = "SELECT id_competence FROM competence WHERE nom_competence = ?";
    $stmtSelectId = $this->conn->prepare($sqlSelectId);
    $stmtSelectId->execute([$nom]);

    $row = $stmtSelectId->fetch(PDO::FETCH_ASSOC);
    $id_competence = $row['id_competence'];

    // Suppression de la compétence en utilisant l'ID
    $sqlDelete = "DELETE FROM competence WHERE id_competence = ?";
    $stmtDelete = $this->conn->prepare($sqlDelete);
    $stmtDelete->execute([$id_competence]);
}
}

$competenceInstance = new Competence($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    switch ($action) {
        case 'add':
            $newCompetence = $_POST['newCompetence'];
            $newNiveau = $_POST['newNiveau'];
            $competenceInstance->addCompetence($newCompetence, $newNiveau);
            break;

        case 'edit':
            $editCompetence = $_POST['editCompetence'];
            $editNiveau = $_POST['editNiveau'];
            $competenceInstance->editCompetence($editCompetence, $editNiveau);
            break;

        case 'delete':
            $deleteCompetence = $_POST['deleteCompetence'];
            $competenceInstance->deleteCompetence($deleteCompetence);
            break;

        default:
            // Gérez les autres actions ou actions invalides
            break;
    }
}

$competences = $competenceInstance->getAllCompetences();

echo "<div class='divcompetence'>";
foreach ($competences as $competence) {
    echo "<p>";
    echo $competence['nom_competence'] . "<br>";
    echo $competence['niv_competence'] . "<br>";
    echo "</p>";
}
echo "</div>";
