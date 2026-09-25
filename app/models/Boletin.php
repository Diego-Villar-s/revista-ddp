<?php
namespace App\Models;

use App\Core\Model;

class Boletin extends Model
{
    protected string $table = 'boletines';

    public function getUltimo(): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM boletines
             WHERE estado = 'publicado' AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()
             ORDER BY fecha_publicacion DESC, id DESC LIMIT 1"
        );
    }

    public function getByNumero(string $numero): ?array
    {
        return $this->db->selectOne(
            'SELECT * FROM boletines WHERE numero_boletin = ? AND estado = \'publicado\' AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()',
            [$numero]
        );
    }

    public function getAllPublic(): array
    {
        return $this->db->select(
            "SELECT * FROM boletines
             WHERE estado = 'publicado' AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()
             ORDER BY fecha_publicacion DESC, id DESC"
        );
    }

    public function paginatePublic(int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $total = $this->db->count(
            "SELECT COUNT(*) FROM boletines WHERE estado = 'publicado' AND fecha_publicacion IS NOT NULL AND fecha_publicacion <= CURDATE()"
        );
        $offset = ($page - 1) * $perPage;
        $items = $this->db->select(
            "SELECT * FROM boletines
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
            $where[] = '(numero_boletin LIKE ? OR resumen LIKE ?)';
            $params[] = '%' . $filters['busqueda'] . '%';
            $params[] = '%' . $filters['busqueda'] . '%';
        }
        if (!empty($filters['usuario_id'])) {
            $where[] = 'b.usuario_id = ?';
            $params[] = (int) $filters['usuario_id'];
        }
        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = $this->db->count("SELECT COUNT(*) FROM boletines b {$whereClause}", $params);
        $offset = ($page - 1) * $perPage;
        $items = $this->db->select(
            "SELECT b.*, u.nombres AS usuario_nombres, u.ap_paterno AS usuario_ap
             FROM boletines b
             LEFT JOIN usuarios u ON u.id = b.usuario_id
             {$whereClause}
             ORDER BY b.fecha_publicacion DESC, b.id DESC
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
