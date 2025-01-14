<?php
	
	//Start session
    session_start();

    //Set page title
    $pageTitle = 'Dashboard';

    //PHP INCLUDES
    include 'connect.php';
    include 'Includes/functions/functions.php'; 
    include 'Includes/templates/header.php';

    //TEST IF THE SESSION HAS BEEN CREATED BEFORE

    if (isset($_SESSION['username']) && isset($_SESSION['password']))
    {
    	include 'Includes/templates/navbar.php';

    	?>

            <script type="text/javascript">

                var vertical_menu = document.getElementById("vertical-menu");


                var current = vertical_menu.getElementsByClassName("active_link");

                if(current.length > 0)
                {
                    current[0].classList.remove("active_link");   
                }
                
                vertical_menu.getElementsByClassName('dashboard_link')[0].className += " active_link";

            </script>

<!-- TOP 4 CARDS -->
<div class="row">
    <div class="col-lg-6">
        <canvas id="salesChart" width="400" height="200"></canvas>
    </div>
    <div class="col-lg-6">
        <canvas id="ordersChart" width="400" height="200"></canvas>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
 // Biểu đồ cho Total Sales
var ctxSales = document.getElementById('salesChart').getContext('2d');
var salesChart = new Chart(ctxSales, {
    type: 'line', // Dạng biểu đồ cho Total Sales
    data: {
        labels: ['10/01/2025', '11/01/2025', '12/01/2025', '13/01/2025', '14/01/2025'], // Nhãn cho từng ngày
        datasets: [{
            label: 'Total Sales',
            data: [
                <?php 
                // Fetch total sales data for each day from 10/01/2025 to 14/01/2025
                $salesData = $con->query("
                    SELECT SUM(total_sales_amount) 
                    FROM SalesData 
                    WHERE sales_date BETWEEN '2025-01-10' AND '2025-01-14' 
                    GROUP BY DATE(sales_date)
                ")->fetchAll(PDO::FETCH_COLUMN);
                
                // Đảm bảo có 5 giá trị cho 5 ngày
                $salesData = array_pad($salesData, 5, 0); // Thêm 0 cho những ngày không có dữ liệu
                echo implode(", ", $salesData); 
                ?>
            ],
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

    // Biểu đồ cho Total Orders
    var ctxOrders = document.getElementById('ordersChart').getContext('2d');
    var ordersChart = new Chart(ctxOrders, {
        type: 'bar', // Dạng biểu đồ cho Total Orders
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'], // Thay đổi theo dữ liệu thực tế
            datasets: [{
                label: 'Total Orders',
                data: [
                    <?php 
                    // Fetch total orders data for each month
                    $ordersData = $con->query("SELECT COUNT(OrderID) FROM Orders GROUP BY MONTH(created_at)")->fetchAll(PDO::FETCH_COLUMN);
                    echo implode(", ", $ordersData); 
                    ?>
                ],
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<div class="row">
    <div class="col-sm-6 col-lg-3">
        <div class="panel panel-green ">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-3">
                        <i class="fa fa-users fa-4x"></i>
                    </div>
                    <div class="col-sm-9 text-right">
                        <div class="huge"><span><?php echo countItems("CustomerID","Customer")?></span></div>
                        <div>Total Customers</div>
                    </div>
                </div>
            </div>
            <a href="customers.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-3">
                        <i class="fas fa-utensils fa-4x"></i>
                    </div>
                    <div class="col-sm-9 text-right">
                        <div class="huge"><span><?php echo countItems("WarehouseId","Warehouse")?></span></div>
                        <div>Total Warehouses</div>
                    </div>
                </div>
            </div>
            <a href="warehouses.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    
    <div class="col-sm-6 col-lg-3">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-3">
                        <i class="far fa-calendar-alt fa-4x"></i>
                    </div>
                    <div class="col-sm-9 text-right">
                        <div class="huge"><span><?php echo countItems("UserID","UserRolesPermissions")?></span></div>
                        <div>Total Users</div>
                    </div>
                </div>
            </div>
            <a href="users.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-sm-3">
                        <i class="fas fa-pizza-slice fa-4x"></i>
                    </div>
                    <div class="col-sm-9 text-right">
                        <div class="huge"><span><?php echo countItems("OrderID","Orders")?></span></div>
                        <div>Total Orders</div>
                    </div>
                </div>
            </div>
            <a href="order_management.php">
                <div class="panel-footer">
                    <span class="pull-left">View Details</span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>
 <!-- START ORDERS TABS -->

<div class="card" style="margin: 20px 10px">

<!-- TABS BUTTONS -->

<div class="card-header tab" style="padding:0px;">
    <button class="tablinks_orders active" onclick="openTab(event, 'recent_orders','tabcontent_orders','tablinks_orders')">Recent Orders</button>
    <button class="tablinks_orders" onclick="openTab(event, 'completed_orders','tabcontent_orders','tablinks_orders')">Completed Orders</button>
    <button class="tablinks_orders" onclick="openTab(event, 'canceled_orders','tabcontent_orders','tablinks_orders')">Canceled Orders</button>
</div>

<!-- TABS CONTENT -->

<div class="card-body">
    <div class='responsive-table'>

        <!-- RECENT ORDERS -->

        <table class="table X-table tabcontent_orders" id="recent_orders" style="display:table">
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Customer</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $stmt = $con->prepare("
                SELECT 
                    O.created_at AS created_at, 
                    O.OrderID AS order_id,
                    C.CustomerID AS customer_id,  
                    C.name AS customer_name,        
                    C.phone_number AS customer_phone, 
                    C.email AS customer_email,      
                    SUM(OD.quantity) AS total_quantity, 
                    SUM(OD.quantity * OD.price) AS total_price
                FROM 
                    Orders O
                JOIN 
                    OrderDetail OD ON O.OrderID = OD.OrderID
                JOIN 
                    Customer C ON O.CustomerID = C.CustomerID
                WHERE 
                    O.status IN ('pending', 'completed', 'canceled')
                GROUP BY 
                    O.OrderID, C.CustomerID 
                ORDER BY 
                    O.created_at DESC;
                ");

                $stmt->execute();
                $Orders = $stmt->fetchAll();
                $count = $stmt->rowCount();

                if ($count == 0) {
                    echo "<tr>";
                    echo "<td colspan='5' style='text-align:center;'>";
                    echo "List of your recent orders will be presented here";
                    echo "</td>";
                    echo "</tr>";
                } else {
                    foreach ($Orders as $order) {
                        echo "<tr>";
                        echo "<td>";
                        echo $order['created_at']; 
                        echo "</td>";
                        echo "<td>";

                        $stmtMenus = $con->prepare("
                            SELECT 
                                P.name AS product_name,  
                                OD.quantity AS quantity, 
                                OD.price AS menu_price 
                            FROM 
                                OrderDetail OD
                            JOIN 
                                Product P ON OD.ProductID = P.ProductID 
                            WHERE 
                                OD.OrderID = ?
                        ");
                        $stmtMenus->execute(array($order['order_id'])); 
                        $menus = $stmtMenus->fetchAll();

                        $total_price = 0;

                        foreach ($menus as $menu) {
                            echo "<span style='display:block'>".$menu['product_name']." - ".$menu['quantity']." x ".$menu['menu_price']."$</span>";
                            $total_price += ($menu['menu_price'] * $menu['quantity']);
                        }

                        echo "</td>";
                        echo "<td>";
                        echo $total_price."$";
                        echo "</td>";
                        echo "<td>";
                        ?>
                        <button class="btn btn-info btn-sm rounded-0" type="button" data-toggle="modal" data-target="#customer_<?php echo $order['customer_id']; ?>" data-placement="top">
                            <?php echo $order['customer_id']; ?>
                        </button>
                        <!-- Customer Modal -->
                        <div class="modal fade" id="customer_<?php echo $order['customer_id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Customer Details</h5> 
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <ul>
                                            <?php
                                            $customerStmt = $con->prepare("SELECT * FROM Customer WHERE CustomerID = ?");
                                            $customerStmt-> execute([$order['customer_id']]);
                                            $customer = $customerStmt->fetch();

                                            if ($customer) {
                                                echo "<li><span style='font-weight: bold;'>Full name: </span> " . htmlspecialchars($customer['name']) . "</li>"; 
                                                echo "<li><span style='font-weight: bold;'>Phone number: </span>" . htmlspecialchars($customer['phone_number']) . "</li>"; 
                                                echo "<li><span style='font-weight: bold;'>E-mail: </span>" . htmlspecialchars($customer['email']) . "</li>"; 
                                            } else {
                                                echo "<li>No customer details found.</li>";
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
echo "</td>";

echo "<td>";

$cancel_data = "cancel_order" . $order["order_id"];
$deliver_data = "deliver_order" . $order["order_id"];
?>
<ul class="list-inline m-0">
    <!-- Deliver Order BUTTON -->
    <li class="list-inline-item" data-toggle="tooltip" title="Deliver Order">
        <button class="btn btn-info btn-sm rounded-0" type="button" data-toggle="modal" data-target="#<?php echo $deliver_data; ?>" data-placement="top">
            <i class="fas fa-truck"></i>
        </button>

        <!-- DELIVER MODAL -->
        <div class="modal fade" id="<?php echo $deliver_data; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $deliver_data; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Deliver Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Mark order as delivered?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" data-id="<?php echo $order['order_id']; ?>" class="btn btn-info deliver_order_button">
                            Yes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </li>
    <!-- CANCEL BUTTON -->
    <li class="list-inline-item" data-toggle="tooltip" title="Cancel Order">
        <button class="btn btn-danger btn-sm rounded-0" type="button" data-toggle="modal" data-target="#<?php echo $cancel_data; ?>" data-placement="top">
            <i class="fas fa-calendar-times"></i>
        </button>

        <!-- CANCEL MODAL -->
        <div class="modal fade" id="<?php echo $cancel_data; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $cancel_data; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancel Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Cancellation Reason</label>
                            <textarea class="form-control" id="cancellation_reason_order_<?php echo $order['order_id']; ?>" required="required"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                        <button type="button" data-id="<?php echo $order['order_id']; ?>" class="btn btn-danger cancel_order_button">
                            Cancel Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </li>
</ul>
<?php
echo "</td>";
echo "</tr>";
}
}
?>

                            </tbody>
                        </table>

<!-- COMPLETED ORDERS -->

<table class="table X-table tabcontent_orders" id="completed_orders">
    <thead>
        <tr>
            <th>Order Date</th>
            <th>Payment Method</th>
            <th>Customer</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>

        <?php
        $stmt = $con->prepare("SELECT O.created_at AS OrderDate, P.payment_method AS PaymentMethod, C.CustomerID, C.name AS CustomerName 
                                FROM Orders O 
                                JOIN Payment P ON O.OrderID = P.OrderID
                                JOIN Customer C ON O.CustomerID = C.CustomerID
                                WHERE O.status = 'completed'
                                ORDER BY O.created_at;");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $count = $stmt->rowCount();

        if ($count == 0) {
            echo "<tr>";
            echo "<td colspan='4' style='text-align:center;'>";
            echo "List of your completed orders will be presented here";
            echo "</td>";
            echo "</tr>";
        } else {
            foreach ($rows as $order) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($order['OrderDate']) . "</td>";
                echo "<td>" . htmlspecialchars($order['PaymentMethod']) . "</td>";
                echo "<td>" . htmlspecialchars($order['CustomerName']) . "</td>";
                echo "<td>";
                echo "<button class='btn btn-info btn-sm' data-toggle='modal' data-target='#CustomerModal_" . $order['CustomerID'] . "'>View Customer</button>";
                echo "</td>";
                echo "</tr>";

                echo "<div class='modal fade' id='CustomerModal_" . $order['CustomerID'] . "' tabindex='-1' role='dialog' aria-labelledby='CustomerModalLabel' aria-hidden='true'>";
                echo "<div class='modal-dialog' role='document'>";
                echo "<div class='modal-content'>";
                echo "<div class='modal-header'>";
                echo "<h5 class='modal-title' id='CustomerModalLabel'>Customer Information</h5>";
                echo "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>";
                echo "<span aria-hidden='true'>&times;</span>";
                echo "</button>";
                echo "</div>";
                echo "<div class='modal-body'>";

                $customerStmt = $con->prepare("SELECT * FROM Customer WHERE CustomerID = ?");
                $customerStmt->execute([$order['CustomerID']]);
                $customer = $customerStmt->fetch();

                echo "<p><strong>Name:</strong> " . htmlspecialchars($customer['name']) . "</p>";
                echo "<p><strong>Phone Number:</strong> " . htmlspecialchars($customer['phone_number']) . "</p>";
                echo "<p><strong>Email:</strong> " . htmlspecialchars($customer['email']) . "</p>";
                echo "<p><strong>Address:</strong> " . htmlspecialchars($customer['address']) . "</p>";


                echo "</div>";
                echo "<div class='modal-footer'>";
                echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        }
        ?>
    </tbody>
</table>

<!-- Pending ORDERS -->

<table class="table X-table tabcontent_orders" id="canceled_orders">
    <thead>
        <tr>
            <th>Order Date</th>
            <th>Customer</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>

        <?php
        $stmt = $con->prepare("SELECT O.created_at AS OrderDate, C.CustomerID, C.name AS CustomerName, O.status 
                                FROM Orders O 
                                JOIN Customer C ON O.CustomerID = C.CustomerID
                                WHERE O.status = 'pending'
                                ORDER BY O.created_at;");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $count = $stmt->rowCount();

        if ($count == 0) {
            echo "<tr>";
            echo "<td colspan='3' style='text-align:center;'>";
            echo "List of your canceled orders will be presented here";
            echo "</td>";
            echo "</tr>";
        } else {
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['OrderDate']) . "</td>";
                echo "<td>" . htmlspecialchars($row['CustomerName']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "</tr>";
            }
        }
        ?>
    </tbody>
</table>

<!-- END ORDERS TABS -->

<?php
include 'Includes/templates/footer.php';

} else {
    header("Location: index.php");
    exit();
}
?>

<!-- JS SCRIPTS -->

<script type="text/javascript">
    
    // WHEN DELIVER ORDER BUTTON IS CLICKED
    $('.deliver_order_button').click(function() {
        var order_id = $(this).data('id');
        var do_ = 'Deliver_Order';

        $.ajax({
            url: "ajax_files/dashboard_ajax.php",
            type: "POST",
            data: { do_: do_, order_id: order_id },
            success: function(data) {
                $('#deliver_order' + order_id).modal('hide');
                swal("Order Delivered", "The order has been marked as delivered", "success").then((value) => {
                    window.location.replace("dashboard.php");
                });
            },
            error: function(xhr, status, error) {
                alert('AN ERROR HAS OCCURRED WHILE TRYING TO PROCESS YOUR REQUEST!');
            }
        });
    });

    // WHEN CANCEL ORDER BUTTON IS CLICKED
    $('.cancel_order_button').click(function() {
        var order_id = $(this).data('id');
        var cancellation_reason_order = $('#cancellation_reason_order_' + order_id).val();
        var do_ = 'Cancel_Order';

        if (!cancellation_reason_order) {
            alert('Please provide a cancellation reason.');
            return;
        }

        $.ajax({
            url: "ajax_files/dashboard_ajax.php",
            type: "POST",
            data: { order_id: order_id, cancellation_reason: cancellation_reason_order, do_: do_ },
            success: function(data) {
                $('#cancel_order' + order_id).modal('hide');
                swal("Order Canceled", "The order has been canceled successfully", "success").then((value) => {
                    window.location.replace("dashboard.php");
                });
            },
            error: function(xhr, status, error) {
                alert('AN ERROR HAS OCCURRED WHILE TRYING TO PROCESS YOUR REQUEST!');
            }
        });
    });

</script>