<?php
// Src/Boundary/Customer/viewOrderUI.php

include '../../header.php';

if (!function_exists('h')) {
    function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}

if (empty($_SESSION['username'])) {
    http_response_code(401);
    ?>
    <!doctype html>
    <html lang="en">
    <head>
    <meta charset="utf-8">
    <title>Please Log In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      body {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f8f8f8;
      }
      .login-message {
        background: white;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: center;
        font-size: 1.2rem;
        color: #333;
      }
      .btn {
        display: inline-block;
        margin-top: 15px;
        padding: 10px 18px;
        background-color: #e67e22;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
      }
      .btn:hover {
        background-color: #cf6d17;
      }
    </style>
    </head>
    <body>
      <div class="login-message">
        Please log in to view your orders.
        <br>
        <a href="#" class="btn" onclick="openLoginPopup()">Login</a>
      </div>

      <script>
        function openLoginPopup() {
          const loginBtn = document.querySelector(".btnLogin");
          if (loginBtn) loginBtn.click();
        }
      </script>
    </body>
    </html>
    <?php
    exit;
}

$USERNAME = $_SESSION['username'];

require_once dirname(__DIR__, 2) . '/Controllers/Customer/viewOrderCtrl.php';
$ctrl = new ViewOrderCtrl();

// CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (!function_exists('check_csrf')) {
    function check_csrf(string $t): bool {
        return hash_equals($_SESSION['csrf_token'] ?? '', $t);
    }
}

// Accept id from GET or POST
function read_order_id(): int {
    $candidates = [
        $_GET['order_id'] ?? null,
        $_GET['id'] ?? null,
        $_POST['order_id'] ?? null,
        $_POST['id'] ?? null,
    ];
    foreach ($candidates as $v) {
        if ($v !== null && ctype_digit((string)$v) && (int)$v > 0) {
            return (int)$v;
        }
    }
    return 0;
}

$orderId = read_order_id();
$message = null;

// Handle cancel action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'cancel') {
    if (!check_csrf($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        echo "Invalid CSRF token.";
        exit;
    }
    $orderId = read_order_id();
    if ($orderId > 0) {
        $ok = $ctrl->cancelForUser($orderId, $USERNAME);
        $message = $ok ? "Order has been cancelled." : "Unable to cancel this order.";
    }
}

// If no orderId, show My Orders list
if ($orderId <= 0) {
    $orders = $ctrl->listForUser($USERNAME, 100, 0);
    ?>
    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <title>My Orders</title>
      <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
      <div class="card" style="max-width: 1000px; width: 95%; margin: 120px auto; background: white; padding: 20px; border-radius: 12px;">
        <h2 style="margin-bottom:12px;">My Orders</h2>

        <?php if (!$orders): ?>
          <div class="success-message">No orders yet.</div>
        <?php else: ?>
          <div style="overflow:auto;">
            <table style="width:100%; border-collapse:collapse;">
              <thead>
                <tr>
                  <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">ID</th>
                  <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">Status</th>
                  <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">Total</th>
                  <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">Created</th>
                  <th style="text-align:left; padding:10px; border-bottom:1px solid #ddd;">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $o): ?>
                  <tr>
                    <td style="padding:10px; border-bottom:1px solid #eee;"><?= h($o['order_id']) ?></td>
                    <td style="padding:10px; border-bottom:1px solid #eee;"><?= h($o['order_status']) ?></td>
                    <td style="padding:10px; border-bottom:1px solid #eee;">$<?= h($o['total_amount']) ?></td>
                    <td style="padding:10px; border-bottom:1px solid #eee;"><?= h($o['created_at']) ?></td>
                    <td style="padding:10px; border-bottom:1px solid #eee;">
                      <a class="btn" href="?order_id=<?= h($o['order_id']) ?>">View</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </body>
    </html>
    <?php
    exit;
}

// Load the specific order
$order = $ctrl->showForUser($orderId, $USERNAME);
if (!$order) {
    http_response_code(404);
    echo "Order not found.";
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Order #<?= h($order['order_id']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <div class="card" style="max-width: 760px; width: 95%; margin: 120px auto; background: white; padding: 20px; border-radius: 12px;">
    <h2 style="margin-bottom:8px;">Order #<?= h($order['order_id']) ?></h2>
    <p class="muted" style="margin-bottom:12px;">Signed in as <code><?= h($USERNAME) ?></code></p>

    <?php if ($message): ?>
      <div class="success-message"><?= h($message) ?></div>
    <?php endif; ?>

    <div class="row" style="display:grid; grid-template-columns: 1fr 2fr; gap:8px; margin:6px 0;">
      <strong>Status</strong><div><?= h($order['order_status']) ?></div>
      <strong>Name</strong><div><?= h($order['customer_first_name'].' '.$order['customer_last_name']) ?></div>
      <strong>Email</strong><div><?= h($order['customer_email']) ?></div>
      <strong>Phone</strong><div><?= h($order['customer_phone']) ?></div>
    </div>

    <hr style="margin:12px 0;">

    <div class="row" style="display:grid; grid-template-columns: 1fr 2fr; gap:8px; margin:6px 0;">
      <strong>Ship To</strong>
      <div>
        <?= h($order['shipping_address']) ?><br>
        <?= h($order['shipping_city']) ?>, <?= h($order['shipping_state']) ?> <?= h($order['shipping_zip']) ?>
      </div>
    </div>

    <hr style="margin:12px 0;">

    <div class="row" style="display:grid; grid-template-columns: 1fr 2fr; gap:8px; margin:6px 0;">
      <strong>Subtotal</strong><div>$<?= h($order['subtotal']) ?></div>
      <strong>Tax</strong><div>$<?= h($order['tax_amount']) ?></div>
      <strong>Shipping</strong><div>$<?= h($order['shipping_fee']) ?></div>
      <strong>Total</strong><div><strong>$<?= h($order['total_amount']) ?></strong></div>
    </div>

    <hr style="margin:12px 0;">

    <div class="row" style="display:grid; grid-template-columns: 1fr 2fr; gap:8px; margin:6px 0;">
      <strong>Created</strong><div><?= h($order['created_at']) ?></div>
      <strong>Updated</strong><div><?= h($order['updated_at']) ?></div>
    </div>

    <div class="actions" style="display:flex; gap:12px; justify-content:center; margin-top:20px;">
      <a href="viewOrderUI.php" class="btn">← Back to My Orders</a>
      <?php if (in_array($order['order_status'], ['pending','processing'], true)): ?>
        <form method="post" onsubmit="return confirm('Cancel this order?');">
          <input type="hidden" name="action" value="cancel">
          <input type="hidden" name="order_id" value="<?= h($order['order_id']) ?>">
          <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
          <button class="btn btn-danger" type="submit">Cancel Order</button>
        </form>
      <?php else: ?>
        <button class="btn" type="button" disabled>Cannot cancel (<?= h($order['order_status']) ?>)</button>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
