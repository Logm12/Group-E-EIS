<?php
// Kết nối đến cơ sở dữ liệu
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';

// Fetch shipping orders
$shippingOrders = $con->query("
    SELECT 
        o.OrderID AS order_id,
        o.created_at AS date,
        o.status,
        o.total_amount,
        c.name AS client_name
    FROM Orders o
    JOIN Customer c ON o.CustomerID = c.CustomerID
    ORDER BY o.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Get shipping card details
$latestShipments = $con->query("
    SELECT 
        s.ShipmentID AS shipment_number,
        s.shipping_method AS category,
        s.tracking_number AS pickup_address,
        s.delivered_at AS delivery_address,
        c.name AS client_name
    FROM Shipment s
    JOIN Orders o ON s.OrderID = o.OrderID
    JOIN Customer c ON o.CustomerID = c.CustomerID
    ORDER BY s.shipped_at DESC
    LIMIT 2
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <!-- Shipping Cards -->
    <div class="shipping-cards">
        <?php foreach ($latestShipments as $shipment): ?>
            <div class="shipping-card">
                <div class="card-header">
                    <div>
                        <div class="shipment-number">Shipment number</div>
                        <div class="number"><?php echo htmlspecialchars($shipment['shipment_number']); ?></div>
                    </div>
                    <img src="truck-icon.jpg" alt="Truck" class="truck-icon">
                </div>
                <div class="addresses">
                    <div class="address">
                        <div class="dot green"></div>
                        <div><?php echo htmlspecialchars($shipment['pickup_address']); ?></div>
                    </div>
                    <div class="address">
                        <div class="dot purple"></div>
                        <div><?php echo htmlspecialchars($shipment['delivery_address']); ?></div>
                    </div>
                </div>
                <div class="client-info">
                    <img src="client-avatar.jpg" alt="Client" class="client-avatar">
                    <div>
                        <div class="client-name"><?php echo htmlspecialchars($shipment['client_name']); ?></div>
                    </div>
                    <div class="actions">
                        <button class="call-btn"><i class="phone-icon"></i></button>
                        <button class="share-btn"><i class="share-icon"></i></button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Shipping Table Header -->
    <div class="shipping-header">
        <h2>Shipping</h2>
        <div class="header-actions">
            <form action="export_orders.php" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-outline">Export to Excel</button>
            </form>
            <button class="btn btn-outline" data-toggle="modal" data-target="#addOrderModal">Add Order</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters">
        <input type="text" placeholder="Search order ID" class="search-input">
        <div class="filter-buttons">
            <select class="filter-select">
                <option>Sales</option>
            </select>
            <select class="filter-select">
                <option>Status</option>
            </select>
            <button class="filter-btn">Filter</button>
        </div>
    </div>

    <!-- Shipping Orders Table -->
    <table class="table">
        <thead>
            <tr>
                <th><input type="checkbox"></th>
                <th>Order ID</th>
                <th>Date</th>
                <th>Status</th>
                <th>Client Name</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbod>
            <?php foreach ($shippingOrders as $order): ?>
                <tr>
                    <td><input type="checkbox"></td>
                    <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo date('m/d/Y', strtotime($order['date'])); ?></td>
                    <td>
                        <span class="status-badge <?php echo strtolower($order['status']); ?>">
                            <?php echo htmlspecialchars($order['status']); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($order['client_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['total_amount']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Order Modal -->
<div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog" aria-labelledby="addOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addOrderModalLabel">Add Order</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="add_order.php">
                    <div class="form-group">
                        <label for="clientName">Client Name:</label>
                        <input type="text" name="clientName" id="clientName" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="orderDate">Order Date:</label>
                        <input type="date" name="orderDate" id="orderDate" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status:</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="Pending">Pending</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Delivered">Delivered</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="totalAmount">Total Amount:</label>
                        <input type="number" name="totalAmount" id="totalAmount" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'Includes/templates/footer.php';
?>