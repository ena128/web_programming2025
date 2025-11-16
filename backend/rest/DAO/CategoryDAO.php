<?php
require_once __DIR__ . '/BaseDAO.php';

class CategoryDAO extends BaseDAO {
    public function __construct() {
        parent::__construct("categories", "category_id");
    }

    public function createCategory($name) {
        $query = "INSERT INTO categories (name) VALUES (:name)";
        $stmt = $this->connection->prepare($query);
        $stmt->execute(['name' => $name]);
        return $this->connection->lastInsertId();
    }

    public function getAllCategories() {
        return $this->fetchAll("SELECT * FROM categories");
    }

    public function getCategoryById($category_id) {
        return $this->getById($category_id);
    }

    public function updateCategory($category_id, $name) {
        $query = "UPDATE categories SET name = :name WHERE category_id = :category_id";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'category_id' => $category_id,
            'name' => $name
        ]);
        return $stmt->rowCount(); 
    }

    public function deleteCategory($category_id) {
        $stmt = $this->connection->prepare("DELETE FROM categories WHERE category_id = :category_id");
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        return $stmt->rowCount(); 
    }
}
?>
