<?php
namespace App\Models;

use App\Core\Model;

/**
 * Valores editoriales y de presentación administrables desde la base de datos.
 */
class Configuracion extends Model
{
    protected string $table = 'configuracion';
    protected string $primaryKey = 'clave';

    /** @return array<string, string> */
    public function allKeyValue(): array
    {
        $rows = $this->db->select('SELECT clave, valor FROM configuracion');
        $values = [];
        foreach ($rows as $row) {
            $values[(string) $row['clave']] = (string) $row['valor'];
        }
        return $values;
    }

    public function get(string $key, string $default = ''): string
    {
        $row = $this->findBy('clave', $key);
        return $row ? (string) $row['valor'] : $default;
    }

    public function save(string $key, string $value, string $type = 'text'): void
    {
        $this->db->execute(
            'INSERT INTO configuracion (clave, valor, tipo) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor), tipo = VALUES(tipo), updated_at = CURRENT_TIMESTAMP',
            [$key, $value, $type]
        );
    }
}
