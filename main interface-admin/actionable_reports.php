<?php
// Kết nối đến cơ sở dữ liệu
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';

// Định nghĩa ngưỡng thời gian lưu trữ (ví dụ: 30 ngày)
$thresholdDays = 30;
$thresholdDate = date('Y-m-d', strtotime("-$thresholdDays days"));

// Báo cáo xu hướng bán hàng
$salesTrendData = $con->query("
    SELECT p.name AS product_name, SUM(od.quantity) AS total_quantity_sold, SUM(od.price * od.quantity) AS total_sales_amount
    FROM OrderDetail od
    JOIN Orders o ON od.OrderID = o.OrderID
    JOIN Product p ON od.ProductID = p.ProductID  -- Thêm phép nối với bảng Product
    WHERE o.created_at >= '$thresholdDate'
    GROUP BY p.name
")->fetchAll(PDO::FETCH_ASSOC);

// Báo cáo hiệu suất tồn kho
$inventoryPerformanceData = $con->query("
    SELECT p.name AS product_name, i.current_stock_level, i.minimum_stock_level, i.optimal_stock_level
    FROM Inventory i
    JOIN Product p ON i.ProductID = p.ProductID
")->fetchAll(PDO::FETCH_ASSOC);

// Báo cáo dự đoán nhu cầu
$predictedDemandData = $con->query("
    SELECT p.name AS product_name, f.forecast_date, f.predicted_sales, f.predicted_revenue
    FROM ForecastResult f
    JOIN Product p ON f.ProductID = p.ProductID
    WHERE f.forecast_date >= '$thresholdDate'
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Sales Trends Report</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Total Quantity Sold</th>
                <th>Total Sales Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($salesTrendData as $sales): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sales['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($sales['total_quantity_sold']); ?></td>
                    <td><?php echo htmlspecialchars($sales['total_sales_amount']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h1>Inventory Performance Report</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Current Stock Level</th>
                <th>Minimum Stock Level</th>
                <th>Optimal Stock Level</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inventoryPerformanceData as $inventory): ?>
                <tr>
                    <td><?php echo htmlspecialchars($inventory['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($inventory['current_stock_level']); ?></td>
                    <td><?php echo htmlspecialchars($inventory['minimum_stock_level']); ?></td>
                    <td><?php echo htmlspecialchars($inventory['optimal_stock_level']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h1>Predicted Demand Report</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Forecast Date</th>
                <th>Predicted Sales</th>
                <th>Predicted Revenue</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($predictedDemandData as $forecast): ?>
                <tr>
                    <td><?php echo htmlspecialchars($forecast['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($forecast['forecast_date']); ?></td>
                    <td><?php echo htmlspecialchars($forecast['predicted_sales']); ?></td>
                    <td><?php echo htmlspecialchars($forecast['predicted_revenue']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include 'Includes/templates/footer.php';
?>