<?php
namespace App\Models;

use App\Core\Model;

class Usuario extends Model {
    
    protected string $table = 'usuarios';
    
    /**
     * Obtiene la lista de usuarios (para gestión en admin)
     */
    public function getAllConDetalles(): array {
        $sql = "SELECT u.*, 
                       (SELECT COUNT(*) FROM logs_actividad l WHERE l.usuario_id = u.id) as total_acciones
                FROM usuarios u
                ORDER BY u.created_at DESC";
        return $this->db->select($sql);
    }
    
    /**
     * Verifica si un email ya existe (excluyendo un id opcional)
     */
    public function emailExists(string $email, int $excludeId = 0): bool {
        $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ? AND id != ?";
        return $this->db->count($sql, [$email, $excludeId]) > 0;
    }
    
    /**
     * Bloquea o desbloquea un usuario
     */
    public function setActivo(int $id, int $activo): int {
        return $this->db->execute("UPDATE usuarios SET activo = ? WHERE id = ?", [$activo, $id]);
    }
    
    /**
     * Actualiza rol de un usuario
     */
    public function setRol(int $id, string $rol): int {
        return $this->db->execute("UPDATE usuarios SET rol = ? WHERE id = ?", [$rol, $id]);
    }
}