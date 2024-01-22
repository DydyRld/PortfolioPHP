<?php
session_start();

require_once 'database.php';

class Contact
{
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function insertMessage($userEmail, $userMessage)
    {
        return $this->database->insertMessage($userEmail, $userMessage);
    }

    public function getMessagesHTML()
    {
        $resultat = $this->database->getMessages();

        if ($resultat === "") {
            return '';
        }

        $messagesHTML = "";

        while ($row = $resultat->fetch(PDO::FETCH_ASSOC)) {
            $messagesHTML .= "<div class='message-container'>";
            $messagesHTML .= "<p class='user-email'><strong>Email:</strong> " . htmlspecialchars($row["email"]) . "</p>";
            $messagesHTML .= "<p class='user-message'><strong>Message:</strong> " . htmlspecialchars($row["message"]) . "</p>";
            $messagesHTML .= "<p class='message-date'><strong>Publié le</strong> " . htmlspecialchars($row["date_mess"]) . "</p>";

            if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
                $messagesHTML .= "<form method='post' action=''>
                                <input type='hidden' name='messageIdToDelete' value='" . htmlspecialchars($row["id_mess"]) . "'>
                                <button type='submit' class='delete-button'>Supprimer</button>
                              </form>";
            }

            $messagesHTML .= "</div>";
            $messagesHTML .= "<hr class='message-divider'>";
        }

        return $messagesHTML;
    }

    public function deleteMessage($messageId)
    {
        return $this->database->deleteMessage($messageId);
    }
}

$database = new Database("localhost", "root", "", "portfoliophp");
$contact = new Contact($database);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userMessage'])) {
    $userMessage = $_POST['userMessage'];

    if (isset($_SESSION['user_email'])) {
        $insertResult = $contact->insertMessage($_SESSION['user_email'], $userMessage);
    } else {
        if (isset($_POST['userEmail'])) {
            $userEmail = $_POST['userEmail'];

            // Validation de l'adresse e-mail côté serveur
            if (filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                $insertResult = $contact->insertMessage($userEmail, $userMessage);
            } else {
                echo '<p style="color: red;">Veuillez fournir une adresse e-mail valide.</p>';
                exit;
            }
        } else {
            echo '<p style="color: red;">Veuillez fournir une adresse e-mail pour envoyer un message.</p>';
            exit;
        }
    }

    if ($insertResult) {
        echo '<div style="color: green;">Votre message a été envoyé avec succès.</div>';
    } else {
        echo '<p style="color: red;">Erreur lors de l\'insertion du message.</p>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['messageIdToDelete']) && isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
    $messageIdToDelete = $_POST['messageIdToDelete'];
    $success = $contact->deleteMessage($messageIdToDelete);

    if ($success) {
        echo '<p style="color: green;">Message supprimé avec succès.</p>';
    } else {
        echo '<p style="color: red;">Erreur lors de la suppression du message.</p>';
    }
}

$messagesHTML = $contact->getMessagesHTML();
$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="./style/style.css">
</head>

<body>

    <?php include_once 'navbar.php'; ?>

    <div class="containercontact">
        <h1>Contactez-moi</h1>

        <?php
        echo '<form method="post" action="" class="contact-form">
            <label for="userEmail">Votre adresse e-mail :</label>
            <input type="email" id="userEmail" name="userEmail" required>
            <label for="userMessage">Votre message :</label>
            <textarea id="userMessage" name="userMessage" rows="4" cols="50" required></textarea>
            <button type="submit" class="submit-button">Envoyer</button>
            <input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . '">
        </form>';
        ?>

        <div id="messagesContainer">
            <?php
            if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
                echo '<p class="admin-info">En tant qu\'administrateur, vous pouvez supprimer les messages.</p>';
            }
            echo $messagesHTML;
            ?>
        </div>
    </div>

</body>

</html>
