<?php

//Classe database et ses fonctions
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
            error_log("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    //Fonction pour ajouter des blogs
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
            error_log("Erreur d'insertion de l'article : " . $e->getMessage());
            return false;
        }
    }
    //Fonction pour ajouter un message de contact

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
                error_log("Erreur lors de l'insertion du message : " . print_r($stmt->errorInfo(), true));
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion du message : " . $e->getMessage());
            return false;
        }
    }
    //Fonction pour faire apparaître les messages
    public function getMessages()
    {
        try {
            if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
                $resultat = $this->conn->query("SELECT contact.message, contact.date_mess, contact.mail as email, contact.id_mess
                                               FROM contact");

                if (!$resultat) {
                    error_log("Erreur dans la requête SQL : " . print_r($this->conn->errorInfo(), true));
                    return false;
                }

                return $resultat; 
            } else {
                return "";
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des messages : " . $e->getMessage());
            return false;
        }
    }
    //Fonction pour supprimer un blog
    public function deleteArticle($articleId)
    {
        try {
            $deleteQuery = "DELETE FROM articles WHERE id = ?";
            $stmt = $this->conn->prepare($deleteQuery);
            $stmt->bindParam(1, $articleId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur de suppression de l'article : " . $e->getMessage());
            return false;
        }
    }
    //Fonction pour modifier le blog
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
            error_log("Erreur lors de la mise à jour de l'article : " . $e->getMessage());
            return false;
        }
    }
    //Fonction pour supprimer le message
    public function deleteMessage($messageId)
    {
        try {
            $deleteQuery = "DELETE FROM contact WHERE id_mess = ?";
            $stmt = $this->conn->prepare($deleteQuery);
            $stmt->bindParam(1, $messageId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du message : " . $e->getMessage());
            return false;
        }
    }
    //Fonction pour faire apparaître les blogs de la BDD.

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
