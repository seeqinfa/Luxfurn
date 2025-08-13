<?php
require_once dirname(__DIR__, 2) . '/Entities/Order.php';

class OrderCtrl {
    
    public function createOrder($customerData, $cartItems, $totals) {
        try {
			$username = $_SESSION['username'] ?? null;
			if (!$username) {
				throw new RuntimeException('User must be logged in to place an order.');
			}


            // Create Order entity and set properties
            $order = new Order();
			$order->setUsername($username);
            $order->setCustomerFirstName($customerData['first_name']);
            $order->setCustomerLastName($customerData['last_name']);
            $order->setCustomerEmail($customerData['email']);
            $order->setCustomerPhone($customerData['phone']);
            $order->setShippingAddress($customerData['address']);
            $order->setShippingCity($customerData['city']);
            $order->setShippingState($customerData['state']);
            $order->setShippingZip($customerData['zip_code']);
            $order->setSubtotal($totals['subtotal']);
            $order->setTaxAmount($totals['tax']);
            $order->setShippingFee($totals['shipping']);
            $order->setTotalAmount($totals['total']);
            $order->setOrderStatus('pending');
            $order->setSpecialInstructions($customerData['special_instructions']);
            
            // Create Order entities for each cart item
            foreach ($cartItems as $item) {
                $order->addItem(
					$item['furnitureID'], 
					$item['name'], 
					$item['price'], 
					$item['quantity']
				);
            }
            
            // Save order (this will handle database operations)
            $orderId = $order->save();
            
            return $orderId;
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    public function getOrderById($orderId) {
        return Order::findById($orderId);
    }
    
    public function validateOrderData($postData) {
        $errors = [];
        
        // Required fields validation
        $required_fields = ['first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip_code'];
        foreach ($required_fields as $field) {
            if (empty(trim($postData[$field]))) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
            }
        }
        
        // Email validation
        if (!empty($postData['email']) && !filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address.";
        }
        
        // Phone validation
        if (!empty($postData['phone']) && !preg_match('/^\d{8}$/', $postData['phone'])) {
            $errors[] = "Please enter a valid phone number (8 digits).";
        }
        
        // ZIP code validation (basic)
        if (!empty($postData['zip_code']) && !preg_match('/^\d{6}$/', $postData['zip_code'])) {
            $errors[] = "Please enter a valid ZIP code (6 digits).";
        }
        
        return $errors;
    }
    
    public function calculateTotals($cartItems) {
        $subtotal = 0;
        $tax_rate = 0.08;
        $shipping_fee = 15.00;
        
        // Calculate subtotal
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        $tax_amount = $subtotal * $tax_rate;
        $total = $subtotal + $tax_amount + $shipping_fee;
        
        return [
            'subtotal' => $subtotal,
            'tax' => $tax_amount,
            'shipping' => $shipping_fee,
            'total' => $total
        ];
    }
    
    public function validateCart($cartItems) {
        $errors = [];
        
        if (empty($cartItems)) {
            $errors[] = "Cart is empty.";
            return $errors;
        }
        
        foreach ($cartItems as $item) {
            if (!isset($item['furnitureID']) || !isset($item['name']) || !isset($item['price']) || !isset($item['quantity'])) {
                $errors[] = "Invalid cart item structure.";
                break;
            }
            
            if ($item['quantity'] <= 0) {
                $errors[] = "Invalid quantity for item: " . $item['name'];
            }
            
            if ($item['price'] <= 0) {
                $errors[] = "Invalid price for item: " . $item['name'];
            }
        }
        
        return $errors;
    }
    
    public function updateOrderStatus($orderId, $status) {
        $order = Order::findById($orderId);
        if (!$order) {
            throw new Exception("Order not found");
        }
        
        $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Invalid order status");
        }
        
        $order->setOrderStatus($status);
        return $order->save();
    }
    
    public function cancelOrder($orderId) {
        return $this->updateOrderStatus($orderId, 'cancelled');
    }
    
    public function processOrder($orderId) {
        return $this->updateOrderStatus($orderId, 'processing');
    }
}
?>