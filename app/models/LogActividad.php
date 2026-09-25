<?php
namespace App\Models;

use App\Core\Model;

class LogActividad extends Model {
    
    protected string $table = 'logs_actividad';
    
    /**
     * Obtiene los últimos movimientos (para dashboard)
     */
    public function getUltimos(int $limit = 10): array {
        $sql = "SELECT l.*, u.nombres, u.ap_paterno, u.email
                FROM logs_actividad l
                LEFT JOIN usuarios u ON u.id = l.usuario_id
                ORDER BY l.created_at DESC, l.id DESC
                LIMIT " . (int) $limit;
        return $this->db->select($sql);
    }
    
    /**
     * Obtiene el historial de cambios de una entidad (para vista de edición)
     */
    public function getHistorial(string $entidad, int $entidadId, int $limit = 10): array {
        $sql = "SELECT l.*, u.nombres, u.ap_paterno
                FROM logs_actividad l
                LEFT JOIN usuarios u ON u.id = l.usuario_id
                WHERE l.entidad = ? AND l.entidad_id = ?
                ORDER BY l.created_at DESC, l.id DESC
                LIMIT " . (int) $limit;
        return $this->db->select($sql, [$entidad, $entidadId]);
    }
    
    /**
     * Obtiene el conteo de acciones por tipo
     */
    public function countByAccion(): array {
        return $this->db->select(
            "SELECT accion, COUNT(*) as total FROM logs_actividad GROUP BY accion ORDER BY total DESC"
        );
    }
}