<?php

class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserByEmail($email) {
        $conn = $this->db->getConnection();

        $query = "SELECT * FROM user WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$email]);  
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $user;
    }

    private function updatePasswordByEmail($email, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $conn = $this->db->getConnection();

        $query = "UPDATE user SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$hashedPassword, $email]);
        $stmt->closeCursor();
    }

    public function displayUserDetails($email) {
        $user = $this->getUserByEmail($email);

        if ($user) {
            $escapedUsername = htmlspecialchars($user['username']);
            $escapedEmail = htmlspecialchars($user['email']);

            echo "Username: $escapedUsername <br>";
            echo "Email: $escapedEmail <br>";
        } else {
            echo "User not found";
        }
    }

    public function generateCsrfToken() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }

    public function isCsrfTokenValid($token) {
        return isset($_SESSION['csrf_token']) && $token === $_SESSION['csrf_token'];
    }

    public function updatePasswordSafely($email, $password, $csrfToken) {
        if ($this->isCsrfTokenValid($csrfToken)) {
            $this->updatePasswordByEmail($email, $password);
        } else {
            echo "CSRF token is invalid!";
        }
    }
}
