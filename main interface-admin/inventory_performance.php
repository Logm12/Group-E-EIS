<?php
// Kết nối đến cơ sở dữ liệu
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';

// Kiểm tra dữ liệu
function validateSalesData($con) {
    // Truy vấn để lấy dữ liệu bán hàng
    $salesData = $con->query("SELECT * FROM SalesData")->fetchAll(PDO::FETCH_ASSOC);
    
    // Kiểm tra tính toàn vẹn và nhất quán của dữ liệu
    foreach ($salesData as $data) {
        if (empty($data['ProductID']) || empty($data['quantity_sold']) || empty($data['total_sales_amount'])) {
            // Ghi log lỗi
            $con->prepare("INSERT INTO Log (event_type, description, created_at) VALUES ('data_validation', 'Invalid data found for SalesData ID: {$data['SalesDataID']}', NOW())")->execute();
            return false; // Dữ liệu không hợp lệ
        }
    }
    return true; // Dữ liệu hợp lệ
}

// Phân tích hiệu suất tồn kho
function analyzeInventoryPerformance($con) {
    // Tính toán tỷ lệ quay vòng
    $turnoverRates = $con->query("
        SELECT p.name, 
               SUM(sd.quantity_sold) / AVG(i.current_stock_level) AS turnover_rate
        FROM SalesData sd
        JOIN Inventory i ON sd.ProductID = i.ProductID
        JOIN Product p ON sd.ProductID = p.ProductID
        GROUP BY p.ProductID
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Tính toán chi phí lưu kho
    $carryingCosts = $con->query("
        SELECT p.name, 
               (i.current_stock_level * p.price * 0.1) AS carrying_cost
        FROM Inventory i
        JOIN Product p ON i.ProductID = p.ProductID
    ")->fetchAll(PDO::FETCH_ASSOC);

    return [$turnoverRates, $carryingCosts];
}

// Xử lý khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (validateSalesData($con)) {
        list($turnoverRates, $carryingCosts) = analyzeInventoryPerformance($con);
    } else {
        $errorMessage = "Invalid data found. Please check the logs for more details.";
    }
}
?>

<div class="container">
    <h1>AI-Powered Sales Forecasting and Inventory Performance Analysis</h1>
    
    <?php if (isset($errorMessage)) echo "<div class='alert alert-danger'>$errorMessage</div>"; ?>
    
    <form method="POST" action="">
        <button type="submit" class="btn btn-primary">Analyze Sales Data</button>
    </form>

    <?php if (isset($turnoverRates)): ?>
        <h2>Inventory Turnover Rates</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Turnover Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($turnoverRates as $rate): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rate['name']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($rate['turnover_rate'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (isset($carryingCosts)): ?>
        <h2>Carrying Costs</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Carrying Cost</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carryingCosts as $cost): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cost['name']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($cost['carrying_cost'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<? php
// Kết thúc mã PHP
include 'Includes/templates/footer.php'; 
?>