<?php
namespace App\Models;

use App\Core\Model;

class Reportaje extends Model
{
    protected string $table = 'reportajes';

    public function findPublicBySlug(string $slug): ?array
    {
        return $this->db->selectOne(
            "SELECT r.*, a.nombres, a.ap_paterno, a.ap_materno, a.nickname, a.es_nickname,
                    (SELECT COUNT(*) FROM reportajes_fotos rf WHERE rf.reportaje_id = r.id) AS total_fotos
             FROM reportajes r
             LEFT JOIN autores a ON a.id = r.autor_id
             WHERE r.slug = ? AND r.estado = 'publicado' AND r.fecha_publicacion <= CURDATE()",
            [$slug]
        );
    }

    public function getDestacadoPrincipal(): ?array
    {
        $row = $this->db->selectOne(
            "SELECT r.*, a.nombres, a.ap_paterno, a.ap_materno, a.nickname, a.es_nickname
             FROM reportajes r
             LEFT JOIN autores a ON a.id = r.autor_id
             WHERE r.estado = 'publicado' AND r.es_destacado = 1 AND r.fecha_publicacion <= CURDATE()
             ORDER BY r.fecha_publicacion DESC, r.id DESC LIMIT 1"
        );
        if ($row) {
            return $row;
        }
        return $this->db->selectOne(
            "SELECT r.*, a.nombres, a.ap_paterno, a.ap_materno, a.nickname, a.es_nickname
             FROM reportajes r
             LEFT JOIN autores a ON a.id = r.autor_id
             WHERE r.estado = 'publicado' AND r.fecha_publicacion <= CURDATE()
             ORDER BY r.fecha_publicacion DESC, r.id DESC LIMIT 1"
        );
    }

    public function getDestacados(int $limit = 4): array
    {
        $limit = max(1, $limit);
        return $this->db->select(
            "SELECT r.*, a.nombres, a.ap_paterno, a.ap_materno, a.nickname, a.es_nickname
             FROM reportajes r
             LEFT JOIN autores a ON a.id = r.autor_id
             WHERE r.estado = 'publicado' AND r.es_destacado = 1 AND r.fecha_publicacion <= CURDATE()
             ORDER BY r.fecha_publicacion DESC, r.id DESC
             LIMIT {$limit}"
        );
    }

    public function getPublicadosPaginated(int $page = 1, int $perPage = REPORT_ITEMS_PER_PAGE): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $total = $this->db->count(
            "SELECT COUNT(*) FROM reportajes WHERE estado = 'publicado' AND fecha_publicacion <= CURDATE()"
        );
        $offset = ($page - 1) * $perPage;
        $items = $this->db->select(
            "SELECT r.*, a.nombres, a.ap_paterno, a.ap_materno, a.nickname, a.es_nickname
             FROM reportajes r
             LEFT JOIN autores a ON a.id = r.autor_id
             WHERE r.estado = 'publicado' AND r.fecha_publicacion <= CURDATE()
             ORDER BY r.fecha_publicacion DESC, r.id DESC
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

    public function getUltimos(int $limit = 3, ?int $excludeId = null): array
    {
        $limit = max(1, $limit);
        $exclude = $excludeId ? ' AND r.id != ' . (int) $excludeId : '';
        return $this->db->select(
            "SELECT r.*, a.nombres, a.ap_paterno, a.ap_materno, a.nickname, a.es_nickname
             FROM reportajes r
             LEFT JOIN autores a ON a.id = r.autor_id
             WHERE r.estado = 'publicado' AND r.fecha_publicacion <= CURDATE(){$exclude}
             ORDER BY r.fecha_publicacion DESC, r.id DESC
             LIMIT {$limit}"
        );
    }

    public function getFotos(int $reportajeId): array
    {
        return $this->db->select(
            'SELECT * FROM reportajes_fotos WHERE reportaje_id = ? ORDER BY orden ASC, id ASC',
            [$reportajeId]
        );
    }

    public function addFoto(int $reportajeId, array $rutas, int $orden, string $descripcion): int
    {
        return $this->db->insert(
            'INSERT INTO reportajes_fotos (reportaje_id, url_foto, url_foto_thumb, url_foto_webp, orden, descripcion)
             VALUES (?, ?, ?, ?, ?, ?)',
            [
                $reportajeId,
                $rutas['ruta_original'] ?? '',
                $rutas['ruta_thumb'] ?? '',
                $rutas['ruta_webp'] ?? '',
                $orden,
                $descripcion,
            ]
        );
    }

    public function deleteFoto(int $fotoId): int
    {
        return $this->db->execute('DELETE FROM reportajes_fotos WHERE id = ?', [$fotoId]);
    }

    public function updateFotoOrder(int $fotoId, int $orden): int
    {
        return $this->db->execute('UPDATE reportajes_fotos SET orden = ? WHERE id = ?', [$orden, $fotoId]);
    }

    public function updateFoto(int $fotoId, array $data): int
    {
        if (!$data) {
            return 0;
        }
        $set = [];
        $params = [];
        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
            $params[] = $value;
        }
        $params[] = $fotoId;
        return $this->db->execute(
            'UPDATE reportajes_fotos SET ' . implode(', ', $set) . ' WHERE id = ?',
            $params
        );
    }

