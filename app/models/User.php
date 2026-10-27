<?php

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Registrar un nuevo cliente. Devuelve el id creado, o false si el correo ya existe.
    public function register($data) {
        if ($this->findUserByEmail($data['email'])) {
            return false;
        }

        $sql = "INSERT INTO usuarios (rol_id, nombre, apellido, email, password)
                VALUES (:rol_id, :nombre, :apellido, :email, :password)";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->execute([
                ':rol_id'   => 2, // 2 = Cliente
                ':nombre'   => $data['nombre'],
                ':apellido' => $data['apellido'] ?? '',
                ':email'    => $data['email'],
                ':password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ]);
        } catch (PDOException $e) {
            // 23000 = clave duplicada (otro registro con el mismo correo al mismo tiempo)
            if ($e->getCode() === '23000') {
                return false;
            }
            throw $e;
        }

        return (int)$this->db->lastInsertId();
    }

    // Buscar usuario por correo para el login
    public function findUserByEmail($email) {
        $sql = "SELECT u.*, r.nombre AS rol_nombre
                FROM usuarios u
                JOIN roles r ON u.rol_id = r.id
                WHERE u.email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    // Crea o repara la cuenta de administrador definida en config.php
    // (rol_id = 1 y contraseña con el hash configurado).
    public function asegurarAdmin($email, $hash) {
        $existente = $this->findUserByEmail($email);
        if ($existente) {
            $stmt = $this->db->prepare("UPDATE usuarios SET rol_id = 1, password = :p WHERE id = :id");
            $stmt->execute([':p' => $hash, ':id' => $existente['id']]);
        } else {
            $stmt = $this->db->prepare(
                "INSERT INTO usuarios (rol_id, nombre, apellido, email, password)
                 VALUES (1, 'Administrador', 'Glow Fashion', :email, :p)"
            );
            $stmt->execute([':email' => $email, ':p' => $hash]);
        }
    }
}
