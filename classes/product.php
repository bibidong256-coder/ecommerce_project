<?php
class Product {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function addProduct($data) {
        try {
            $this->conn->beginTransaction();
            
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = $this->createSlug($data['name']);
            }
            
            // Insert product
            $query = "INSERT INTO products (name, slug, brand, description, price, stock, image, category_id) 
                      VALUES (:name, :slug, :brand, :description, :price, :stock, :image, :category_id)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':name' => $data['name'],
                ':slug' => $data['slug'],
                ':brand' => $data['brand'] ?? null,
                ':description' => $data['description'] ?? null,
                ':price' => $data['price'],
                ':stock' => $data['stock'] ?? 0,
                ':image' => $data['image'] ?? null,
                ':category_id' => !empty($data['subcategory_id']) ? $data['subcategory_id'] : $data['category_id']
            ]);
            
            $product_id = $this->conn->lastInsertId();
            
            // Assign to product_categories (main category + subcategory if selected)
            if (!empty($data['subcategory_id'])) {
                $this->assignCategory($product_id, $data['subcategory_id']);
                $this->assignCategory($product_id, $data['category_id']); // Also assign to parent
            } else {
                $this->assignCategory($product_id, $data['category_id']);
            }
            
            // Set page visibility
            $this->setPageVisibility($product_id, $data);
            
            // Clean up old columns (optional - for backward compatibility)
            $this->updateOldColumns($product_id, $data);
            
            $this->conn->commit();
            return ['success' => true, 'product_id' => $product_id];
            
        } catch(Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function assignCategory($product_id, $category_id) {
        $query = "INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (:product_id, :category_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':product_id' => $product_id,
            ':category_id' => $category_id
        ]);
    }
    
    private function setPageVisibility($product_id, $data) {
        $query = "INSERT INTO product_page_visibility (product_id, show_on_shop_page_1, show_on_shop_page_2) 
                  VALUES (:product_id, :shop1, :shop2)
                  ON DUPLICATE KEY UPDATE 
                  show_on_shop_page_1 = :shop1, show_on_shop_page_2 = :shop2";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':product_id' => $product_id,
            ':shop1' => isset($data['show_shop_page_1']) ? 1 : 0,
            ':shop2' => isset($data['show_shop_page_2']) ? 1 : 0
        ]);
    }
    
    private function updateOldColumns($product_id, $data) {
        // Only for backward compatibility during migration
        $category_name = $this->getCategoryNameById($data['category_id']);
        $subcategory_name = !empty($data['subcategory_id']) ? $this->getCategoryNameById($data['subcategory_id']) : null;
        
        $query = "UPDATE products SET category = :category, subcategory = :subcategory WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':category' => $category_name,
            ':subcategory' => $subcategory_name ?? 'all',
            ':id' => $product_id
        ]);
    }
    
    private function getCategoryNameById($id) {
        $query = "SELECT slug FROM categories WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['slug'] : null;
    }
    
    private function createSlug($string) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
        return $slug . '-' . uniqid();
    }
    
    // Fetch products for Shop Page
    public function getShopProducts($page_num = 1, $per_page = 12) {
        $offset = ($page_num - 1) * $per_page;
        $shop_page_field = "show_on_shop_page_" . $page_num;
        
        $query = "SELECT p.*, 
                         GROUP_CONCAT(DISTINCT c.slug) as category_slugs
                  FROM products p
                  JOIN product_page_visibility ppv ON p.id = ppv.product_id
                  LEFT JOIN product_categories pc ON p.id = pc.product_id
                  LEFT JOIN categories c ON pc.category_id = c.id
                  WHERE p.status = 'active' AND ppv.{$shop_page_field} = 1
                  GROUP BY p.id
                  ORDER BY p.created_at DESC
                  LIMIT :offset, :per_page";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Fetch products for category page (includes subcategories automatically)
    public function getProductsByCategorySlug($slug, $page = 1, $per_page = 12) {
        $offset = ($page - 1) * $per_page;
        
        $query = "SELECT DISTINCT p.* 
                  FROM products p
                  JOIN product_categories pc ON p.id = pc.product_id
                  JOIN categories c ON pc.category_id = c.id
                  WHERE (c.slug = :slug OR c.parent_id = (SELECT id FROM categories WHERE slug = :slug))
                  AND p.status = 'active'
                  ORDER BY p.created_at DESC
                  LIMIT :offset, :per_page";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>