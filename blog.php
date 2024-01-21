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
        // Vérification du jeton CSRF
        if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            // Vérifier si l'utilisateur est autorisé à ajouter un article
            if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
                $newArticle = new Article(htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8'), htmlspecialchars($_POST['content'], ENT_QUOTES, 'UTF-8'));
                $database->insertArticle($newArticle);
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                echo "Vous n'avez pas les autorisations pour ajouter un article.";
            }
        } else {
            echo "Erreur CSRF : Jeton CSRF invalide.";
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
        $updatedTitle = htmlspecialchars($_POST['update_title'], ENT_QUOTES, 'UTF-8');
        $updatedContent = htmlspecialchars($_POST['update_content'], ENT_QUOTES, 'UTF-8');
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
        echo "<h2 class='article-title-custom'>" . html_entity_decode(htmlspecialchars($article->getTitle(), ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8') . "</h2>";
        echo "<p class='article-content-custom'>" . html_entity_decode(htmlspecialchars($article->getContent(), ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p class='article-date-custom'>Date de publication: " . htmlspecialchars($article->getDatePublished(), ENT_QUOTES, 'UTF-8') . "</p>";

        // Ajouter un formulaire de suppression pour chaque article
        if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
            echo "<form method='post' action=''>
            <input type='hidden' name='id' value='{$id}'>
            <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . "'>
            <button type='submit' name='delete_article' class='delete-button-custom'>Supprimer</button>
        </form>";

            // Formulaire de modification
            echo "<form method='post' action=''>
            <input type='hidden' name='update_id' value='{$id}'>
            <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . "'>
            <label for='update_title' class='form-label-custom'>Nouveau titre:</label>
            <input type='text' id='update_title' name='update_title' required class='form-input-custom'>
            <label for='update_content' class='form-label-custom'>Nouveau contenu:</label>
            <textarea id='update_content' name='update_content' required class='form-textarea-custom'></textarea>
            <input type='submit' value='Modifier' class='form-submit-custom'>
        </form>";
        }

        echo "</div>";
    }

    // Formulaire pour ajouter de nouveaux articles
    if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
        $csrfToken = bin2hex(random_bytes(32)); // Génération du jeton CSRF
        $_SESSION['csrf_token'] = $csrfToken; // Stockage du jeton dans la session

        echo "<form class='custom-form' method='post' action=''>
            <input type='hidden' name='csrf_token' value='" . htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') . "'>
            <label for='title' class='form-label-custom'>Titre:</label>
            <input type='text' id='title' name='title' class='form-input-custom' required><br>

            <label for='content' class='form-label-custom'>Contenu:</label>
            <textarea id='content' name='content' class='form-textarea-custom' required></textarea><br>

            <input type='submit' value='Ajouter' class='form-submit-custom'>
        </form>";
    }
    ?>
</body>

</html>