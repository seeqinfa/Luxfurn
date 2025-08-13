<?php
include '../../header.php';
require_once dirname(__DIR__, 2) . '/Controllers/Customer/CustomerSupportTicketsCtrl.php';

if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

/* The logged-in customer */
$userId = $_SESSION['user_id'] ?? null;

/* Use the customer controller */
$ctrl = new CustomerSupportTicketsCtrl();

$error = $success = '';
$mode = $_GET['mode'] ?? $_POST['mode'] ?? null;
$viewOnly = ($mode === 'view') || (isset($_GET['readonly']) && $_GET['readonly'] == '1');

/* CSRF */
if (empty($_SESSION['csrf_ticket_user'])) {
    $_SESSION['csrf_ticket_user'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_ticket_user'];

/* Helper: enforce ownership */
function ensure_owner_or_throw(?array $ticket, ?int $userId): void {
    if (!$ticket) {
        throw new RuntimeException('Ticket not found.');
    }
    if (!$userId || (int)$ticket['user_id'] !== (int)$userId) {
        http_response_code(403);
        throw new RuntimeException('You are not allowed to view or modify this ticket.');
    }
}

/* POST: reply (customer) — no resolve here */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['response'])) {
    try {
        if ($viewOnly) { throw new RuntimeException('View-only mode: replies are disabled.'); }
        if (!hash_equals($csrf, $_POST['csrf'] ?? '')) { throw new RuntimeException('Invalid CSRF token.'); }

        $ticketId = (int)($_POST['ticket_id'] ?? 0);
        $t = $ctrl->getTicketDetails($ticketId);
        ensure_owner_or_throw($t, $userId);

        $msg = trim($_POST['response'] ?? '');
        // Save as customer reply (admin_id = NULL inside the controller/repo)
        if (method_exists($ctrl, 'respondAsCustomer')) {
            $ctrl->respondAsCustomer($ticketId, (int)$userId, $msg);
        } else {
            // fallback if you still use the admin-style method
            $ctrl->respondToTicket($ticketId, null, $msg);
        }
        $success = 'Reply sent.';
        $_GET['id'] = $ticketId;
    } catch (Throwable $e) { $error = $e->getMessage(); }
}

/* GET: single ticket view (only yours) */
$ticket = null; $replies = [];
if (isset($_GET['id'])) {
    try {
        $ticket = $ctrl->getTicketDetails((int)$_GET['id']);
        ensure_owner_or_throw($ticket, $userId);
        $replies = $ctrl->getTicketReplies((int)$ticket['id']);
    } catch (Throwable $e) {
        $error = $e->getMessage();
        $ticket = null;
    }
}

/* Listing: only tickets created by this user */
$tickets = [];
if (!$ticket) {
    if ($userId) {
        if (method_exists($ctrl, 'getTicketsForUser')) {
            $tickets = $ctrl->getTicketsForUser((int)$userId);
        } else {
            // fallback: if your controller exposes listTicketsForUser
            $tickets = $ctrl->listTicketsForUser((int)$userId);
        }
    } else {
        $error = 'Please log in to see your tickets.';
    }
}

