<?php
namespace App\Models;

use App\Core\Model;

/**
 * Páginas institucionales editables (Sobre D&D, Alianzas y Contacto).
 */
class Pagina extends Model
{
    protected string $table = 'paginas';

    public function findPublicBySlug(string $slug): ?array
    {
        return $this->db->selectOne(
            'SELECT * FROM paginas WHERE slug = ? AND activo = 1',
            [$slug]
        );
    }

    public function getActive(): array
    {
        return $this->db->select('SELECT * FROM paginas WHERE activo = 1 ORDER BY titulo ASC');
    }
}
