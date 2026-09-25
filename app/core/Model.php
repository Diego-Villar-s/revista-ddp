<?php
namespace App\Core;

/**
 * Base de modelos. Las subclases definen $table y, opcionalmente, $primaryKey.
 */
class Model
{
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    public function findBy(string $column, $value): ?array
    {
        return $this->db->selectOne(
            "SELECT * FROM {$this->table} WHERE {$column} = ?",
            [$value]
        );
    }

    public function all(string $orderBy = 'id DESC'): array
    {
        return $this->db->select("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
    }

    public function paginate(array $filters = [], int $page = 1, int $perPage = ITEMS_PER_PAGE, string $orderBy = 'id DESC'): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $where = [];
        $params = [];

        foreach ($filters as $column => $value) {
            if ($value !== '' && $value !== null) {
                $where[] = "{$column} = ?";
                $params[] = $value;
            }
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $total = $this->db->count("SELECT COUNT(*) FROM {$this->table} {$whereClause}", $params);
        $offset = ($page - 1) * $perPage;
        $items = $this->db->select(
            "SELECT * FROM {$this->table} {$whereClause} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}",
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

    public function create(array $data): int
    {
        if (!$data) {
            throw new \InvalidArgumentException('No se pueden crear registros sin datos.');
        }
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = 'INSERT INTO ' . $this->table
            . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
        return $this->db->insert($sql, array_values($data));
    }

    public function update(int $id, array $data): int
    {
        if (!$data) {
            return 0;
        }
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = ?";
        }
        $params = array_values($data);
        $params[] = $id;
        $sql = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $set)
            . " WHERE {$this->primaryKey} = ?";
        return $this->db->execute($sql, $params);
    }

    public function delete(int $id): int
    {
        return $this->db->execute("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?", [$id]);
    }

    public function countWhere(string $where = '1=1', array $params = []): int
    {
        return $this->db->count("SELECT COUNT(*) FROM {$this->table} WHERE {$where}", $params);
    }
}
