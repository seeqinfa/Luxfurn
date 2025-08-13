<?php
include '../../header.php';
require_once dirname(__DIR__, 2) . '/Controllers/Customer/viewFurnitureCtrl.php';

$controller = new FurnitureController();
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$itemsPerPage = 9;
$offset = ($currentPage - 1) * $itemsPerPage;

$totalItems = $controller->countFurniture($searchTerm);
$totalPages = ceil($totalItems / $itemsPerPage);
$furnitureList = $controller->getFurniturePaginated($offset, $itemsPerPage, $searchTerm);
?>

<div class="container" style="margin-top: 140px; max-width: 1200px; width: 100%; padding: 0 20px;">
	<?php
    // Display success message
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">';
        echo htmlspecialchars($_SESSION['success_message']);
        echo '</div>';
        unset($_SESSION['success_message']);
    }

    // Display error message
    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">';
        echo htmlspecialchars($_SESSION['error_message']);
        echo '</div>';
        unset($_SESSION['error_message']);
    }
    ?>
    <form method="GET" style="margin-bottom: 20px; display: flex; gap: 10px;">
        <input type="text" name="search" placeholder="Search by name or category"
               value="<?php echo htmlspecialchars($searchTerm); ?>"
               style="padding: 8px; flex: 1; border: 1px solid #ccc; border-radius: 4px;">
        <button type="submit" style="padding: 16px 20px; background-color: #e67e22; color: white; border: none; border-radius: 4px; cursor: pointer;">Search</button>
    </form>

    <div class="furniture-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        <?php foreach ($furnitureList as $item): ?>
            <div class="card" style="background: rgba(255,255,255,0.9); border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); overflow: hidden;">
                <img src="<?php echo htmlspecialchars($item->image_url); ?>" alt="<?php echo htmlspecialchars($item->name); ?>" style="width:100%; height:180px; object-fit:cover;">
                <div class="card-body" style="padding: 15px;">
                    <div class="category" style="font-size: 12px; color: gray;"><?php echo htmlspecialchars($item->category); ?></div>
                    <div class="name" style="font-size: 16px; font-weight: bold;"><?php echo htmlspecialchars($item->name); ?></div>
                    <div class="price" style="font-size: 16px; color: #e67e22;">$<?php echo htmlspecialchars($item->price); ?></div>
                    <div class="actions" style="display: flex; justify-content: center; gap: 10px; margin-top: 12px;">
                        <a href="viewFurnitureDetailsUI.php?id=<?php echo urlencode($item->furnitureID); ?>" class="btn">View</a>
                        <a href="../../Controllers/Customer/CartCtrl.php?action=add&id=<?php echo urlencode($item->furnitureID); ?>" class="btn">Add to Cart</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination" style="text-align: center; margin-top: 30px;">
        <?php if ($currentPage > 1): ?>
            <a href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $currentPage - 1; ?>">&laquo; Prev</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $i; ?>" class="<?php echo ($i == $currentPage) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($currentPage < $totalPages): ?>
            <a href="?search=<?php echo urlencode($searchTerm); ?>&page=<?php echo $currentPage + 1; ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
