<?php
// Kết nối đến cơ sở dữ liệu
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';


// Truy vấn để lấy thông tin sản phẩm có tồn kho thấp
$lowStockData = $con->query("
    SELECT p.name AS name, i.current_stock_level, i.optimal_stock_level 
    FROM Inventory i
    JOIN Product p ON i.ProductID = p.ProductID
    WHERE i.current_stock_level < i.optimal_stock_level
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Low Stock SKUs</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Current Stock Level</th>
                <th>Optimal Stock Level</th>
                <th>Status</th>
                <th>Recommendations</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lowStockData as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['current_stock_level']); ?></td>
                    <td><?php echo htmlspecialchars($product['optimal_stock_level']); ?></td>
                    <td>
                        <?php 
                        // Xác định trạng thái khẩn cấp
                        if ($product['current_stock_level'] <= $product['optimal_stock_level'] * 0.2) {
                            echo "Restock Now.";
                        } elseif ($product['current_stock_level'] <= $product['optimal_stock_level'] * 0.5) {
                            echo "Restock Soon.";
                        } else {
                            echo "Sufficient Stock.";
                        }
                        ?>
                    </td>
                    <td>
                        <?php 
                        // Gợi ý bổ sung hàng tồn kho
                        if ($product['current_stock_level'] <= $product['optimal_stock_level'] * 0.2) {
                            echo "Order immediately.";
                        } elseif ($product['current_stock_level'] <= $product['optimal_stock_level'] * 0.5) {
                            echo "Consider ordering soon.";
                        } else {
                            echo "No action needed.";
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include 'Includes/templates/footer.php';
?>