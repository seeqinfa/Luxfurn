<?php
session_start();

// Check if index parameter is provided
if (!isset($_GET['index']) || !is_numeric($_GET['index'])) {
    $_SESSION['error_message'] = "Invalid item to remove.";
    header('Location: viewCart.php');
    exit();
}

$index = intval($_GET['index']);

// Check if cart exists and has items
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['error_message'] = "Your cart is empty.";
    header('Location: viewCart.php');
    exit();
}

// Check if the index is valid
if ($index < 0 || $index >= count($_SESSION['cart'])) {
    $_SESSION['error_message'] = "Invalid item to remove.";
    header('Location: viewCart.php');
    exit();
}

// Get the item name before removing
$itemName = $_SESSION['cart'][$index]['name'];

// Remove the item from cart
array_splice($_SESSION['cart'], $index, 1);

$_SESSION['success_message'] = $itemName . " has been removed from your cart.";

header('Location: viewCart.php');
exit();
?>