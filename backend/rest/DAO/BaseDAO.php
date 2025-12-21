<?php

class BaseDAO {
    protected $connection;
    protected $tableName;
    protected $primaryKey;

    public function __construct($tableName, $primaryKey = null) {
        $this->tableName = $tableName;
    
        // Ensure primaryKey is set, or try to detect it
        if ($primaryKey === null) {
            $this->primaryKey = $this->detectPrimaryKey();
        } else {
            $this->primaryKey = $primaryKey;
        }
    
        try {
            $dsn = "mysql:host=localhost;dbname=todomasterdb;charset=utf8mb4";
            $username = "root";
            $password = "";
            $this->connection = new PDO($dsn, $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }


    private function detectPrimaryKey() {
        $stmt = $this->connection->prepare("SHOW KEYS FROM $this->tableName WHERE Key_name = 'PRIMARY'");
        $stmt->execute();
        $primaryKeyData = $stmt->fetch(PDO::FETCH_ASSOC);
        return $primaryKeyData ? $primaryKeyData['Column_name'] : 'id'; // Default to 'id' if no primary key found
    }


    public function getAll() {
        $stmt = $this->connection->prepare("SELECT * FROM $this->tableName");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getById($id) {
        $stmt = $this->connection->prepare("SELECT * FROM $this->tableName WHERE $this->primaryKey = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($data) {
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $stmt = $this->connection->prepare("INSERT INTO $this->tableName ($columns) VALUES ($values)");
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $this->connection->lastInsertId();
    }

  
    public function update($id, $data) {
        // Ensure $data is not empty
        if (empty($data)) {
            echo "No data provided to update<br>";
            return false;
        }
    
        // Dynamically generate the SET clause for SQL
        $fields = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));
        
        // Check the generated SQL for debugging purposes
        $sql = "UPDATE $this->tableName SET $fields WHERE $this->primaryKey = :id";
        echo "SQL Query: $sql<br>";
        
        $stmt = $this->connection->prepare($sql);
    
        // Bind the values for fields
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        // Bind the primary key value
        $stmt->bindValue(":id", $id);
        
        // Execute the query and return the result
        if ($stmt->execute()) {
            echo "Update successful<br>";
            return true;
        } else {
            echo "Update failed<br>";
            return false;
        }
    }

    public function delete($id) {
        $stmt = $this->connection->prepare("DELETE FROM $this->tableName WHERE $this->primaryKey = :id");
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

   
    public function countAll() {
        $stmt = $this->connection->prepare("SELECT COUNT(*) FROM $this->tableName");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    protected function fetchAll($query) {
        $stmt = $this->connection->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>