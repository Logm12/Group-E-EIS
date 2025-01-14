<?php include '../connect.php'; ?>
<?php include '../Includes/functions/functions.php'; ?>

<?php

	if(isset($_POST['do_']) && $_POST['do_'] == "Delete")
	{
		$warehouse_id = $_POST['WarehouseID']; 

        if (is_numeric($warehouse_id)) {
            $stmt = $con->prepare("DELETE FROM Warehouses WHERE WarehouseID = ?");
            $stmt->execute(array($warehouse_id));

            if ($stmt->rowCount() > 0) {
                echo json_encode(array("status" => "success", "message" => "Warehouse deleted successfully."));
            } else {
                echo json_encode(array("status" => "error", "message" => "Warehouse not found."));
            }
        } else {
            echo json_encode(array("status" => "error", "message" => "Invalid Warehouse ID."));
        }
	}
?>