<?php
namespace App\Models;

use App\Core\Model;

class Autor extends Model {
    
    protected string $table = 'autores';
    
    /**
     * Obtiene el nombre completo de un autor
     */
    public function fullName(array $autor): string {
        if (!empty($autor['es_nickname']) && !empty($autor['nickname'])) {
            return $autor['nickname'];
        }
        
        return trim($autor['nombres'] . ' ' . $autor['ap_paterno'] . ' ' . $autor['ap_materno']);
    }
    
    /**
     * Lista de autores con el conteo de reportajes publicados
     */
    public function getAllWithCount(): array {
        $sql = "SELECT a.*, 
                       (SELECT COUNT(*) FROM reportajes r WHERE r.autor_id = a.id AND r.estado = 'publicado') as total_reportajes
                FROM autores a
                ORDER BY a.nombres ASC";
        return $this->db->select($sql);
    }
    
    /**
     * Opciones en formato clave-valor para select
     */
    public function getOptions(): array {
        $autores = $this->all('nombres ASC, ap_paterno ASC');
        $options = [];
        foreach ($autores as $a) {
            $options[$a['id']] = $this->fullName($a);
        }
        return $options;
    }
}