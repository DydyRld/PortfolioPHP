<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

//Classe compétence et fonctions
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
                'nom_competence' => htmlspecialchars($row['nom_competence'], ENT_QUOTES, 'UTF-8'),
                'niv_competence' => htmlspecialchars($row['niv_competence'], ENT_QUOTES, 'UTF-8'),
            );
            $competences[] = $competence;
        }

        return $competences;
    }
    
//Fonction ajout de compétences
    public function addCompetence($nom, $niveau)
    {
        $sql = "INSERT INTO competence (nom_competence, niv_competence) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([htmlspecialchars($nom, ENT_QUOTES, 'UTF-8'), htmlspecialchars($niveau, ENT_QUOTES, 'UTF-8')]);
    }
//Fonction modification de compétences

    public function editCompetence($nom, $niveau)
    {
        $sqlSelectId = "SELECT id_competence FROM competence WHERE LOWER(nom_competence) = LOWER(?)";
        $stmtSelectId = $this->conn->prepare($sqlSelectId);
        $stmtSelectId->execute([htmlspecialchars($nom, ENT_QUOTES, 'UTF-8')]);

        if ($stmtSelectId) {
            $row = $stmtSelectId->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $id_competence = $row['id_competence'];

                $sqlUpdate = "UPDATE competence SET niv_competence = ? WHERE id_competence = ?";
                $stmtUpdate = $this->conn->prepare($sqlUpdate);
                $result = $stmtUpdate->execute([htmlspecialchars($niveau, ENT_QUOTES, 'UTF-8'), $id_competence]);
            }
        }
    }
//Fonction suppression de compétences

    public function deleteCompetence($nom)
    {
        $sqlSelectId = "SELECT id_competence FROM competence WHERE LOWER(nom_competence) = LOWER(?)";
        $stmtSelectId = $this->conn->prepare($sqlSelectId);
        $stmtSelectId->execute([htmlspecialchars($nom, ENT_QUOTES, 'UTF-8')]);

        if ($stmtSelectId) {
            $row = $stmtSelectId->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $id_competence = $row['id_competence'];

                $sqlDelete = "DELETE FROM competence WHERE id_competence = ?";
                $stmtDelete = $this->conn->prepare($sqlDelete);
                $result = $stmtDelete->execute([$id_competence]);
            }
        }
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
            break;
    }
}

$competences = $competenceInstance->getAllCompetences();
?>
<!-- Affichage des compétences -->
<div class='divcompetence'>
    <?php foreach ($competences as $competence): ?>
        <p>
            <?php echo $competence['nom_competence'] . "<br>"; ?>
            <?php echo $competence['niv_competence'] . "<br>"; ?>
        </p>
    <?php endforeach; ?>
</div>
