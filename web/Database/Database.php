<?php
namespace /Database;

class Database
{
    protected mysqli $connection;

    function __construct(
        string $host,
        string $user,
        string $pass,
        string $database = ""
    ) {
        $this->connection = new mysqli($host, $user, $pass, $database);
        if ($this->connection->connect_error) {
            throw new die(
                'mysqli connection error: '
                . $this->mysqli->connect_error
            );
        }
    }

    function __destruct()
    {
        $this->connection->close();
    }

    private function query(Query $query, array $params = null): bool
    {
        $conn = $this->connection;
        $stmt = $conn->prepare($query->getQuery());
        if ($params != null && count($params) > 0)
        {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, $params);
        }

        return $stmt->execute();
    }

    public function delete(
        string $table,
        string $requirement,
        array $params
    ): bool
    {
        $query = new Query();
        $query->delete()->from($table)->where($requirement);
        return $this->query($query, $params);
    }

    public function insert(string $table, array $columns, array $params): int
    {
        $query = new Query();
        $query->insert($table)->columns($columns)->values($params);
        $stmt = $conn->prepare($query->getQuery());
        if ($params != null && count($params) > 0)
        {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, $params);
        }

        $stmt->execute();
        return $stmt->insert_id;
    }

    public function update(
        string $table,
        array $columns,
        string $requirement,
        array $params
    ): bool
    {
        $query = new Query();
        $query->update($table)->set($columns)->where($requirement);

        return $this->query($query, $params);
    }

    public function select(
        string $table,
        array $columns,
        string $requirement = "",
        array $params = null
    ): mysqli_result
    {
        $conn = $this->connection;
        $query = new Query();
        $query->select($columns)->from($table)->where($requirement);
        $stmt = $conn($query->getQuery());

        if ($params != null && count($params) > 0) {
            $types = str_repeat("s", count($params));
            $stmt->bind_param($types, $params);
        }

        if (!$stmt->execute()) {
            return null;
        }

        return $stmt->get_result();
    }

    public function selectALL(string $table): mysqli_result
    {
        $conn = $this->connection;
        $stmt = $conn("SELECT * FROM " . $table);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function count(string $table): int {
        $conn = $this->connection;
        $stmt = $conn("SELECT COUNT(*) FROM " . $table);
        $stmt->execute();
        return $stmt->get_result()->fetch_row()[0];
    }

    public function change_db($database) {
        return $this->mysqli->select_db($database);
    }

    public function db_die() {
        die ($this->mysqli->error);
    }
}
?>