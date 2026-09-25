<?php
namespace App\Models;

use App\Core\Model;

class Noticia extends Model
{
    protected string $table = 'noticias';

    public function getUltimas(int $limit = 5): array
    {
        $limit = max(1, $limit);
        return $this->db->select(
            "SELECT * FROM noticias
             WHERE estado = 'publicado' AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()
             ORDER BY fecha_publicacion DESC, id DESC
             LIMIT {$limit}"
        );
    }

    public function paginatePublic(int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $total = $this->db->count(
            "SELECT COUNT(*) FROM noticias WHERE estado = 'publicado' AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()"
        );
        $offset = ($page - 1) * $perPage;
        $items = $this->db->select(
            "SELECT * FROM noticias
             WHERE estado = 'publicado' AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()
             ORDER BY fecha_publicacion DESC, id DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ];
    }

    public function paginateAdmin(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $where = [];
        $params = [];
        if (!empty($filters['busqueda'])) {
            $where[] = '(titulo LIKE ? OR slug LIKE ?)';
            $params[] = '%' . $filters['busqueda'] . '%';
            $params[] = '%' . $filters['busqueda'] . '%';
        }
        if (!empty($filters['usuario_id'])) {
            $where[] = 'n.usuario_id = ?';
            $params[] = (int) $filters['usuario_id'];
        }
        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = $this->db->count("SELECT COUNT(*) FROM noticias n {$whereClause}", $params);
        $offset = ($page - 1) * $perPage;
        $items = $this->db->select(
            "SELECT n.*, u.nombres AS usuario_nombres, u.ap_paterno AS usuario_ap
             FROM noticias n
             LEFT JOIN usuarios u ON u.id = n.usuario_id
             {$whereClause}
             ORDER BY n.fecha_publicacion DESC, n.id DESC
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
