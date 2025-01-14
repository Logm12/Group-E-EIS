<?php
ob_start();
session_start();

$pageTitle = 'Inventory Management';

if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    include 'connect.php';
    include 'Includes/functions/functions.php'; 
    include 'Includes/templates/header.php';
    include 'Includes/templates/navbar.php';

    $do = '';

    if (isset($_GET['do']) && in_array(htmlspecialchars($_GET['do']), array('Add', 'Edit', 'Delete', 'Report'))) {
        $do = $_GET['do'];
    } else {
        $do = 'Manage';
    }

    if ($do == "Manage") {
        $stmt = $con->prepare("
            SELECT WarehouseID, name AS WarehouseName, location AS Location, created_at AS CreatedAt 
            FROM Warehouse
        ");
        $stmt->execute();
        $warehouses = $stmt->fetchAll();
        ?>
        <div class="card">
            <div class="card-header">
                <?php echo $pageTitle; ?>
            </div>
            <div class="card-body">
                <div class="above-table" style="margin-bottom: 1rem!important;">
                    <a href="warehouses.php?do=Add" class="btn btn-success">
                        <i class="fa fa-plus"></i> 
                        <span>Add new Warehouse</span>
                    </a>
                </div>
                <div class="above-table" style="margin-bottom: 1rem!important;">
                    <button onclick="generateReport()" class="btn btn-info">
                        <i class="fa fa-file"></i> 
                        <span>Generate Inventory Report</span>
                    </button>
                </div>
                <table class="table table-bordered warehouses-table">
                    <thead>
                        <tr>
                            <th scope="col">Warehouse Name</th>
                            <th scope="col">Location</th>
                            <th scope="col">Created At</th>
                            <th scope="col">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($warehouses as $warehouse) {
                            echo "<tr>";
                                echo "<td>" . htmlspecialchars($warehouse['WarehouseName']) . "</td>";
                                echo "<td>" . htmlspecialchars($warehouse['Location']) . "</td>";
                                echo "<td>" . htmlspecialchars($warehouse['CreatedAt']) . "</td>";
                                echo "<td>";
                                    ?>
                                    <a href="warehouses.php?do=Edit&warehouse_id=<?php echo $warehouse['WarehouseID']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a href="warehouses.php?do=Delete&warehouse_id=<?php echo $warehouse['WarehouseID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this warehouse?');">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                    <?php
                                echo "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>  
            </div>
        </div>
        <?php
    } elseif ($do == 'Delete') {
        $warehouse_id = (isset($_GET['warehouse_id']) && is_numeric($_GET['warehouse_id'])) ? intval($_GET['warehouse_id']) : 0;
    
        if ($warehouse_id) {
            try {
                $stmt = $con->prepare("DELETE FROM Warehouse WHERE WarehouseID = ?");
                $stmt->execute(array($warehouse_id));
                header('Location: warehouses.php?message=deleted');
                exit();
            } catch (Exception $e) {
                echo 'Error occurred: ' . $e->getMessage();
            }
        } else {
            header('Location: warehouses.php');
            exit();
        }
    } elseif ($do == 'Report') {
        // Report logic can be added here if needed
    } elseif ($do == 'Add') {
        ?>
        <div class="card">
            <div class="card-header">
                Add New Warehouse
            </div>
            <div class="card-body">
                <form method="POST" class="warehouse_form" action="warehouses.php?do=Add" enctype="multipart/form-data">
                    <div class="panel-X">
                        <div class="panel-header-X">
                            <div class="main-title">
                                Add New Warehouse
                            </div>
                        </div>
                        <div class="save-header-X">
                            <div style="display:flex">
                                <div class="icon">
                                    <i class="fa fa-sliders-h"></i>
                                </div>
                                <div class="title-container">Warehouse details</div>
                            </div>
                            <div class="button-controls">
                                <button type="submit" name="add_new_warehouse" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                        <div class="panel-body-X">
                            <!-- WAREHOUSE NAME INPUT -->
                            <div class="form-group">
                                <label for="warehouse_name">Warehouse Name</label>
                                <input type="text" class="form-control" onkeyup="this.value=this.value.replace(/[^\sa-zA-Z]/g,'');" value="<?php echo (isset($_POST['warehouse_name'])) ? htmlspecialchars($_POST['warehouse_name']) : '' ?>" placeholder="Warehouse Name" name="warehouse_name" required>
                            </div>

                            <!-- LOCATION INPUT -->
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" class="form-control" value="<?php echo (isset($_POST['location'])) ? htmlspecialchars($_POST['location']) : '' ?>" placeholder="Location" name="location" required>
                            </div>

                            <!-- CONTACT NUMBER INPUT -->
                            <div class="form-group">
                                <label for="contact_number">Contact Number</label>
                                <input type="text" class="form-control" value="<?php echo (isset($_POST['contact_number'])) ? htmlspecialchars($_POST['contact_number']) : '' ?>" placeholder="Contact Number" name="contact_number" required>
                            </div>

                            <!-- MANAGER NAME INPUT -->
                            <div class="form-group">
                                <label for="manager_name">Manager Name</label>
                                <input type="text" class="form-control" value="<?php echo (isset($_POST['manager_name'])) ? htmlspecialchars($_POST['manager_name']) : '' ?>" placeholder="Manager Name" name="manager_name" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
        if (isset($_POST['add_new_warehouse']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $warehouse_name = test_input($_POST['warehouse_name']);
            $location = test_input($_POST['location']);
            $contact_number = test_input($_POST['contact_number']);
            $manager_name = test_input($_POST['manager_name']);
        
            try {
                $stmt = $con->prepare("INSERT INTO Warehouse (name, location, contact_number, manager_name, created_at) VALUES (?, ?, ?, ?, CURDATE())");
                $stmt->execute(array($warehouse_name, $location, $contact_number, $manager_name));

                ?>
                <script type="text/javascript">
                    swal("New Warehouse", "The new warehouse has been inserted successfully", "success").then((value) => {
                        window.location.replace("warehouses.php"); 
                    });
                </script>
                <?php

            } catch (Exception $e) {
                echo 'Error occurred: ' . $e->getMessage();
            }
        }
    }

} else {
    header('Location: warehouses.php');
    exit();
}

include 'Includes/templates/footer.php';
?>

<script type="text/javascript">
function generateReport() {
    window.location.href = "export_report.php";
}
</script>