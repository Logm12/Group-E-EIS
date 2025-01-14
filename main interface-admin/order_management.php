<?php
// Kết nối đến cơ sở dữ liệu
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';

$overallStats = $con->prepare("
    SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as total_delivered,
        SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as delivered_revenue,
        SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as total_returned,
        SUM(CASE WHEN status = 'processing' THEN total_amount ELSE 0 END) as returned_cost,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as on_the_way,
        SUM(CASE WHEN status = 'pending' THEN total_amount ELSE 0 END) as on_the_way_cost
    FROM Orders 
    WHERE created_at BETWEEN '2025-01-10' AND '2025-01-17'
");
$overallStats->execute();
$overallStats = $overallStats->fetch(PDO::FETCH_ASSOC);

// Fetch orders list
$orders = $con->query("
    SELECT 
        p.name as product_name,
        od.price AS order_value,
        od.quantity,
        o.OrderID AS order_id,
        d.estimated_delivery_date,
        o.status
    FROM Orders o
    JOIN OrderDetail od ON o.OrderID = od.OrderID
    JOIN Product p ON od.ProductID = p.ProductID
    JOIN Delivery d ON o.OrderID = d.OrderID
    ORDER BY o.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <!-- Overall Orders Section -->
    <div class="overall-orders">
        <h2>Overall Orders</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="stat-value"><?php echo $overallStats['total_orders']; ?></div>
                <div class="stat-period">Last 7 days</div>
            </div>
            <div class="stat-card">
                <h3>Total Delivered</h3>
                <div class="stat-value"><?php echo $overallStats['total_delivered']; ?></div>
                <div class="stat-amount"><?php echo number_format($overallStats['delivered_revenue']); ?></div>
                <div class="stat-period">Last 7 days</div>
            </div>
            <div class="stat-card">
                <h3>Total Returned</h3>
                <div class="stat-value"><?php echo $overallStats['total_returned']; ?></div>
                <div class="stat-amount"><?php echo number_format($overallStats['returned_cost']); ?></div>
                <div class="stat-period">Last 7 days</div>
            </div>
            <div class="stat-card">
                <h3>On the way</h3>
                <div class="stat-value"><?php echo $overallStats['on_the_way']; ?></div>
                <div class="stat-amount"><?php echo number_format($overallStats['on_the_way_cost']); ?></div>
                <div class="stat-period">Ordered</div>
            </div>
        </div>
    </div>

<!-- Orders Section -->
<div class="orders-section">
    <div class="orders-header">
        <h2>Orders</h2>
        <div class="header-actions">
            <button class="btn btn-primary" id="addOrderBtn">Add Order</button>
            <button class="btn btn-outline">
                <i class="filter-icon"></i>
                Filters
            </button>
            <button class="btn btn-outline">Order History</button>
        </div>
    </div>

    <!-- Form to Add New Order -->
    <div id="addOrderForm" style="display: none; margin-top: 20px;">
        <h3>Add New Order</h3>
        <form action="" method="POST">
            <div class="form-group">
                <label for="customerID">Customer ID:</label>
                <input type="number" id="customerID" name="customerID" required class="form-control">
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <input type="text" id="status" name="status" required class="form-control">
            </div>
            <div class="form-group">
                <label for="totalAmount">Total Amount:</label>
                <input type="number" step="0.01" id="totalAmount" name="totalAmount" required class="form-control">
            </div>
            <div class="form-group">
                <label for="createdAt">Created At:</label>
                <input type="date" id="createdAt" name="createdAt" required class="form-control">
            </div>
            <div class="form-group">
                <label for="updatedAt">Updated At:</label>
                <input type="date" id="updatedAt" name="updatedAt" required class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Submit Order</button>
            <button type="button" class="btn btn-secondary" id="cancelOrderBtn">Cancel</button>
        </form>
    </div>

    <?php
    // Xử lý form khi được gửi
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        include 'connect.php'; // Kết nối đến cơ sở dữ liệu
    
        $customerID = $_POST['customerID'];
        $status = $_POST['status'];
        $totalAmount = $_POST['totalAmount'];
        $createdAt = $_POST['createdAt'];
        $updatedAt = $_POST['updatedAt'];
    
        // Thêm đơn hàng vào cơ sở dữ liệu
        $stmt = $con->prepare("INSERT INTO Orders (CustomerID, status, total_amount, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
        
        // Sử dụng bindValue để gán giá trị
        $stmt->bindValue(1, $customerID, PDO::PARAM_INT);
        $stmt->bindValue(2, $status, PDO::PARAM_STR);
        $stmt->bindValue(3, $totalAmount, PDO::PARAM_STR);
        $stmt->bindValue(4, $createdAt, PDO::PARAM_STR);
        $stmt->bindValue(5, $updatedAt, PDO::PARAM_STR);
    
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>New order added successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->errorInfo()[2] . "</div>";
        }
    
        $stmt->close();
        $con = null; // Đóng kết nối
    }
    ?>
</div>

<script>
    // JavaScript to toggle the visibility of the add order form
    document.getElementById('addOrderBtn').addEventListener('click', function() {
        document.getElementById('addOrderForm').style.display = 'block';
    });

    document.getElementById('cancelOrderBtn').addEventListener('click', function() {
        document.getElementById('addOrderForm').style.display = 'none';
    });
</script>

        <table class="orders-table">
            <thead>
                <tr>
                    <th>Products</th>
                    <th>Order Value</th>
                    <th>Quantity</th>
                    <th>Order ID</th>
                    <th>Estimated Delivery</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td><?php echo number_format($order['order_value']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?> Packets</td>
                        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                        <td><?php echo date('d/m/y', strtotime($order['estimated_delivery_date'])); ?></td>
                        <td>
                            <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="pagination">
            <button class="btn btn-outline">Previous</button>
            <div class="page-info">Page 1 of 10</div>
            <button class="btn btn-outline">Next</button>
        </div>
    </div>
</div>

<style>
.overall-orders {
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-top: 1rem;
}

.stat-card {
    padding: 1rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
    margin: 0.5rem 0;
}

.stat-amount {
    color: #666;
}

.stat-period {
    color: #888;
    font-size: 0.9rem;
}

.orders-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.header-actions {
    display: flex;
    gap: 1rem;
}

.orders-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
}

.orders-table th,
.orders-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
}

.status-badge.completed {
    background-color: #e3f2fd;
    color: #1976d2;
}

.status-badge.processing {
    background-color: #fff3e0;
    color: #f57c00;
}

.status-badge.pending {
    background-color: #e8f5e9;
    color: #388e3c;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
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
</style>

<?php
include 'Includes/templates/footer.php';
?>