<?php
// Démarrage de la session

session_start();

// Génération d'un jeton CSRF ou récupération du jeton existant depuis la session

$csrfToken = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;
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
    // Inclusion de la barre de navigation
    
    include_once 'navbar.php';
    // Inclusion des classes nécessaires
    
    require_once 'article.php';
    require_once 'database.php';
    // Connexion à la base de données

    $database = new Database('localhost', 'root', '', 'portfoliophp');
        
    // Traitement du formulaire d'ajout d'article

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['title']) && !empty($_POST['content'])) {
        if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
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
        // Traitement du formulaire de suppression d'article


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $articleIdToDelete = $_POST['id'];
            $success = $database->deleteArticle($articleIdToDelete);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "Erreur CSRF : Jeton CSRF invalide pour la suppression.";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_title']) && isset($_POST['update_content'])) {
        if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $articleIdToUpdate = $_POST['update_id'];
            $updatedTitle = htmlspecialchars($_POST['update_title'], ENT_QUOTES, 'UTF-8');
            $updatedContent = htmlspecialchars($_POST['update_content'], ENT_QUOTES, 'UTF-8');
            $database->updateArticle($articleIdToUpdate, $updatedTitle, $updatedContent);
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "Erreur CSRF : Jeton CSRF invalide pour la modification.";
        }
    }

    $articlesFromDB = $database->getArticlesFromDatabase();

        // Affichage des articles


    foreach ($articlesFromDB as $article) {
        $id = $article->getId();
        echo "<div class='article-container'>";
        echo "<h2 class='article-title-custom'>" . html_entity_decode(htmlspecialchars($article->getTitle(), ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8') . "</h2>";
        echo "<p class='article-content-custom'>" . html_entity_decode(htmlspecialchars($article->getContent(), ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8') . "</p>";
        echo "<p class='article-date-custom'>Date de publication: " . htmlspecialchars($article->getDatePublished(), ENT_QUOTES, 'UTF-8') . "</p>";

        if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {
            echo "<form method='post' action=''>
                <input type='hidden' name='id' value='{$id}'>
                <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . "'>
                <button type='submit' name='delete_article' class='delete-button-custom'>Supprimer</button>
            </form>";

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
        // Formulaire d'ajout d'article pour l'administrateur


    if (isset($_SESSION['user_email']) && $_SESSION['user_email'] === 'admin@admin.fr') {

        echo "<form class='custom-form' method='post' action=''>
        <p class='ajtblogp'>Ajouter un nouveau blog</p>
            <input type='hidden' name='csrf_token' value='" . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . "'>
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