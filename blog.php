<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio Rolland Dylan</title>
    <link rel="stylesheet" href="./style/style.css">
</head>

<body>
    <?php
    include_once 'navbar.php';

    require_once 'article.php';
    require_once 'database.php';

    $database = new Database('localhost', 'root', '', 'portfoliophp');

    // Traitement du formulaire d'ajout d'articles
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title']) && !empty($_POST['content'])) {
        // Vérifier si l'utilisateur est autorisé à ajouter un article
        if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
            $newArticle = new Article($_POST['title'], $_POST['content']);
            $database->insertArticle($newArticle);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "Vous n'avez pas les autorisations pour ajouter un article.";
        }
    }

    // Traitement du formulaire de suppression
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $articleIdToDelete = $_POST['id'];
        $success = $database->deleteArticle($articleIdToDelete);

        // Rafraîchir la page pour refléter la suppression
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Traitement du formulaire de modification
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_title']) && isset($_POST['update_content'])) {
        $articleIdToUpdate = $_POST['update_id'];
        $updatedTitle = $_POST['update_title'];
        $updatedContent = $_POST['update_content'];
        $database->updateArticle($articleIdToUpdate, $updatedTitle, $updatedContent);

        // Rafraîchir la page pour refléter la modification
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Afficher les articles existants
    $articlesFromDB = $database->getArticlesFromDatabase();

    foreach ($articlesFromDB as $article) {
        $id = $article->getId();
        echo "<div class='article-container'>";
        echo "<h2 class='article-title'>{$article->getTitle()}</h2>";
        echo "<p class='article-content'>{$article->getContent()}</p>";
        echo "<p class='article-date'>Date de publication: {$article->getDatePublished()}</p>";

        // Ajouter un formulaire de suppression pour chaque article
        if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
            echo "<form method='post' action=''>
            <input type='hidden' name='id' value='{$id}'>
            <button type='submit' name='delete_article' class='delete-button'>Supprimer</button>
        </form>";

            // Formulaire de modification
            echo "<form method='post' action=''>
            <input type='hidden' name='update_id' value='{$id}'>
            <label for='update_title'>Nouveau titre:</label>
            <input type='text' id='update_title' name='update_title' required>
            <label for='update_content'>Nouveau contenu:</label>
            <textarea id='update_content' name='update_content' required></textarea>
            <input type='submit' value='Modifier'>
        </form>";
        }

        echo "</div>";
    }

    // Formulaire pour ajouter de nouveaux articles
    if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
        echo "<form class='custom-form' method='post' action=''>
            <label for='title' class='form-label'>Titre:</label>
            <input type='text' id='title' name='title' class='form-input' required><br>

            <label for='content' class='form-label'>Contenu:</label>
            <textarea id='content' name='content' class='form-textarea' required></textarea><br>

            <input type='submit' value='Ajouter' class='form-submit'>
        </form>";
    }
    ?>
</body>

</html>
