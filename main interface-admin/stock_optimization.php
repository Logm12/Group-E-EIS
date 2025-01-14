<?php
// Kết nối đến cơ sở dữ liệu
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';

// Lấy dữ liệu tồn kho hiện tại
$currentInventoryData = $con->query("
    SELECT p.ProductID, p.name AS product_name, i.current_stock_level, 
           i.minimum_stock_level, i.optimal_stock_level, 
           COALESCE(s.quantity_sold, 0) AS quantity_sold
    FROM Inventory i
    JOIN Product p ON i.ProductID = p.ProductID
    LEFT JOIN (
        SELECT ProductID, SUM(quantity) AS quantity_sold
        FROM SalesData
        WHERE sales_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)  -- Dữ liệu bán hàng trong 30 ngày qua
        GROUP BY ProductID
    ) s ON i.ProductID = s.ProductID
")->fetchAll(PDO::FETCH_ASSOC);

// Khởi tạo mảng để lưu thông tin đơn đặt hàng bổ sung
$restockOrders = [];

// Phân tích và tạo đơn đặt hàng bổ sung nếu cần
foreach ($currentInventoryData as $product) {
    // Tính toán nhu cầu dự báo (giả sử sử dụng số lượng bán hàng trung bình trong 30 ngày)
    $averageSales = $product['quantity_sold'];
    $forecastedDemand = $averageSales * 30; // Dự báo cho 30 ngày tới

    // So sánh mức tồn kho với nhu cầu dự báo
    if ($product['current_stock_level'] < $forecastedDemand) {
        // Tính toán số lượng cần bổ sung
        $replenishmentQuantity = $forecastedDemand - $product['current_stock_level'];

        // Tạo đơn đặt hàng bổ sung
        $stmt = $con->prepare("INSERT INTO RestockOrder (InventoryID, SupplierID, order_quantity, order_status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
        $stmt->execute([$product['ProductID'], /* SupplierID cần được xác định */ 1, $replenishmentQuantity]); // Giả sử SupplierID là 1

        // Cập nhật tồn kho
        $updateStmt = $con->prepare("UPDATE Inventory SET current_stock_level = current_stock_level + ? WHERE ProductID = ?");
        $updateStmt->execute([$replenishmentQuantity, $product['ProductID']]);

        // Lưu thông tin đơn đặt hàng vào mảng
        $restockOrders[] = [
            'product_name' => $product['product_name'],
            'replenishment_quantity' => $replenishmentQuantity
        ];
    }
}
?>

<div class="container">
    <h1>Inventory Optimization Process</h1>
    <p>The inventory optimization process has been executed. Below are the details of any new restock orders created:</p>

    <?php if (empty($restockOrders)): ?>
        <p>No restock orders were created. All stock levels are sufficient.</p>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Replenishment Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($restockOrders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($order['replenishment_quantity']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
include 'Includes/templates/footer.php';
?>