    public function getArchivos(): array
    {
        return $this->db->select(
            "SELECT DATE_FORMAT(fecha_publicacion, '%Y-%m') AS periodo,
                    DATE_FORMAT(fecha_publicacion, '%m/%Y') AS etiqueta,
                    COUNT(*) AS cantidad
             FROM reportajes
             WHERE estado = 'publicado' AND fecha_publicacion <= CURDATE()
             GROUP BY periodo, etiqueta
             ORDER BY periodo DESC"
        );
    }

    public function getByPeriodo(string $periodo): array
    {
        return $this->db->select(
            "SELECT r.*, a.nombres, a.ap_paterno, a.ap_materno, a.nickname, a.es_nickname
             FROM reportajes r
             LEFT JOIN autores a ON a.id = r.autor_id
             WHERE r.estado = 'publicado'
               AND r.fecha_publicacion <= CURDATE()
               AND DATE_FORMAT(r.fecha_publicacion, '%Y-%m') = ?
             ORDER BY r.fecha_publicacion DESC, r.id DESC",
            [$periodo]
        );
    }

    public function paginateAdmin(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $where = [];
        $params = [];

        if (!empty($filters['estado'])) {
            $where[] = 'r.estado = ?';
            $params[] = $filters['estado'];
        }
        if (!empty($filters['autor_id'])) {
            $where[] = 'r.autor_id = ?';
            $params[] = (int) $filters['autor_id'];
        }
        if (isset($filters['destacado']) && $filters['destacado'] !== '') {
            $where[] = 'r.es_destacado = ?';
            $params[] = (int) $filters['destacado'];
        }
        if (!empty($filters['desde'])) {
            $where[] = 'r.fecha_publicacion >= ?';
            $params[] = $filters['desde'];
        }
        if (!empty($filters['hasta'])) {
            $where[] = 'r.fecha_publicacion <= ?';
            $params[] = $filters['hasta'];
        }
        if (!empty($filters['busqueda'])) {
            $where[] = '(r.titulo LIKE ? OR r.slug LIKE ?)';
            $params[] = '%' . $filters['busqueda'] . '%';
            $params[] = '%' . $filters['busqueda'] . '%';
        }
        if (!empty($filters['usuario_id'])) {
            $where[] = 'r.usuario_id = ?';
            $params[] = (int) $filters['usuario_id'];
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = $this->db->count("SELECT COUNT(*) FROM reportajes r {$whereClause}", $params);
        $offset = ($page - 1) * $perPage;
        $items = $this->db->select(
            "SELECT r.*, a.nombres AS autor_nombres, a.ap_paterno AS autor_ap, a.nickname AS autor_nick,
                    u.nombres AS usr_nombres, u.ap_paterno AS usr_ap
             FROM reportajes r
             LEFT JOIN autores a ON a.id = r.autor_id
             LEFT JOIN usuarios u ON u.id = r.usuario_id
             {$whereClause}
             ORDER BY r.fecha_publicacion DESC, r.id DESC
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

    public function countByEstado(): array
    {
        return $this->db->select('SELECT estado, COUNT(*) AS total FROM reportajes GROUP BY estado');
    }

    public function findWithRelations(int $id): ?array
    {
        return $this->db->selectOne(
            "SELECT r.*, a.nombres AS autor_nombres, a.ap_paterno AS autor_ap, a.nickname AS autor_nick
             FROM reportajes r
             LEFT JOIN autores a ON a.id = r.autor_id
             WHERE r.id = ?",
            [$id]
        );
    }
}
