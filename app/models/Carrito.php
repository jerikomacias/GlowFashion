<?php
// Carrito guardado en la base de datos para usuarios con sesión iniciada.
// Así el carrito sobrevive al cerrar sesión y está disponible en cualquier dispositivo.
class Carrito {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Devuelve [producto_id => cantidad]
    public function obtener(int $usuarioId): array {
        $stmt = $this->db->prepare(
            "SELECT producto_id, cantidad FROM carrito_items WHERE usuario_id = :u ORDER BY updated_at, producto_id"
        );
        $stmt->execute([':u' => $usuarioId]);
        $resultado = [];
        foreach ($stmt->fetchAll() as $fila) {
            $resultado[(int)$fila['producto_id']] = (int)$fila['cantidad'];
        }
        return $resultado;
    }

    // Reemplaza todo el carrito del usuario (en una transacción)
    public function guardar(int $usuarioId, array $cantidades): void {
        $this->db->beginTransaction();
        try {
            $del = $this->db->prepare("DELETE FROM carrito_items WHERE usuario_id = :u");
            $del->execute([':u' => $usuarioId]);

            $ins = $this->db->prepare(
                "INSERT INTO carrito_items (usuario_id, producto_id, cantidad) VALUES (:u, :p, :c)"
            );
            foreach ($cantidades as $productoId => $cantidad) {
                $ins->execute([':u' => $usuarioId, ':p' => (int)$productoId, ':c' => (int)$cantidad]);
            }
            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Total de unidades en el carrito (para el contador del menú)
    public function contar(int $usuarioId): int {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(cantidad), 0) FROM carrito_items WHERE usuario_id = :u");
        $stmt->execute([':u' => $usuarioId]);
        return (int)$stmt->fetchColumn();
    }
}
