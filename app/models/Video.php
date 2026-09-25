<?php
namespace App\Models;

use App\Core\Model;

class Video extends Model
{
    protected string $table = 'videos';

    public function getAllPublic(): array
    {
        return $this->db->select(
            "SELECT * FROM videos
             WHERE estado = 'publicado' AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()
             ORDER BY fecha_publicacion DESC, id DESC"
        );
    }

    public function getUltimos(int $limit = 3): array
    {
        $limit = max(1, $limit);
        return $this->db->select(
            "SELECT * FROM videos
             WHERE estado = 'publicado' AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()
             ORDER BY fecha_publicacion DESC, id DESC
             LIMIT {$limit}"
        );
    }

    public function findPublicBySlug(string $slug): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM videos
             WHERE slug = ? AND estado = 'publicado'
               AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()",
            [$slug]
        );
    }

    public function findPublicById(int $id): ?array
    {
        if ($id < 1) {
            return null;
        }
        return $this->db->selectOne(
            "SELECT * FROM videos
             WHERE id = ? AND estado = 'publicado'
               AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()",
            [$id]
        );
    }

    public function paginateAdmin(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $where = [];
        $params = [];
        if (!empty($filters['estado'])) {
            $where[] = 'estado = ?';
            $params[] = $filters['estado'];
        }
        if (!empty($filters['busqueda'])) {
            $where[] = '(titulo LIKE ? OR slug LIKE ?)';
            $params[] = '%' . $filters['busqueda'] . '%';
            $params[] = '%' . $filters['busqueda'] . '%';
        }
        if (!empty($filters['usuario_id'])) {
            $where[] = 'v.usuario_id = ?';
            $params[] = (int) $filters['usuario_id'];
        }
        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = $this->db->count("SELECT COUNT(*) FROM videos v {$whereClause}", $params);
        $offset = ($page - 1) * $perPage;
        $items = $this->db->select(
            "SELECT v.*, u.nombres AS usuario_nombres, u.ap_paterno AS usuario_ap
             FROM videos v
             LEFT JOIN usuarios u ON u.id = v.usuario_id
             {$whereClause}
             ORDER BY v.fecha_publicacion DESC, v.id DESC
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
