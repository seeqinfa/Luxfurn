<?php
session_start();

// Check if required parameters are provided
if (!isset($_POST['index']) || !isset($_POST['quantity']) || !is_numeric($_POST['index']) || !is_numeric($_POST['quantity'])) {
    $_SESSION['error_message'] = "Invalid update request.";
    header('Location: viewCart.php');
    exit();
}

$index = intval($_POST['index']);
$quantity = intval($_POST['quantity']);

// Check if cart exists and has items
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['error_message'] = "Your cart is empty.";
    header('Location: viewCart.php');
    exit();
}

// Check if the index is valid
if ($index < 0 || $index >= count($_SESSION['cart'])) {
    $_SESSION['error_message'] = "Invalid item to update.";
    header('Location: viewCart.php');
    exit();
}

// Check if quantity is valid
if ($quantity <= 0) {
    // If quantity is 0 or negative, remove item
    $itemName = $_SESSION['cart'][$index]['name'];
    array_splice($_SESSION['cart'], $index, 1);
    $_SESSION['success_message'] = $itemName . " has been removed from your cart.";
} else {
    // Update quantity
    $_SESSION['cart'][$index]['quantity'] = $quantity;
    $_SESSION['success_message'] = "Cart updated successfully.";
}

header('Location: viewCart.php');
exit();
?>