<?php
class Category {
    private $conn;
    private $table = "categories";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllMainCategories() {
        $query = "SELECT * FROM " . $this->table . " WHERE parent_id IS NULL ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSubcategories($parent_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE parent_id = :parent_id ORDER BY display_order";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":parent_id", $parent_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategoryTree() {
        $categories = $this->getAllMainCategories();
        foreach ($categories as &$category) {
            $category['subcategories'] = $this->getSubcategories($category['id']);
        }
        return $categories;
    }

    public function getCategoryById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>