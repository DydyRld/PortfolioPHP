<?php
//Démarrage de la session
session_start();

require_once 'database.php';
require_once 'user.php';

// Générer le token CSRF
$csrfToken = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrfToken;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $db = new Database("localhost", "root", "", "portfoliophp");
        $user = new User($db);

        $user_data = $user->getUserByEmail($email);

        if ($user_data && password_verify($password, $user_data['password'])) {
            session_regenerate_id();

            $_SESSION['user_email'] = $email;
            $_SESSION['id_user'] = $user_data['id_user'];

            if ($email === 'admin@admin.fr') {
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
    } else {
        $error_message = "Erreur CSRF : Jeton CSRF invalide.";
    }
}
?>
<!-- Page de connexion -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de connexion</title>
    <link rel="stylesheet" href="style/connexion.css">
</head>

<body>
    <?php include_once 'navbar.php'; ?>
    
    <form action="" method="post">
        <h2>Connexion</h2>

        <?php
        if (isset($error_message)) {
            echo '<p class="error">Erreur lors de la connexion.</p>';
        } elseif (isset($success_message)) {
            echo '<p class="success">' . $success_message . '</p>';
        }
        ?>

        <label for="email">Email :</label>
        <input type="email" name="email" required><br>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" required><br>

        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

        <input type="submit" value="Se connecter">
    </form>

</body>

</html>
