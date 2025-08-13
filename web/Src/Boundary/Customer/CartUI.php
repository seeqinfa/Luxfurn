<?php
session_start();
include '../../header.php';
require_once dirname(__DIR__, 2) . '/Controllers/Customer/CartCtrl.php';

$cartController = new CartController();
$cartItems = $cartController->getCartItems();
$totalPrice = $cartController->getCartTotal();
?>

<div class="container" style="margin-top: 140px; max-width: 1200px; width: 100%; padding: 0 20px;">
    <h2 style="text-align: center; margin-bottom: 30px;">Shopping Cart</h2>
    
    <?php if ($cartController->isCartEmpty()): ?>
        <div style="text-align: center; padding: 50px; padding-bottom: 20px;">
            <h3 style="padding-bottom: 30px;">
            Your cart is empty
            </h3>
            <a href="viewFurnitureUI.php" style="background-color: #e67e22; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($cartItems as $index => $item): ?>
                <div class="cart-item" style="display: flex; align-items: center; background: white; padding: 15px; margin-bottom: 15px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 5px; margin-right: 15px;">
                    
                    <div style="flex: 1;">
                        <h4 style="margin: 0; font-size: 18px;"><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p style="margin: 5px 0; color: gray; font-size: 14px;"><?php echo htmlspecialchars($item['category']); ?></p>
                        <p style="margin: 5px 0; color: #e67e22; font-weight: bold;">$<?php echo number_format($item['price'], 2); ?></p>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <form method="POST" action="../../Controllers/Customer/CartCtrl.php?action=update" style="display: flex; align-items: center; gap: 5px;">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <label style="font-size: 14px;">Qty:</label>
                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                   min="1" max="99" style="width: 50px; padding: 3px; border: 1px solid #ccc; border-radius: 3px;">
                            <button type="submit" style="background-color: #28a745; color: white; padding: 3px 8px; border: none; border-radius: 3px; font-size: 12px; cursor: pointer;">Update</button>
                        </form>
                        <span style="font-weight: bold; color: #e67e22;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        <a href="../../Controllers/Customer/CartCtrl.php?action=remove&index=<?php echo $index; ?>" 
                           style="background-color: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px;"
                           onclick="return confirm('Remove this item from cart?')">Remove</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="cart-summary" style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: right;">
            <h3>Total: $<?php echo number_format($totalPrice, 2); ?></h3>
            <div style="margin-top: 15px;">
                <a href="viewFurnitureUI.php" style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;">Continue Shopping</a>
                <a href="CheckoutUI.php" style="background-color: #e67e22; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>