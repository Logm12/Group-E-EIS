<?php
// Kết nối đến cơ sở dữ liệu
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';

// Sử dụng thư viện php-ai/php-ml
require 'vendor/autoload.php'; // Đảm bảo bạn đã cài đặt php-ai/php-ml qua Composer
use Phpml\Regression\SVR;
use Phpml\SupportVectorMachine\Kernel;

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

// Dự đoán nhu cầu
function forecastDemand($con) {
    // Lấy dữ liệu bán hàng
    $salesData = $con->query("SELECT ProductID, SUM(quantity_sold) AS total_sold, sales_date FROM SalesData GROUP BY ProductID, sales_date")->fetchAll(PDO::FETCH_ASSOC);
    
    // Chuẩn bị dữ liệu cho mô hình
    $X = []; // Dữ liệu đầu vào
    $y = []; // Dữ liệu đầu ra

    foreach ($salesData as $data) {
        $X[] = [strtotime($data['sales_date'])]; // Chuyển đổi ngày thành timestamp
        $y[] = $data['total_sold'];
    }

    // Sử dụng mô hình SVR để dự đoán
    $regressor = new SVR(Kernel::RBF);
    $regressor->train($X, $y);

    // Dự đoán cho tương lai
    $futureDate = strtotime('2025-01-12'); // Ngày dự đoán
    $predictedSales = $regressor->predict([$futureDate]);

    return $predictedSales;
}

// Xử lý khi người dùng gửi biểu mẫu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (validateSalesData($con)) {
        $predictedSales = forecastDemand($con);
    } else {
        $errorMessage = "Invalid data found. Please check the logs for more details.";
    }
}
?>

<div class="container">
    <h1>AI-Powered Sales Forecasting</h1>
    
    <?php if (isset($errorMessage)) echo "<div class='alert alert-danger'>$errorMessage</div>"; ?>
    
    <form method="POST" action="">
        <button type="submit" class="btn btn-primary">Forecast Demand</button>
    </form>

    <?php if (isset($predictedSales)): ?>
        <h2>Predicted Sales for 2025-01-12</h2>
        <p><?php echo htmlspecialchars(number_format($predictedSales, 2)); ?> units</p>
    <?php endif; ?>
</div>

<?php
// Kết thúc mã PHP
include 'Includes/templates/footer.php'; 
?>