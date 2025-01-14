<?php
ob_start();
session_start();

$pageTitle = 'Orders';

if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    include 'connect.php';
    include 'Includes/functions/functions.php'; 
    include 'Includes/templates/header.php';
    include 'Includes/templates/navbar.php';
    ?>

    <script type="text/javascript">
        var vertical_menu = document.getElementById("vertical-menu");
        var current = vertical_menu.getElementsByClassName("active_link");

        if (current.length > 0) {
            current[0].classList.remove("active_link");   
        vertical_menu.getElementsByClassName('orders_link')[0].className += " active_link";
    </script>

    <?php
    $do = 'Manage';

    if ($do == "Manage") {
        // Xử lý thêm đơn hàng mới
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_order'])) {
            $customer_id = $_POST['customer_id'];
            $order_date = $_POST['order_date'];
            $status = $_POST['status'];
            $payment_method = $_POST['payment_method'];
            $delivery_date = $_POST['delivery_date'];
            $shipping_address = $_POST['shipping_address'];

            $stmt = $con->prepare("INSERT INTO Orders (CustomerID, OrderDate, Status, PaymentMethod, DeliveryDate, ShippingAddress) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$customer_id, $order_date, $status, $payment_method, $delivery_date, $shipping_address]);

            if ($stmt) {
                echo "<div class='alert alert-success'>Order added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to add order.</div>";
            }
        }

        // Xử lý xóa đơn hàng
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_order'])) {
            $order_id = $_POST['order_id'];

            $stmt = $con->prepare("DELETE FROM Orders WHERE OrderID = ?");
            $stmt->execute([$order_id]);

            if ($stmt) {
                echo "<div class='alert alert-success'>Order deleted successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to delete order.</div>";
            }
        }

        // Lấy danh sách đơn hàng
        $stmt = $con->prepare("SELECT o.OrderID, c.Name AS CustomerName, o.OrderDate, o.Status, o.PaymentMethod, o.DeliveryDate, o.ShippingAddress FROM Orders o JOIN Customers c ON o.CustomerID = c.CustomerID");
        $stmt->execute();
        $orders = $stmt->fetchAll();
        ?>
        <div class="card">
            <div class="card-header">
                <?php echo $pageTitle; ?>
                <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addOrderModal">Add Order</button>
            </div>
            <div class="card-body">

                <!-- BẢNG ĐƠN HÀNG -->
                <table class="table table-bordered orders-table">
                    <thead>
                        <tr>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Order Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Payment Method</th>
                            <th scope="col">Delivery Date</th>
                            <th scope="col">Shipping Address</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($orders as $order) {
                            echo "<tr>";
                                echo "<td>" . htmlspecialchars($order['CustomerName']) . "</td>";
                                echo "<td>" . htmlspecialchars($order['OrderDate']) . "</td>";
                                echo "<td>" . htmlspecialchars($order['Status']) . "</td>";
                                echo "<td>" . htmlspecialchars($order['PaymentMethod']) . "</td>";
                                echo "<td>" . htmlspecialchars($order['DeliveryDate']) . "</td>";
                                echo "<td>" . htmlspecialchars($order['ShippingAddress']) . "</td>";
                                echo "<td>
                                        <form action='' method='POST' style='display:inline;'>
                                            <input type='hidden' name='order_id' value='" . $order['OrderID'] . "'>
                                            <button type='submit' name='delete_order' class='btn btn-danger btn-sm'>Delete</button>
                                        </form>
                                      </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>  
            </div>
        </div>

        <!-- Modal Thêm Đơn Hàng -->
        <div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog" aria-labelledby="addOrderModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addOrderModalLabel">Add New Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="customer_id">Customer</label>
                                <select class="form-control" id="customer_id" name="customer_id" required>
                                    <?php
                                    // Lấy danh sách khách hàng để chọn
                                    $stmt = $con->prepare("SELECT CustomerID, Name FROM Customers");
                                    $stmt->execute();
                                    $customers = $stmt->fetchAll();
                                    foreach ($customers as $customer) {
                                        echo "<option value='" . $customer['CustomerID'] . "'>" . htmlspecialchars($customer['Name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="order_date">Order Date</label>
                                <input type="date" class="form-control" id="order_date" name="order_date" required>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Shipped">Shipped</option>
                                    <option value="Delivered">Delivered</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="payment_method">Payment Method</label>
                                <input type="text" class="form-control" id="payment_method" name="payment_method" required>
                            </div>
                            <div class="form-group">
                                <label for="delivery_date">Delivery Date</label>
                                <input type="date" class="form-control" id="delivery_date" name="delivery_date" required>
                            </div>
                            <div class="form-group">
                                <label for="shipping_address">Shipping Address</label>
                                <input type="text" class="form-control" id="shipping_address" name="shipping_address" required>
                            </div>
                            <button type="submit" name="add_order" class="btn btn-primary">Add Order</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <?php
    }

    /* FOOTER BOTTOM */
    include 'Includes/templates/footer.php';

} else {
    header('Location: index.php');
    exit();
}
?>
