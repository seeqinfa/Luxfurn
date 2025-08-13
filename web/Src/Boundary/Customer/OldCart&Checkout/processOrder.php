<?php
require_once '../../header.php';
require_once dirname(__DIR__, 2) . '/db_config.php';

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

class OrderProcessor {
    private $db;
    
    public function __construct() {
		global $conn;
        $this->db = $conn;
        
        // Check for any database connection issues
        if ($this->db->connect_error) {
            throw new Exception("Database connection failed: " . $this->db->connect_error);
        }
    }
    
    public function createOrder($customerData, $cartItems, $totals) {
        try {
            $this->db->begin_transaction();
            
            // Insert order record
            $orderQuery = "INSERT INTO orders (
                customer_first_name, customer_last_name, customer_email, customer_phone,
                shipping_address, shipping_city, shipping_state, shipping_zip,
                subtotal, tax_amount, shipping_fee, total_amount,
                order_status, special_instructions, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW())";
            
            $stmt = $this->db->prepare($orderQuery);
            $stmt->bind_param(
                "ssssssssdddds",
                $customerData['first_name'], $customerData['last_name'], 
                $customerData['email'], $customerData['phone'],
                $customerData['address'], $customerData['city'], 
                $customerData['state'], $customerData['zip_code'],
                $totals['subtotal'], $totals['tax'], $totals['shipping'], $totals['total'],
                $customerData['special_instructions']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create order: " . $stmt->error);
            }
            
            $orderId = $this->db->insert_id;
            
            // Insert order items
            $itemQuery = "INSERT INTO order_items (
                order_id, furniture_id, furniture_name, unit_price, quantity, total_price
            ) VALUES (?, ?, ?, ?, ?, ?)";
            
            $itemStmt = $this->db->prepare($itemQuery);
            
            foreach ($cartItems as $item) {
                $itemTotal = $item['price'] * $item['quantity'];
                $itemStmt->bind_param(
                    "iisdid",
                    $orderId, $item['furnitureID'], $item['name'],
                    $item['price'], $item['quantity'], $itemTotal
                );
                
                if (!$itemStmt->execute()) {
                    throw new Exception("Failed to add order item: " . $itemStmt->error);
                }
            }
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    
    public function getOrderById($orderId) {
        $query = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    }
    
    public function getOrderItems($orderId) {
        $query = "SELECT * FROM order_items WHERE order_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
}

?>