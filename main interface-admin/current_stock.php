<?php
// Database connection
include 'connect.php';
include 'Includes/functions/functions.php';
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';

// Fetch stock levels for all SKUs
$stockLevels = $con->query("SELECT 
    p.ProductID, 
    p.name AS product_name, 
    i.current_stock_level, 
    i.minimum_stock_level, 
    i.last_restock_date,
    w.WarehouseId,
    w.name AS warehouse_name,
    w.location,
    w.contact_number,
    w.manager_name
FROM Inventory i
JOIN Product p ON i.ProductID = p.ProductID
JOIN Warehouse w ON i.WarehouseID = w.WarehouseId
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch data for stock value chart
$stockValues = $con->query("SELECT 
    p.name AS product_name,
    SUM(i.current_stock_level) AS total_quantity
FROM Inventory i
JOIN Product p ON i.ProductID = p.ProductID
GROUP BY p.ProductID
ORDER BY p.name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <!-- Stock Levels Section -->
    <div class="stock-section">
        <h2>Current Stock Levels</h2>
        <table class="stock-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Current Stock Level</th>
                    <th>Reorder Point</th>
                    <th>Last Restocked</th>
                    <th>Warehouse</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stockLevels as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['current_stock_level']); ?></td>
                        <td><?php echo htmlspecialchars($item['minimum_stock_level']); ?></td>
                        <td><?php echo htmlspecialchars($item['last_restock_date']); ?></td>
                        <td><?php echo htmlspecialchars($item['warehouse_name']); ?></td>
                        <td>
                            <button class="btn btn-outline" onclick="showWarehouseInfo(<?php echo $item['WarehouseId']; ?>)">Warehouse Detail</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button class="btn btn-primary" onclick="addProduct()">Add Product</button>
    </div>

    <!-- Stock Value Chart Section -->
    <div class="stock-value-section">
        <h2>Stock Value by Product</h2>
        <div class="chart-container">
            <canvas id="stockValueChart"></canvas>
        </div>
    </div>
</div>

<style>
.container {
    padding: 2rem;
}

.stock-section {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.stock-table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.stock-table th,
.stock-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    margin-right: 0.5rem;
}

.btn-primary {
    background: #1976d2;
    color: white;
    border: none;
}

.btn-outline {
    background: white;
    border: 1px solid #ddd;
    color: #666;
}

.chart-container {
    height: 400px;
    margin-top: 1rem;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('stockValueChart').getContext('2d');

    const stockData = <?php echo json_encode($stockValues, JSON_NUMERIC_CHECK); ?>;

    const labels = stockData.map(item => item.product_name);
    const data = stockData.map(item => item.total_quantity);

    // Mảng màu sắc cho từng sản phẩm
    const colors = [
        'rgba(75, 192, 192, 0.2)',
        'rgba(255, 99, 132, 0.2)',
        'rgba(255, 206, 86, 0.2)',
        'rgba(54, 162, 235, 0.2)',
        'rgba(153, 102, 255, 0.2)',
        'rgba(255, 159, 64, 0.2)',
        'rgba(201, 203, 207, 0.2)',
        'rgba(255, 99, 132, 0.2)',
        'rgba(75, 192, 192, 0.2)',
        'rgba(54, 162, 235, 0.2)'
    ];

    // Tạo mảng màu sắc cho dữ liệu
    const backgroundColors = colors.slice(0, labels.length);
    const borderColors = colors.slice(0, labels.length).map(color => color.replace('0.2', '1'));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Stock Quantity',
                data: data,
                backgroundColor: backgroundColors,
                borderColor: borderColors,
                borderWidth: 1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
function showWarehouseInfo(warehouseId) {
    // Implement the logic to show full warehouse information based on warehouseId
    alert('Showing full warehouse information for Warehouse ID: ' + warehouseId);
}

function addProduct() {
    // Implement the logic to add a new product
    alert('Adding a new product');
}
</script>

<?php
include 'Includes/templates/footer.php';
?>