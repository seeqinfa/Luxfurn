<?php
include '../../header.php';
require_once dirname(__DIR__, 2) . '/Controllers/Admin/AdminRatingandReviewsCtrl.php';

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;

$ctrl = new AdminRatingandReivewsCtrl();
[$reviews, $totalRows] = $ctrl->list($page, $perPage);
$totalPages = max(1, (int)ceil($totalRows / $perPage));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chatbot Ratings & Reviews</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        .page { margin-top:140px; max-width:1200px; padding:0 20px; margin-left:auto; margin-right:auto; }
        .title { font-size:24px; margin:30px 0 16px; border-bottom:2px solid #eee; padding-bottom:8px; color:#333; }
        table { width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden; }
        th, td { padding:12px 14px; border-bottom:1px solid #eee; text-align:left; vertical-align:top; }
        th { background:#e67e22; color:#fff; }
        .meta { color:#666; font-size:12px; }
        .actions { display:flex; gap:6px; }
        .pagination { text-align:center; margin-top:14px; }
        .pagination a { display:inline-block; padding:8px 12px; margin:0 3px; background:#f4f4f4; color:#333; text-decoration:none; border-radius:4px; }
        .pagination a.active { background:#e67e22; color:#fff; }
        textarea { width:100%; min-height:60px; resize:vertical; }
        .btn { padding:6px 10px; border:none; border-radius:4px; cursor:pointer; }
        .btn-primary { background:#e67e22; color:#fff; }
        .btn-secondary { background:#9b59b6; color:#fff; }
    </style>
</head>
<body>
<div class="page">
    <h1 class="title">Chatbot Ratings & Reviews</h1>
    <div class="meta">Showing <?= count($reviews) ?> of <?= (int)$totalRows ?> total</div>

    <table>
        <thead>
            <tr>
                <th style="width:60px;">ID</th>
                <th style="width:180px;">User</th>
                <th style="width:80px;">Rating</th>
                <th>Comment</th>
                <th style="width:320px;">Admin Comment</th>
                <th style="width:160px;">Date</th>
                <th style="width:120px;">Delete</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!$reviews): ?>
            <tr><td colspan="7" style="text-align:center;color:#666;">No reviews found.</td></tr>
        <?php else: foreach ($reviews as $r): ?>
            <tr>
                <td>#<?= (int)$r['reviewID'] ?></td>
                <td><?= htmlspecialchars($r['username'] ?? ('User #' . (int)$r['user_id'])) ?>
                    <div class="meta">UID: <?= (int)$r['user_id'] ?></div>
                </td>
                <td><?= (int)$r['rating'] ?> ★</td>
                <td><?= nl2br(htmlspecialchars($r['comment'])) ?></td>
                <td>
                    <form method="post" action="../../Controllers/Admin/AdminRatingandReviewsCtrl.php">
                        <input type="hidden" name="action" value="save_admin_comment">
                        <input type="hidden" name="reviewID" value="<?= (int)$r['reviewID'] ?>">
                        <textarea name="admin_comment"><?= htmlspecialchars($r['admin_comment'] ?? '') ?></textarea>
                        <div style="margin-top:6px;">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
                <td>
                    <form method="post" action="../../Controllers/Admin/AdminRatingandReviewsCtrl.php" onsubmit="return confirm('Delete this review?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="reviewID" value="<?= (int)$r['reviewID'] ?>">
                        <button type="submit" class="btn btn-secondary">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($p = 1; $p <= $totalPages; $p++):
                $link = 'AdminRatingandReviewsUI.php?page=' . $p; ?>
                <a href="<?= htmlspecialchars($link) ?>" class="<?= $p===$page ? 'active' : '' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
