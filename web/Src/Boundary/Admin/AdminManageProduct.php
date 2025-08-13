<?php
/* ----------  EARLY EXIT:  AJAX DELETE  ---------- */
require_once dirname(__DIR__, 2) . '/Controllers/Admin/AdminManageProductCtrl.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && ($_POST['action'] ?? '') === 'delete') {

    header('Content-Type: application/json');

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Invalid furniture ID']);
        exit;
    }

    $ctrl   = new AdminManageProductCtrl();
    $result = $ctrl->removeFurniture($id);

    echo json_encode($result);
    exit;              // stop before any HTML is emitted
}
/* ----------  END AJAX DELETE BLOCK  ---------- */
?>

<?php
/* ----------  PAGE SET-UP  ---------- */
include '../../header.php';

$controller  = new AdminManageProductCtrl();
$searchTerm  = trim($_GET['search'] ?? '');
$currentPage = max(1, intval($_GET['page'] ?? 1));
$itemsPerPage = 9;
$offset      = ($currentPage - 1) * $itemsPerPage;

$totalItems   = $controller->countFurniture($searchTerm);
$totalPages   = ceil($totalItems / $itemsPerPage);
$furnitureList = $controller->getFurniturePaginated($offset, $itemsPerPage, $searchTerm);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
</head>
<body>

<div class="container" style="margin-top:140px;max-width:1200px;width:100%;padding:0 20px;">
    <!-- Add Product button -->
    <div style="margin-bottom:15px;">
        <a href="AdminAddProduct.php"
           class="btn"
           style="padding:6px 12px;background:#2ecc71;color:#fff;border:none;border-radius:4px;font-size:14px">
           + Add Product
        </a>
    </div>

    <!-- Search -->
    <form method="GET" style="display:flex;gap:10px;margin-bottom:20px;">
        <input type="text" name="search" placeholder="Search by name or category"
               value="<?= htmlspecialchars($searchTerm) ?>"
               style="flex:1;padding:8px;border:1px solid #ccc;border-radius:4px">
        <button type="submit"
                style="padding:8px 16px;background:#e67e22;color:#fff;border:none;border-radius:4px">
            Search
        </button>
    </form>

    <!-- Table -->
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;background:rgba(255,255,255,.9);
                       border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,.2);">
            <thead>
                <tr style="background:#e67e22;color:#fff;">
                    <th style="padding:12px 15px;text-align:left;">Product Name</th>
                    <th style="padding:12px 15px;text-align:left;">Category</th>
                    <th style="padding:12px 15px;text-align:right;">Price</th>
                    <th style="padding:12px 15px;text-align:center;">Quantity Left</th>
                    <th style="padding:12px 15px;text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody id="furniture-table-body">
            <?php foreach ($furnitureList as $item): ?>
                <tr id="row-<?= $item->furnitureID ?>" style="border-bottom:1px solid #ddd;">
                    <td style="padding:12px 15px;"><?= htmlspecialchars($item->name) ?></td>
                    <td style="padding:12px 15px;"><?= htmlspecialchars($item->category) ?></td>
                    <td style="padding:12px 15px;text-align:right;color:#e67e22;font-weight:bold;">
                        $<?= htmlspecialchars($item->price) ?>
                    </td>
                    <td style="padding:12px 15px;text-align:center;">
                        <?php
                            echo isset($item->stock_quantity)
                                ? ($item->stock_quantity > 0 ? $item->stock_quantity : 'Out of Stock')
                                : 'N/A';
                        ?>
                    </td>
                    <td style="padding:12px 15px;text-align:center;">
                        <div style="display:flex;justify-content:center;gap:20px;">
                            <a href="AdminEditProduct.php?id=<?= urlencode($item->furnitureID) ?>"
                               class="btn" style="background:purple">Edit</a>

                            <button class="btn remove-btn"
                                    data-id="<?= $item->furnitureID ?>"
                                    data-name="<?= htmlspecialchars($item->name) ?>"
                                    style="background:red">
                                Remove
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination" style="text-align:center;margin-top:30px;">
        <?php if ($currentPage > 1): ?>
            <a href="?search=<?= urlencode($searchTerm) ?>&page=<?= $currentPage - 1 ?>">&laquo; Prev</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?search=<?= urlencode($searchTerm) ?>&page=<?= $i ?>"
               class="<?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($currentPage < $totalPages): ?>
            <a href="?search=<?= urlencode($searchTerm) ?>&page=<?= $currentPage + 1 ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>

<!-- ----------  JS: Remove via AJAX  ---------- -->
<script>
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.remove-btn');
    if (!btn) return;

    const id   = btn.dataset.id;
    const name = btn.dataset.name;

    if (!confirm(`Are you sure you want to remove "${name}"?`)) return;

    btn.disabled = true;
    btn.textContent = 'Removing…';

    try {
        const res = await fetch(window.location.href, {
            method : 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body   : new URLSearchParams({ action: 'delete', id })
        });

        if (!res.ok) throw new Error('Network error');
        const data = await res.json();

        if (data.success) {
            const row = document.getElementById('row-' + id);
            if (row) {
                row.style.transition = 'opacity .3s';
                row.style.opacity = '0';
                setTimeout(() => row.remove(), 300);
            }
        } else {
            alert(data.message || 'Delete failed');
            btn.disabled = false;
            btn.textContent = 'Remove';
        }
    } catch (err) {
        console.error(err);
        alert('An error occurred while removing the item');
        btn.disabled = false;
        btn.textContent = 'Remove';
    }
});
</script>

</body>
</html>
