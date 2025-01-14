<?php
ob_start();
session_start();

$pageTitle = 'Customers';

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
        }
        vertical_menu.getElementsByClassName('clients_link')[0].className += " active_link";
    </script>

    <?php
    $do = 'Manage';

    if ($do == "Manage") {
        // Xử lý thêm khách hàng mới
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_customer'])) {
            $name = $_POST['name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $email = $_POST['email'];

            $stmt = $con->prepare("INSERT INTO Customer (name, phone_number, address, email, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $phone, $address, $email]);

            if ($stmt) {
                echo "<div class='alert alert-success'>Customer added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to add customer.</div>";
            }
        }

        // Xử lý xóa khách hàng
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_customer'])) {
            $customer_id = $_POST['customer_id'];

            $stmt = $con->prepare("DELETE FROM Customer WHERE CustomerID = ?");
            $stmt->execute([$customer_id]);

            if ($stmt) {
                echo "<div class='alert alert-success'>Customer deleted successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to delete customer.</div>";
            }
        }

        // Lấy danh sách khách hàng
        $stmt = $con->prepare("SELECT * FROM Customer");
        $stmt->execute();
        $clients = $stmt->fetchAll();
        ?>
        <div class="card">
            <div class="card-header">
                <?php echo $pageTitle; ?>
                <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addCustomerModal">Add Customer</button>
            </div>
            <div class="card-body">

                <!-- BẢNG KHÁCH HÀNG -->
                <table class="table table-bordered clients-table">
                    <thead>
                        <tr>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col">Address</th>
                            <th scope="col">E-mail</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($clients as $client) {
                            echo "<tr>";
                                echo "<td>" . htmlspecialchars($client['name']) . "</td>";
                                echo "<td>" . htmlspecialchars($client['phone_number']) . "</td>";
                                echo "<td>" . htmlspecialchars($client['address']) . "</td>";
                                echo "<td>" . htmlspecialchars($client['email']) . "</td>";
                                echo "<td>" . htmlspecialchars($client['created_at']) . "</td>";
                                echo "<td>
                                        <form action='' method='POST' style='display:inline;'>
                                            <input type='hidden' name='customer_id' value='" . $client['CustomerID'] . "'>
                                            <button type='submit' name='delete_customer' class='btn btn-danger btn-sm'>Delete</button>
                                        </form>
                                      </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>  
            </div>
        </div>

        <!-- Modal Thêm Khách Hàng -->
        <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCustomerModalLabel">Add New Customer</h5>
                        <button type="button" class="close" data -dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="name">Customer Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>
                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button type="submit" name="add_customer" class="btn btn-primary">Add Customer</button>
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