/* Helper: label for replies (admin vs you) */
function reply_label(?int $adminId): string {
    return is_null($adminId) ? 'You' : ('Admin #' . $adminId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Support Tickets</title>
<link rel="stylesheet" href="../../CSS/style.css">
<style>
  .container { margin-top:140px; max-width:1200px; padding:0 20px; margin-inline:auto; }
  .fixed-ticket-width { max-width: 900px; margin: 0 auto; }

  .panel { background:#fff; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,.15); padding:16px; margin-top:16px; }
  .message { padding:10px 12px; border-radius:6px; margin:12px 0; }
  .message.ok { background:#ecf9f0; border:1px solid #c6efda; color:#1b5e20; }
  .message.err { background:#fdecea; border:1px solid #f5c6cb; color:#b71c1c; }

  table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,.15); overflow:hidden; }
  th, td { padding:12px 14px; border-bottom:1px solid #eee; text-align:left; }
  th { background:#e67e22; color:#fff; }
  .status-open { color:#e74c3c; font-weight:600; }
  .status-responded { color:#2ecc71; font-weight:600; }
  .status-resolved { color:#3498db; font-weight:600; }

  .btn { display:inline-block; padding:8px 12px; border-radius:6px; color:#fff; text-decoration:none; font-size:14px; }
  .btn-blue { background:#3498db; }
  .btn-purple { background:#9b59b6; }
  .btn-grey { background:#95a5a6; }

  textarea { width:100%; min-height:120px; padding:10px; border:1px solid #ddd; border-radius:6px; resize:vertical; }
  .reply { background:#fafafa; border:1px solid #eee; border-radius:8px; padding:10px; margin:8px 0; }
  .meta { font-size:12px; color:#777; margin-bottom:6px; }
</style>
</head>
<body>
<div class="container">
  <h1>My Support Tickets</h1>

  <div class="fixed-ticket-width">
    <?php if ($success): ?><div class="message ok"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="message err"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  </div>

  <?php if ($ticket): ?>
    <div class="fixed-ticket-width">
      <a class="btn btn-grey" href="CustomerSupportTicketsUI.php">&larr; Back to My Tickets</a>

      <div class="panel">
        <h2>Ticket #<?= (int)$ticket['id'] ?> — <?= htmlspecialchars($ticket['subject']) ?></h2>
        <p><strong>Status:</strong>
          <span class="status-<?= strtolower($ticket['status']) ?>"><?= htmlspecialchars(ucfirst($ticket['status'])) ?></span>
        </p>
        <p><strong>Your original message:</strong></p>
        <div class="reply"><?= nl2br(htmlspecialchars($ticket['message'])) ?></div>

        <?php if ($replies): ?>
          <h3>Conversation</h3>
          <?php foreach ($replies as $r): ?>
            <div class="reply">
              <div class="meta"><?= htmlspecialchars(reply_label($r['admin_id'])) ?> • <?= htmlspecialchars($r['created_at']) ?></div>
              <div><?= nl2br(htmlspecialchars($r['message'])) ?></div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if (!$viewOnly && strtolower($ticket['status']) !== 'resolved'): ?>
          <form class="panel" method="post">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf) ?>">
            <input type="hidden" name="ticket_id" value="<?= (int)$ticket['id'] ?>">
            <div>
              <label><strong>Your reply</strong></label>
              <textarea name="response" required></textarea>
            </div>
            <div style="margin-top:8px;">
              <button type="submit" class="btn btn-blue">Send Reply</button>
            </div>
          </form>
        <?php elseif (strtolower($ticket['status']) !== 'resolved'): ?>
          <div class="message ok">Viewing only. Replies are disabled.</div>
        <?php else: ?>
          <div class="message ok">This ticket is resolved. Replies are disabled.</div>
        <?php endif; ?>
      </div>
    </div>

  <?php else: ?>
    <div class="fixed-ticket-width">
      <div class="panel">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Subject</th>
              <th>Status</th>
              <th>Created</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$userId): ?>
              <tr><td colspan="5">Please log in to see your tickets.</td></tr>
            <?php elseif (!$tickets): ?>
              <tr><td colspan="5">You have no tickets yet.</td></tr>
            <?php else: foreach ($tickets as $t): ?>
              <tr>
                <td><?= (int)$t['id'] ?></td>
                <td><?= htmlspecialchars($t['subject']) ?></td>
                <td class="status-<?= strtolower($t['status']) ?>"><?= htmlspecialchars(ucfirst($t['status'])) ?></td>
                <td><?= htmlspecialchars($t['created_at']) ?></td>
                <td>
                  <a class="btn btn-blue" href="CustomerSupportTicketsUI.php?id=<?= (int)$t['id'] ?>">Reply</a>
                  <a class="btn btn-purple" href="CustomerSupportTicketsUI.php?id=<?= (int)$t['id'] ?>&mode=view">View</a>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
