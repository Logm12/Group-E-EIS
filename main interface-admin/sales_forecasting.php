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

// Phân tích xu hướng bán hàng
function analyzeSalesTrends($con) {
    // Phân tích sản phẩm bán chạy nhất
    $topSellingProducts = $con->query("
        SELECT p.name, SUM(sd.quantity_sold) AS total_sold
        FROM SalesData sd
        JOIN Product p ON sd.ProductID = p.ProductID
        GROUP BY p.ProductID
        ORDER BY total_sold DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Phân tích mẫu theo mùa
    $seasonalPatterns = $con->query("
        SELECT MONTH(sales_date) AS month, SUM(total_sales_amount) AS total_sales
        FROM SalesData
        GROUP BY MONTH(sales_date)
        ORDER BY month
    ")->fetchAll(PDO::FETCH_ASSOC);

    return [$topSellingProducts, $seasonalPatterns];
}

// Tích hợp AI để dự đoán doanh số
function predictSales($con) {
    // Truy vấn để lấy dữ liệu bán hàng
    $salesData = $con->query("SELECT sales_date, total_sales_amount FROM SalesData ORDER BY sales_date")->fetchAll(PDO::FETCH_ASSOC);
    
    // Chuyển đổi dữ liệu thành định dạng phù hợp cho mô hình AI
    $dates = [];
    $sales = [];
    foreach ($salesData as $data) {
        $dates[] = $data['sales_date'];
        $sales[] = $data['total_sales_amount'];
    }

    // Giả sử bạn đã có một mô hình AI đã được huấn luyện và lưu trữ
    // Ở đây, bạn có thể gọi một API hoặc một mô hình đã được huấn luyện để dự đoán doanh số
    // Ví dụ: sử dụng một API RESTful để dự đoán
    $predictedSales = callAIPredictionAPI($dates, $sales);
    
    return $predictedSales;
}

// Hàm gọi API dự đoán AI (giả định)
function callAIPredictionAPI($dates, $sales) {
    // Gọi API và trả về dự đoán (đây chỉ là một ví dụ)
    // Bạn cần thay thế bằng mã thực tế để gọi API của mô hình AI
    return [
        'predicted_sales' => [100, 150, 200, 250, 300], // Dữ liệu giả định
        'confidence' => 0.95 // Độ tin cậy giả định
    ];
}

// Xử lý khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (validateSalesData($con)) {
        list($topSellingProducts, $seasonalPatterns) = analyzeSalesTrends($con);
        $predictedSalesData = predictSales($con);
    } else {
        $errorMessage = "Invalid data found. Please check the logs for more details.";
    }
}
?>

<div class="container">
    <h1>AI-Powered Sales Forecasting</h1>
    
    <?php if (isset($errorMessage)) echo "<div class='alert alert-danger'>$errorMessage</div>"; ?>
    
    <form method="POST" action="">
        <button type="submit" class="btn btn-primary">Analyze Sales Data</button>
    </form>

    <?php if (isset($topSellingProducts )): ?>
        <h2>Top-Selling Products</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Total Sold</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topSellingProducts as $product): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['total_sold']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (isset($seasonalPatterns)): ?>
        <h2>Seasonal Patterns</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($seasonalPatterns as $pattern): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pattern['month']); ?></td>
                        <td><?php echo htmlspecialchars($pattern['total_sales']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if (isset($predictedSalesData)): ?>
        <h2>Predicted Sales</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Predicted Sales</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($predictedSalesData['predicted_sales'] as $index => $predictedSale): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(date('F', mktime(0, 0, 0, $index + 1, 1))); ?></td>
                        <td><?php echo htmlspecialchars($predictedSale); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p>Confidence Level: <?php echo htmlspecialchars($predictedSalesData['confidence'] * 100) . '%'; ?></p>
    <?php endif; ?>
</div>

<?php
include 'Includes/templates/footer.php';
?>