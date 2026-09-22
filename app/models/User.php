<?php

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Registrar nuevo usuario
    public function register($data) {
        $sql = "INSERT INTO usuarios (rol_id, nombre, apellido, email, password) VALUES (:rol_id, :nombre, :apellido, :email, :password)";
        $stmt = $this->db->prepare($sql);

        // Asignamos por defecto rol_id = 2 (Cliente)
        $stmt->bindValue(':rol_id', 2, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $data['nombre'], PDO::PARAM_STR);
        $stmt->bindValue(':apellido', $data['apellido'] ?? '', PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        
        // Encriptación de contraseña segura con BCrypt
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);

        return $stmt->execute();
    }

    // Buscar usuario por Email para Login
    public function findUserByEmail($email) {
        $sql = "SELECT u.*, r.nombre AS rol_nombre FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE u.email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }
}