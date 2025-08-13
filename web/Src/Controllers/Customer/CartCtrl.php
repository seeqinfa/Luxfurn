<?php

require_once dirname(__DIR__, 2) . '/Entities/Cart.php';
require_once dirname(__DIR__, 2) . '/Controllers/Customer/viewFurnitureCtrl.php';

class CartController
{
    public function handleCartActions()
    {
        session_start();
        
        $action = $_GET['action'] ?? '';
        
        try {
            switch ($action) {
                case 'add':
                    $this->handleAddToCart();
                    break;
                case 'remove':
                    $this->handleRemoveFromCart();
                    break;
                case 'update':
                    $this->handleUpdateCart();
                    break;
                default:
                    $_SESSION['error_message'] = "Invalid action.";
                    $this->redirectToCart();
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            $this->redirectToCart();
        }
    }

    private function handleAddToCart()
    {
        $furnitureID = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $successMessage = $this->addToCart($furnitureID);
        $_SESSION['success_message'] = $successMessage;
        
        if (isset($_GET['redirect']) && $_GET['redirect'] == 'cart') {
            $this->redirectToCart();
        } else {
            header('Location: ../../Boundary/Customer/viewFurnitureUI.php');
            exit();
        }
    }

    private function handleRemoveFromCart()
    {
        if (!isset($_GET['index'])) {
            throw new Exception("Invalid item to remove.");
        }
        
        $successMessage = $this->removeFromCart($_GET['index']);
        $_SESSION['success_message'] = $successMessage;
        $this->redirectToCart();
    }

    private function handleUpdateCart()
    {
        if (!isset($_POST['index']) || !isset($_POST['quantity'])) {
            throw new Exception("Invalid update request.");
        }
        
        $successMessage = $this->updateCart($_POST['index'], $_POST['quantity']);
        $_SESSION['success_message'] = $successMessage;
        $this->redirectToCart();
    }

    private function redirectToCart()
    {
        header('Location: ../../Boundary/Customer/CartUI.php');
        exit();
    }

    public function addToCart($furnitureID)
    {
        if ($furnitureID <= 0) {
            throw new Exception("Invalid furniture item.");
        }

        $furnitureController = new FurnitureController();
        $furniture = $furnitureController->getFurnitureById($furnitureID);
        
        if (!$furniture) {
            throw new Exception("Furniture not found.");
        }

        // Initialize cart in session if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        // Check if item exists in cart, exists = +1 to qty
        $itemExists = false;
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['furnitureID'] == $furnitureID) {
                $cartItem['quantity'] += 1;
                $itemExists = true;
                break;
            }
        }

        // If item doesn't exist in cart, add into cart
        if (!$itemExists) {
            $_SESSION['cart'][] = array(
                'furnitureID' => $furniture->furnitureID,
                'name' => $furniture->name,
                'price' => $furniture->price,
                'image_url' => $furniture->image_url,
                'category' => $furniture->category,
                'quantity' => 1
            );
        }

        return $furniture->name . " has been added to your cart!";
    }

    public function removeFromCart($index)
    {
        if (!is_numeric($index)) {
            throw new Exception("Invalid item to remove.");
        }

        $index = intval($index);

        // Check if cart exists and has items
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            throw new Exception("Your cart is empty.");
        }

        // Check if the index is valid
        if ($index < 0 || $index >= count($_SESSION['cart'])) {
            throw new Exception("Invalid item to remove.");
        }

        // Get the item name before removing
        $itemName = $_SESSION['cart'][$index]['name'];

        // Remove the item from cart
        array_splice($_SESSION['cart'], $index, 1);

        return $itemName . " has been removed from your cart.";
    }

    public function updateCart($index, $quantity)
    {
        if (!is_numeric($index) || !is_numeric($quantity)) {
            throw new Exception("Invalid update request.");
        }

        $index = intval($index);
        $quantity = intval($quantity);

        // Check if cart exists and has items
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            throw new Exception("Your cart is empty.");
        }

        // Check if the index is valid
        if ($index < 0 || $index >= count($_SESSION['cart'])) {
            throw new Exception("Invalid item to update.");
        }

        // Check if quantity is valid
        if ($quantity <= 0) {
            // If quantity is 0 or negative, remove item
            $itemName = $_SESSION['cart'][$index]['name'];
            array_splice($_SESSION['cart'], $index, 1);
            return $itemName . " has been removed from your cart.";
        } else {
            // Update quantity
            $_SESSION['cart'][$index]['quantity'] = $quantity;
            return "Cart updated successfully.";
        }
    }

    public function getCartItems()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
        return $_SESSION['cart'];
    }

    public function getCartTotal()
    {
        $cartItems = $this->getCartItems();
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
        }

        return $totalPrice;
    }

    public function isCartEmpty()
    {
        return empty($this->getCartItems());
    }
}

// Handle actions if this file is accessed directly
if (basename($_SERVER['PHP_SELF']) === 'CartCtrl.php') {
    $cartController = new CartController();
    $cartController->handleCartActions();
}

?>