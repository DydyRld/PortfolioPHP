<?php
session_start(); // Démarre la session

require_once 'database.php';
require_once 'user.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = new Database("localhost", "root", "", "portfoliophp");
    $user = new User($db);

    $user_data = $user->getUserByEmail($email);

    if ($user_data && password_verify($password, $user_data['password'])) {
        $_SESSION['user_email'] = $email;
        $_SESSION['id_user'] = $user_data['id_user']; // Ajout de l'ID de l'utilisateur dans la session

        if ($email === 'admin@admin.fr') {
            // Si l'utilisateur est l'administrateur, redirige vers admin.php
            header("Location: admin.php");
            exit();
        } else {
            $success_message = "Identifiants corrects";
            header("Location: index.php");
            exit();
        }
    } else {
        $error_message = "Identifiants incorrects";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de connexion</title>
    <link rel="stylesheet" href="style/connexion.css">
</head>

<body>
    <?php
    include_once 'navbar.php'
    ?>
    <form action="" method="post">
        <h2>Connexion</h2>

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

        <input type="submit" value="Se connecter">
    </form>

</body>

</html>