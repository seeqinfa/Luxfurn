<?php

require_once dirname(__DIR__, 2) . '/Entities/furniture.php';

class AdminManageProductCtrl {
    private $db;

    public function countFurniture($searchTerm = '') {
        return Furniture::count($searchTerm);
    }

    public function getFurniturePaginated($offset, $limit, $searchTerm = '') {
        return Furniture::findPaginated($offset, $limit, $searchTerm);
    }

    public function getFurnitureById    ($id) {
        $sql = "SELECT * FROM furnitures WHERE furnitureID = ?";
        $stmt = mysqli_prepare($this->db, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            return new Furniture(
                $row['furnitureID'],
                $row['name'],
                $row['category'],
                $row['description'],
                $row['price'],
                $row['stock_quantity'],
                $row['image_url']
            );
        }

        return null;
    }

    public function addProduct($name, $category, $price, $quantity, $description, $imagePath) {
        global $conn;
    
    // Verify the image path is not empty
        if (empty($imagePath)) {
            throw new Exception("Image path cannot be empty");
        }

        $sql = "INSERT INTO furnitures 
            (name, category, price, stock_quantity, description, image_url) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }
        $price = round((float)$price, 2);
        mysqli_stmt_bind_param($stmt, "ssdiss", 
            $name, $category, $price, $quantity, $description, $imagePath);
    
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    
        return $success;
    }
    public function editProduct($name, $category, $price, $quantity, $description) {
        global $conn;

        $sql = "INSERT INTO furnitures 
            (name, category, price, stock_quantity, description) 
            VALUES (?, ?, ?, ?, ?)";
    
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception(mysqli_error($conn));
        }
        $price = round((float)$price, 2);
        mysqli_stmt_bind_param($stmt, "ssdis", 
            $name, $category, $price, $quantity, $description);
    
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    
        return $success;
    }
    public function removeFurniture(int $id): array
    {
        $furniture = Furniture::findById($id);

        if (!$furniture) {
            return ['success' => false, 'message' => 'Furniture not found'];
        }

        $deleted = $furniture->delete();

        if (!$deleted) {
            return ['success' => false, 'message' => 'Failed to delete furniture'];
        }

        return ['success' => true];
    }
}