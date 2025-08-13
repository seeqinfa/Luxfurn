<?php
// Src/Boundary/Customer/CustomerInstructionManualUI.php

require_once '../../header.php'; 
require_once dirname(__DIR__,2) . '../Controllers/Customer/CustomerInstructionManualCtrl.php';

$q       = trim($_GET['q'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;

$ctrl = new CustomerInstructionManualCtrl();
[$rows, $total] = $ctrl->list($q, $page, $perPage);
$totalPages = max(1, (int)ceil($total / $perPage));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Instruction Manuals</title>
  <link rel="stylesheet" href="../../CSS/style.css">
<style>
  .page { 
    margin-top: 140px; /* increased from 140px */
    max-width: 1200px; 
    padding: 0 20px; 
    margin-left: auto; 
    margin-right: auto; 
  }
  .title { 
    font-size: 24px; 
    margin: 30px 0 16px; 
    border-bottom: 2px solid #eee; 
    padding-bottom: 8px; 
    color: #333; 
  }
  .search { display: flex; gap: 8px; margin-bottom: 12px; }
  .search input { flex: 1; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
  .btn { padding: 10px 14px; border: none; border-radius: 4px; background: #e67e22; color: #fff; cursor: pointer; text-decoration: none; display: inline-block; }
  table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; }
  th,td { padding: 12px 14px; border-bottom: 1px solid #eee; text-align: left; }
  th { background: #e67e22; color: #fff; }
  .pagination { text-align: center; margin-top: 14px; }
  .pagination a { display: inline-block; padding: 8px 12px; margin: 0 3px; background: #f4f4f4; color: #333; border-radius: 4px; text-decoration: none; }
  .pagination a.active { background: #e67e22; color: #fff; }
</style>
</head>
<body>
<div class="page">
  <h1 class="title">Instruction Manuals</h1>

  <!-- Search -->
  <form class="search" method="get" action="">
    <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search by product name, code, or keyword">
    <button class="btn" type="submit">Search</button>
  </form>

  <div style="color:#666; font-size:12px; margin-bottom:8px;">
    Showing <?= count($rows) ?> of <?= (int)$total ?> result(s)
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:70px;">ID</th>
        <th>Product</th>
        <th style="width:160px;">Code</th>
        <th style="width:180px;">Updated</th>
        <th style="width:160px;">Manual</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="5" style="text-align:center;color:#666;">No manuals found.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td>#<?= (int)$r['manualID'] ?></td>
          <td><?= htmlspecialchars($r['product_name']) ?></td>
          <td><?= htmlspecialchars($r['product_code'] ?? '') ?></td>
          <td><?= htmlspecialchars($r['updated_at']) ?></td>
          <td>
                <a class="btn" 
                href="/FYP-25-S2-34-Chatbot/Src/assets/manuals/<?= rawurlencode(basename($r['manual_url'])) ?>" 
                target="_blank" 
                rel="noopener">
                Download
                </a>
          </td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>

  <?php if ($totalPages > 1): ?>
    <div class="pagination">
      <?php for ($p=1; $p<=$totalPages; $p++):
        $qs = http_build_query(['q'=>$q,'page'=>$p]); ?>
        <a href="?<?= htmlspecialchars($qs) ?>" class="<?= $p===$page?'active':'' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
