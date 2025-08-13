<?php
ob_start();  // Output buffering START, prevent clash with other echos etc
session_start();
require_once '../../header.php';
require_once '../../Controllers/Customer/OrderCtrl.php';

// Check if cart exists and has items
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['error_message'] = "Your cart is empty. Please add items before checkout.";
    header('Location: CartUI.php');
    exit();
}

$cartItems = $_SESSION['cart'];
$orderController = new OrderCtrl();
$totals = $orderController->calculateTotals($cartItems);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data using controller
    $errors = $orderController->validateOrderData($_POST);
    
    // If no errors, process using OrderController
    if (empty($errors)) {
        try {
            $customerData = [
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'address' => $_POST['address'],
                'city' => $_POST['city'],
                'state' => $_POST['state'],
                'zip_code' => $_POST['zip_code'],
                'special_instructions' => $_POST['special_instructions'] ?? ''
            ];

            // Create the order in the database
            $orderId = $orderController->createOrder($customerData, $cartItems, $totals);

            // Clear cart after order placed
            unset($_SESSION['cart']);
            
            $_SESSION['success_message'] = "Order #$orderId placed successfully!";

            header('Location: viewFurnitureUI.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = "There was an error processing your order. Please try again.";
            error_log("Order processing error: " . $e->getMessage());
        }
    }
}
ob_end_flush();  // Output buffering END
?>

<style>
.checkout-container {
    max-width: 1000px;
    margin: 140px auto 40px;
    padding: 20px;
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
}

.checkout-form {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.order-summary {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    height: fit-content;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 16px;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #e67e22;
    box-shadow: 0 0 5px rgba(230, 126, 34, 0.3);
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
}

.item-details {
    flex: 1;
}

.item-name {
    font-weight: bold;
    margin-bottom: 5px;
}

.item-price {
    color: #666;
    font-size: 14px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin: 10px 0;
    padding: 5px 0;
}

.summary-total {
    font-size: 18px;
    font-weight: bold;
    color: #e67e22;
    border-top: 2px solid #e67e22;
    padding-top: 10px;
    margin-top: 15px;
}

.btn-checkout {
    width: 100%;
    background-color: #e67e22;
    color: white;
    padding: 15px;
    border: none;
    border-radius: 5px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 20px;
}

.btn-checkout:hover {
    background-color: #c15500;
}

.btn-back {
    display: inline-block;
    background-color: #6c757d;
    color: white;
    padding: 10px 20px;
    text-decoration: none;
    border-radius: 5px;
    margin-bottom: 20px;
}

.btn-back:hover {
    background-color: #5a6268;
}

.error-messages {
    background-color: #f8d7da;
    color: #721c24;
    padding: 15px;
    border-radius: 5px;
    margin-bottom: 20px;
    border: 1px solid #f5c6cb;
}

.error-messages ul {
    margin: 0;
    padding-left: 20px;
}

@media (max-width: 768px) {
    .checkout-container {
        grid-template-columns: 1fr;
        gap: 20px;
        margin-top: 120px;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="checkout-container">
    <div class="checkout-form">
        <a href="CartUI.php" class="btn-back">← Back to Cart</a>
        
        <h2>Checkout</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <h3>Shipping Information</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="address">Street Address *</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="city">City *</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="state">State *</label>
                    <select id="state" name="state" required>
                        <option value="">Select State</option>
                        <option value="AL" <?php echo ($_POST['state'] ?? '') === 'AL' ? 'selected' : ''; ?>>Alabama</option>
                        <option value="AK" <?php echo ($_POST['state'] ?? '') === 'AK' ? 'selected' : ''; ?>>Alaska</option>
                        <option value="AZ" <?php echo ($_POST['state'] ?? '') === 'AZ' ? 'selected' : ''; ?>>Arizona</option>
                        <option value="CA" <?php echo ($_POST['state'] ?? '') === 'CA' ? 'selected' : ''; ?>>California</option>
                        <option value="FL" <?php echo ($_POST['state'] ?? '') === 'FL' ? 'selected' : ''; ?>>Florida</option>
                        <option value="NY" <?php echo ($_POST['state'] ?? '') === 'NY' ? 'selected' : ''; ?>>New York</option>
                        <option value="TX" <?php echo ($_POST['state'] ?? '') === 'TX' ? 'selected' : ''; ?>>Texas</option>
                        <!-- Add more states as needed -->
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="zip_code">ZIP Code *</label>
                <input type="text" id="zip_code" name="zip_code" value="<?php echo htmlspecialchars($_POST['zip_code'] ?? ''); ?>" required>
            </div>
            
            <h3>Payment Information</h3>
            
            <div class="form-group">
                <label for="card_number">Card Number *</label>
                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="expiry_date">Expiry Date *</label>
                    <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required>
                </div>
                <div class="form-group">
                    <label for="cvv">CVV *</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="card_name">Name on Card *</label>
                <input type="text" id="card_name" name="card_name" required>
            </div>
            
            <div class="form-group">
                <label for="special_instructions">Special Instructions (Optional)</label>
                <textarea id="special_instructions" name="special_instructions" rows="3" placeholder="Any special delivery instructions..."><?php echo htmlspecialchars($_POST['special_instructions'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="btn-checkout">Place Order</button>
        </form>
    </div>
    
    <div class="order-summary">
        <h3>Order Summary</h3>
        
        <div class="order-items">
            <?php foreach ($cartItems as $item): ?>
                <div class="order-item">
                    <div class="item-details">
                        <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="item-price">$<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?></div>
                    </div>
                    <div class="item-total">
                        $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="summary-totals">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>$<?php echo number_format($totals['subtotal'], 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Tax (8%):</span>
                <span>$<?php echo number_format($totals['tax'], 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span>$<?php echo number_format($totals['shipping'], 2); ?></span>
            </div>
            <div class="summary-row summary-total">
                <span>Total:</span>
                <span>$<?php echo number_format($totals['total'], 2); ?></span>
            </div>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background-color: #d4edda; border-radius: 5px; border: 1px solid #c3e6cb;">
            <small style="color: #155724;">
                <strong>Secure Checkout:</strong> Your payment information is encrypted and secure.
            </small>
        </div>
    </div>
</div>

</body>
</html>