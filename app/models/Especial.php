<?php
namespace App\Models;

use App\Core\Model;

class Especial extends Model
{
    protected string $table = 'especiales';

    public function getActive(): array
    {
        return $this->db->select(
            "SELECT * FROM especiales
             WHERE activo = 1
             ORDER BY fecha_creacion DESC, id DESC"
        );
    }

    public function findPublicById(int $id): ?array
    {
        if ($id < 1) {
            return null;
        }
        return $this->db->selectOne(
            'SELECT * FROM especiales WHERE id = ? AND activo = 1',
            [$id]
        );
    }

    public function paginateAdmin(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $where = [];
        $params = [];

        if (isset($filters['activo']) && $filters['activo'] !== '') {
            $where[] = 'activo = ?';
            $params[] = (int) $filters['activo'];
        }
        if (!empty($filters['busqueda'])) {
            $where[] = '(titulo_completo LIKE ? OR titulo_leet LIKE ? OR palabra_resaltada LIKE ?)';
            $search = '%' . $filters['busqueda'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = $this->db->count("SELECT COUNT(*) FROM especiales {$whereClause}", $params);
        $offset = ($page - 1) * $perPage;
        $items = $this->db->select(
            "SELECT * FROM especiales {$whereClause}
             ORDER BY fecha_creacion DESC, id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }
}
