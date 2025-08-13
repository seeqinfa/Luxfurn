<?php
session_start();
require_once dirname(__DIR__, 2) . '/Controllers/Customer/viewFurnitureCtrl.php';

// Get furniture ID from URL
$furnitureID = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($furnitureID <= 0) {
    $_SESSION['error_message'] = "Invalid furniture item.";
    header('Location: viewFurnitureUI.php');
    exit();
}

try {
    $controller = new FurnitureController();
    
    // Check if furniture exists
    $furniture = $controller->getFurnitureById($furnitureID);
    
    if (!$furniture) {
        $_SESSION['error_message'] = "Furniture not found.";
        header('Location: viewFurnitureUI.php');
        exit();
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
    
    $_SESSION['success_message'] = $furniture->name . " has been added to your cart!";
    
    if (isset($_GET['redirect']) && $_GET['redirect'] == 'cart') {
        header('Location: viewCart.php');
    } else {
        header('Location: viewFurnitureUI.php');
    }
    exit();
    
} catch (Exception $e) {
    $_SESSION['error_message'] = "Error adding item to cart: " . $e->getMessage();
    header('Location: viewFurnitureUI.php');
    exit();
}
?>