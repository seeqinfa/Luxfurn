<?php
require_once '../../header.php';
require_once dirname(__DIR__, 2) . '/Entities/furniture.php';
require_once dirname(__DIR__, 2) . '/Controllers/Customer/viewFurnitureCtrl.php';
require_once dirname(__DIR__, 2) . '/config.php';

// Get ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid furniture ID.";
    exit;
}

$id = intval($_GET['id']);
$controller = new FurnitureController();
$item = $controller->getFurnitureById($id);

if (!$item) {
    echo "Furniture item not found.";
    exit;
}
?>

<style>
.details-container {
    max-width: 900px;
    margin: 140px auto 40px;
    padding: 20px;
    background-color: #fdfdfd;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    border-radius: 10px;
    display: flex;
    gap: 30px;
}

.details-container img {
    max-width: 400px;
    border-radius: 8px;
    object-fit: cover;
}

.details-info {
    flex: 1;
}

.details-info h2 {
    margin-top: 0;
    color: #e67e22;
}

.details-info p {
    margin: 10px 0;
}

.price {
    font-size: 24px;
    color: #e67e22;
    margin: 10px 0;
}

.btn-back {
    margin-top: 20px;
    display: inline-block;
    background-color: #e67e22;
    color: white;
    padding: 16px 16px;
    border-radius: 5px;
    text-decoration: none;
}

.btn-back:hover {
    background-color: #c15500;
}
</style>

<div class="details-container">
    <img src="<?php echo htmlspecialchars($item->image_url); ?>" alt="<?php echo htmlspecialchars($item->name); ?>">
    <div class="details-info">
        <h2><?php echo htmlspecialchars($item->name); ?></h2>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($item->category); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($item->description); ?></p>
        <p><strong>Stock:</strong> <?php echo (int)$item->stock_quantity; ?> available</p>
        <div class="price">$<?php echo htmlspecialchars($item->price); ?></div>
        <a href="viewFurnitureUI.php" class="btn-back">← Back to List</a>
    </div>
</div>
