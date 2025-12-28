<?php

class BaseDAO {
    protected $connection;
    protected $tableName;
    protected $primaryKey;

    public function __construct($tableName, $primaryKey = null) {
        $this->tableName = $tableName;

        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                Config::DB_HOST(),
                Config::DB_PORT(),
                Config::DB_NAME()
            );

            $this->connection = new PDO(
                $dsn,
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_SSL_MODE => PDO::MYSQL_ATTR_SSL_MODE_REQUIRED
                ]
            );

        } catch (PDOException $e) {
            die("Database connection failed");
        }

        // Primary key
        if ($primaryKey === null) {
            $this->primaryKey = $this->detectPrimaryKey();
        } else {
            $this->primaryKey = $primaryKey;
        }
    }

    private function detectPrimaryKey() {
        $stmt = $this->connection->prepare(
            "SHOW KEYS FROM {$this->tableName} WHERE Key_name = 'PRIMARY'"
        );
        $stmt->execute();
        $pk = $stmt->fetch();
        return $pk ? $pk['Column_name'] : 'id';
    }

    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM {$this->tableName}");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->connection->prepare(
            "SELECT * FROM {$this->tableName} WHERE {$this->primaryKey} = :id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $columns = implode(", ", array_keys($data));
        $params  = ":" . implode(", :", array_keys($data));

        $stmt = $this->connection->prepare(
            "INSERT INTO {$this->tableName} ($columns) VALUES ($params)"
        );

        $stmt->execute($data);
        return $this->connection->lastInsertId();
    }

    public function update($id, $data) {
        if (empty($data)) return false;

        $fields = implode(
            ", ",
            array_map(fn($k) => "$k = :$k", array_keys($data))
        );

        $data['id'] = $id;

        $stmt = $this->connection->prepare(
            "UPDATE {$this->tableName} SET $fields WHERE {$this->primaryKey} = :id"
        );

        return $stmt->execute($data);
    }

    public function delete($id) {
        $stmt = $this->connection->prepare(
            "DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = :id"
        );
        return $stmt->execute(['id' => $id]);
    }

    public function countAll() {
        $stmt = $this->connection->prepare(
            "SELECT COUNT(*) FROM {$this->tableName}"
        );
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
