<?php
class Database
{
    private $conn;

    public function getConnection()
    {
        return $this->conn;
    }

    public function __construct($host, $username, $password, $database)
    {
        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public function insertMessage($userEmail, $userMessage)
    {
        try {
            $insertQuery = "INSERT INTO contact (mail, message, date_mess) VALUES (?, ?, NOW())";
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->bindParam(1, $userEmail, PDO::PARAM_STR);
            $stmt->bindParam(2, $userMessage, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return true;
            } else {
                // Afficher les informations sur l'erreur
                var_dump($stmt->errorInfo());
                return false;
            }
        } catch (PDOException $e) {
            die("Erreur d'insertion du message : " . $e->getMessage());
        }
    }

    public function getMessages()
{
    if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
        $resultat = $this->conn->query("SELECT contact.message, contact.date_mess, contact.mail as email, contact.id_mess
                                       FROM contact");

        $messagesHTML = "";

        while ($row = $resultat->fetch(PDO::FETCH_ASSOC)) {
            $messagesHTML .= "<div class='message-container'>";
            $messagesHTML .= "<p class='user-email'><strong></strong> " . $row["email"] . "</p>";
            $messagesHTML .= "<p class='user-message'><strong></strong> " . $row["message"] . "</p>";
            $messagesHTML .= "<p class='message-date'><strong>Publié le</strong> " . $row["date_mess"] . "</p>";

            // Afficher le bouton "Supprimer" uniquement pour l'administrateur
            $messagesHTML .= "<form method='post' action=''>
                                <input type='hidden' name='messageIdToDelete' value='{$row["id_mess"]}'>
                                <button type='submit'>Supprimer</button>
                              </form>";

            $messagesHTML .= "</div>";
            $messagesHTML .= "<hr class='message-divider'>";
        }

        return $messagesHTML;
    } else {
        return ""; 
    }
}

    public function deleteMessage($messageId)
    {
        try {
            $deleteQuery = "DELETE FROM contact WHERE id_mess = ?";
            $stmt = $this->conn->prepare($deleteQuery);
            $stmt->bindParam(1, $messageId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            die("Erreur lors de la suppression du message : " . $e->getMessage());
        }
    }

    public function getArticlesFromDatabase()
    {
        $articles = [];
        $result = $this->conn->query("SELECT * FROM articles");

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $article = new Article($row['title'], $row['content']);
            $article->setId($row['id']);
            $article->setDatePublished($row['date_published']);
            $articles[] = $article;
        }

        return $articles;
    }

    public function closeConnection()
    {
        $this->conn = null;
    }
}
