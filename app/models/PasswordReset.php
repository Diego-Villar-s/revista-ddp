<?php
namespace App\Models;

use App\Core\Model;

class PasswordReset extends Model {
    
    protected string $table = 'password_resets';
    
    /**
     * Crea un token de recuperación de contraseña
     */
    public function createForUser(int $usuarioId): array {
        $token = bin2hex(random_bytes(32));
        $expiraEn = date('Y-m-d H:i:s', time() + PASSWORD_RESET_EXPIRY_HOURS * 3600);
        
        $this->db->insert(
            "INSERT INTO password_resets (usuario_id, token, expira_en) VALUES (?, ?, ?)",
            [$usuarioId, $token, $expiraEn]
        );
        
        return ['token' => $token, 'expira_en' => $expiraEn];
    }
    
    /**
     * Busca un token válido y no usado
     */
    public function findValidToken(string $token): ?array {
        $sql = "SELECT pr.*, u.email, u.id as usuario_id 
                FROM password_resets pr
                JOIN usuarios u ON u.id = pr.usuario_id
                WHERE pr.token = ? AND pr.usado = 0 AND pr.expira_en > NOW()";
        return $this->db->selectOne($sql, [$token]);
    }
    
    /**
     * Marca un token como usado
     */
    public function markAsUsed(int $id): int {
        return $this->db->execute("UPDATE password_resets SET usado = 1 WHERE id = ?", [$id]);
    }
    
    /**
     * Invalida todos los tokens activos de un usuario
     */
    public function invalidateForUser(int $usuarioId): int {
        return $this->db->execute(
            "UPDATE password_resets SET usado = 1 WHERE usuario_id = ? AND usado = 0",
            [$usuarioId]
        );
    }
}