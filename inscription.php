<?php
session_start(); // Démarre la session

require_once 'database.php';
require_once 'user.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = new Database("localhost", "root", "", "portfoliophp");
    $user = new User($db);

    // Utilisez la méthode registerUser pour ajouter un nouvel utilisateur
    $registration_result = $user->registerUser($email, $password);

    if ($registration_result === true) {
        $_SESSION['user_email'] = $email;
        $success_message = "Inscription réussie. Bienvenue!";
        header("Location: connexion.php");
        exit();
    } else {
        $error_message = $registration_result;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'inscription</title>
    <link rel="stylesheet" href="style/connexion.css">
</head>

<body>
<?php
include_once 'navbar.php'
?>
    <form action="" method="post">
        <h2>Inscription</h2>

        <?php
        if (isset($error_message)) {
            echo '<p class="error">' . $error_message . '</p>';
        } elseif (isset($success_message)) {
            echo '<p class="success">' . $success_message . '</p>';
        }
        ?>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Mot de passe:</label>
        <input type="password" name="password" required><br>

        <input type="submit" value="S'inscrire">
    </form>

    <p>Vous avez déjà un compte ? <a href="connexion.php">Connectez-vous ici</a></p>
</body>

</html>