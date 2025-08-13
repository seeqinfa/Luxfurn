<?php
// Src/Boundary/Admin/AdminAssignRoleUI.php
require_once '../../header.php';
require_once dirname(__DIR__, 2) . '/Controllers/Admin/AdminAssignRoleCtrl.php';

if (!function_exists('h')) {
    function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
}
$currentAdmin = $_SESSION['username'] ?? 'admin';
// Enforce your RBAC here if needed:
// if (($_SESSION['role'] ?? '') !== 'superadmin') { http_response_code(403); exit('Forbidden'); }

// CSRF
if (empty($_SESSION['csrf_admin_assign'])) {
    $_SESSION['csrf_admin_assign'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_admin_assign'];

$ctrl = new AdminAssignRoleCtrl();

// Load data
$admins   = $ctrl->getAdmins();
$active   = $ctrl->getActiveMap();
$msg = $err = '';

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
            throw new RuntimeException('Invalid CSRF token.');
        }
        $selected = $_POST['agent'] ?? [];
        if (!is_array($selected)) $selected = [];
        $ctrl->saveAssignments($selected, $currentAdmin);

        $msg    = "Roles updated successfully.";
        $active = $ctrl->getActiveMap(); // refresh
    } catch (Throwable $e) {
        $err = "Could not update roles: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assign Support Ticket Roles</title>
  <link rel="stylesheet" href="../../CSS/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .container { margin-top: 140px; max-width: 1000px; padding: 0 20px; margin-inline: auto; }
    .section-title { font-size: 24px; margin: 30px 0 20px; color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }

    .grid { display: grid; grid-template-columns: 2fr 1fr; gap: 16px; }
    @media (max-width: 900px) { .grid { grid-template-columns: 1fr; } }

    .panel { background:#fff; border-radius:10px; box-shadow:0 4px 10px rgba(0,0,0,.1); padding:16px; }
    .panel h3 { margin:0 0 12px; font-size:18px; color:#333; }

    .alert { margin-bottom:12px; padding:10px 12px; border-radius:6px; }
    .alert.ok { background:#ecf9f0; border:1px solid #c6efda; color:#2e7d32; }
    .alert.err{ background:#fdecea; border:1px solid #f5c6cb; color:#b71c1c; }

    .admin-list { max-height: 420px; overflow:auto; border:1px solid #eee; border-radius:8px; }
    .row { display:flex; align-items:center; gap:10px; padding:10px 12px; border-bottom:1px solid #f5f5f5; }
    .row:last-child { border-bottom:0; }
    .row label { flex:1; display:flex; flex-direction:column; }
    .row small { color:#777; }

    .actions { display:flex; gap:8px; margin-top:12px; }
    .btn { display:inline-block; padding:10px 14px; border-radius:6px; background:#e67e22; color:#fff; text-decoration:none; border:none; cursor:pointer; }
    .btn:hover { background:#d35400; }
    .btn.secondary { background:#f4f4f4; color:#333; }
    .btn.secondary:hover { background:#e9e9e9; }

    .pill { display:inline-block; padding:2px 8px; border-radius:999px; font-size:12px; background:#f0f0f0; margin:2px 4px 2px 0; }
    ul.clean { list-style:none; margin:0; padding:0; display:flex; flex-wrap:wrap; }

    .search { display:flex; gap:8px; margin-bottom:10px; }
    .search input { flex:1; padding:8px 10px; border:1px solid #ddd; border-radius:6px; }

    .muted { color:#666; font-size:13px; }
  </style>
</head>
<body>
<div class="container">
  <h1 class="section-title"><i class="fas fa-user-shield"></i> Assign Support Ticket Roles</h1>

  <?php if ($msg): ?><div class="alert ok"><i class="fas fa-check-circle"></i> <?= h($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert err"><i class="fas fa-exclamation-triangle"></i> <?= h($err) ?></div><?php endif; ?>

  <div class="grid">
    <!-- Left: Assignable admins -->
    <div class="panel">
      <h3>Admins list</h3>

      <div class="search">
        <input type="text" id="filter" placeholder="Filter by username or email…">
        <button type="button" class="btn secondary" id="btnAll">Select All</button>
        <button type="button" class="btn secondary" id="btnNone">Clear</button>
      </div>

      <form method="post" action="">
        <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
        <div class="admin-list" id="adminList">
          <?php if (!$admins): ?>
            <div class="muted" style="padding:8px 12px;">No admin users found.</div>
          <?php else: foreach ($admins as $a):
            $u   = (string)$a['username'];
            $em  = (string)($a['email'] ?? '');
            $chk = isset($active[$u]) ? 'checked' : '';
          ?>
            <div class="row" data-u="<?= h(strtolower($u)) ?>" data-e="<?= h(strtolower($em)) ?>">
              <input type="checkbox" name="agent[]" value="<?= h($u) ?>" <?= $chk ?> />
              <label>
                <strong><?= h($u) ?></strong>
                <?php if ($em): ?><small><?= h($em) ?></small><?php endif; ?>
              </label>
            </div>
          <?php endforeach; endif; ?>
        </div>

        <div class="actions">
          <button type="submit" class="btn"><i class="fas fa-save"></i> Save Changes</button>
          <a href="AdminDashboardUI.php" class="btn secondary">Back to Dashboard</a>
        </div>
      </form>
    </div>

    <!-- Right: Currently assigned -->
    <div class="panel">
      <h3>Currently Assigned Agents</h3>
      <?php if (!$active): ?>
        <div class="muted">No agents assigned yet.</div>
      <?php else: ?>
        <ul class="clean">
          <?php foreach (array_keys($active) as $u): ?>
            <li><span class="pill"><?= h($u) ?></span></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>

      <p class="muted" style="margin-top:12px;">
        Tip: Use the checkboxes to add/remove agents. Click <em>Save Changes</em> to apply.
      </p>
    </div>
  </div>
</div>

<script>
  // Filter list by username/email + select all/none
  (function(){
    var filter = document.getElementById('filter');
    var list   = document.getElementById('adminList');
    if (!filter || !list) return;

    filter.addEventListener('input', function(){
      var q = this.value.trim().toLowerCase();
      var rows = list.querySelectorAll('.row');
      rows.forEach(function(r){
        var u = r.getAttribute('data-u') || '';
        var e = r.getAttribute('data-e') || '';
        r.style.display = (!q || u.includes(q) || e.includes(q)) ? '' : 'none';
      });
    });

    var btnAll  = document.getElementById('btnAll');
    var btnNone = document.getElementById('btnNone');
    if (btnAll) btnAll.addEventListener('click', function(){
      list.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);
    });
    if (btnNone) btnNone.addEventListener('click', function(){
      list.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
    });
  })();
</script>
</body>
</html>
