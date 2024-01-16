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
        $stmt->execute([$hashedPassword, $email]);  // Passer les paramètres directement dans execute
        $stmt->closeCursor();
    }
    public function registerUser($email, $password) {
        $existingUser = $this->getUserByEmail($email);

        if ($existingUser) {
            return "L'utilisateur avec cet email existe déjà.";
        }

        // Hachez le mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $conn = $this->db->getConnection();
        $query = "INSERT INTO user (email, password) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $email, $hashedPassword);

        if ($stmt->execute()) {
            $stmt->close();
            return true; 
        } else {
            return "Erreur lors de l'inscription. Veuillez réessayer.";
        }
    }
}
