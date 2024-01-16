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
    public function insertArticle(Article $article)
    {
        try {
            $title = $article->getTitle();
            $content = $article->getContent();

            $insertQuery = "INSERT INTO articles (title, content, date_published) VALUES (?, ?, NOW())";
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->bindParam(1, $title, PDO::PARAM_STR);
            $stmt->bindParam(2, $content, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            die("Erreur d'insertion de l'article : " . $e->getMessage());
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
    try {
        if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
            $resultat = $this->conn->query("SELECT contact.message, contact.date_mess, contact.mail as email, contact.id_mess
                                       FROM contact");

            // Ajoutez ces messages de débogage
            if (!$resultat) {
                die("Erreur dans la requête SQL : " . print_r($this->conn->errorInfo(), true));
            }

            return $resultat; // Retournez directement le résultat sans vérifier son type
        } else {
            return "";
        }
    } catch (PDOException $e) {
        die("Erreur lors de la récupération des messages : " . $e->getMessage());
    }
}
    public function deleteArticle($articleId)
    {
        try {
            $deleteQuery = "DELETE FROM articles WHERE id = ?";
            $stmt = $this->conn->prepare($deleteQuery);
            $stmt->bindParam(1, $articleId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            die("Erreur de suppression de l'article : " . $e->getMessage());
        }
    }
    public function updateArticle($articleId, $newTitle, $newContent)
    {
        try {
            $updateQuery = "UPDATE articles SET title = ?, content = ? WHERE id = ?";
            $stmt = $this->conn->prepare($updateQuery);
            $stmt->bindParam(1, $newTitle, PDO::PARAM_STR);
            $stmt->bindParam(2, $newContent, PDO::PARAM_STR);
            $stmt->bindParam(3, $articleId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            die("Erreur lors de la mise à jour de l'article : " . $e->getMessage());
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
