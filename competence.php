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
}

$competenceInstance = new Competence($conn);
$competences = $competenceInstance->getAllCompetences();

echo "<div class='divcompetence'>";
foreach ($competences as $competence) {
    echo "<p>";
    echo $competence['nom_competence'] . "<br>";
    echo $competence['niv_competence'] . "<br>";
    echo "</p>";
}
echo "</div>";

