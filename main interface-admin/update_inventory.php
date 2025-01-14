<?php
// Kết nối đến cơ sở dữ liệu
include 'connect.php';
include 'Includes/functions/functions.php'; 
include 'Includes/templates/header.php';
include 'Includes/templates/navbar.php';
// Xử lý khi người dùng gửi biểu mẫu cập nhật tồn kho
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_stock'])) {
    $productId = $_POST['product_id'];
    $action = $_POST['action'];
    $quantity = (int)$_POST['quantity'];

    // Truy vấn để lấy thông tin sản phẩm
    $product = $con->query("SELECT * FROM Product WHERE ProductID = $productId")->fetch(PDO::FETCH_ASSOC);

    if ($action == 'add') {
        // Thêm số lượng tồn kho
        $newStockLevel = $product['stock_quantity'] + $quantity;
        $con->prepare("UPDATE Product SET stock_quantity = ? WHERE ProductID = ?")
            ->execute([$newStockLevel, $productId]);
        $message = "Stock updated successfully. New stock level: $newStockLevel.";
    } elseif ($action == 'reduce') {
        // Giảm số lượng tồn kho
        $newStockLevel = max(0, $product['stock_quantity'] - $quantity); // Đảm bảo không âm
        $con->prepare("UPDATE Product SET stock_quantity = ? WHERE ProductID = ?")
            ->execute([$newStockLevel, $productId]);
        $message = "Stock updated successfully. New stock level: $newStockLevel.";
    } elseif ($action == 'inactive') {
        // Đánh dấu SKU là không hoạt động
        $con->prepare("UPDATE Product SET status = 'inactive' WHERE ProductID = ?")
            ->execute([$productId]);
        $message = "Product marked as inactive.";
    }
}

// Xử lý khi người dùng gửi biểu mẫu thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $supplierId = $_POST['supplier_id'];
    $price = $_POST['price'];
    $productCode = $_POST['product_code'];
    $categoryId = $_POST['category_id'];
    $stockQuantity = $_POST['stock_quantity'];

    // Thêm sản phẩm mới vào cơ sở dữ liệu
    $con->prepare("INSERT INTO Product (name, description, SupplierID, price, product_code, CategoryID, stock_quantity, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())")
        ->execute([$name, $description, $supplierId, $price, $productCode, $categoryId, $stockQuantity]);
    $addMessage = "Product added successfully.";
}

// Truy vấn để lấy danh sách sản phẩm
$products = $con->query("SELECT * FROM Product")->fetchAll(PDO::FETCH_ASSOC);

// Truy vấn để lấy danh sách nhà cung cấp và danh mục
$suppliers = $con->query("SELECT * FROM Supplier")->fetchAll(PDO::FETCH_ASSOC);
$categories = $con->query("SELECT * FROM Category")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Update Inventory Information</h1>
    <?php if (isset($message)) echo "<div class='alert alert-success'>$message</div>"; ?>
    <?php if (isset($addMessage)) echo "<div class='alert alert-success'>$addMessage</div>"; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="product_id">Select SKU:</label>
            <select name="product_id" id="product_id" class="form-control" required>
                <option value="">Select a product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo $product['ProductID']; ?>">
                        <?php echo htmlspecialchars($product['name']); ?> (Current Stock: <?php echo $product['stock_quantity']; ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="action">Action:</label>
            <select name="action" id="action" class="form-control" required>
                <option value="">Select an action</option>
                <option value="add">Add Stock</option>
                <option value="reduce">Reduce Stock</option>
                <option value=" inactive">Mark as Inactive</option>
            </select>
        </div>

        <div class="form-group" id="quantity-group" style="display: none;">
            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1">
        </div>

        <button type="submit" name="update_stock" class="btn btn-primary">Update Inventory</button>
    </form>

    <h2>Add New Product</h2>
    <button class="btn btn-info" onclick="document.getElementById('addProductModal').style.display='block'">Add Product</button>

    <div id="addProductModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span onclick="document.getElementById('addProductModal').style.display='none'" class="close">&times;</span>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Product Name:</label>
                    <input type="text" name="name" id="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea name="description" id="description" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label for="supplier_id">Supplier:</label>
                    <select name="supplier_id" id="supplier_id" class="form-control" required>
                        <option value="">Select a supplier</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?php echo $supplier['SupplierID']; ?>">
                                <?php echo htmlspecialchars($supplier['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                </div>

                <div class="form-group">
                    <label for="product_code">Product Code:</label>
                    <input type="text" name="product_code" id="product_code" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Category:</label>
                    <select name="category_id" id="category_id" class="form-control" required>
                        <option value="">Select a category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['CategoryID']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="stock_quantity">Initial Stock Quantity:</label>
                    <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" min="0" value="0" required>
                </div>

                <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
            </form>
        </div>
    </div>

    <script>
        // Hiển thị trường nhập số lượng chỉ khi chọn hành động thêm hoặc giảm
        document.getElementById('action').addEventListener('change', function() {
            var quantityGroup = document.getElementById('quantity-group');
            if (this.value === 'add' || this.value === 'reduce') {
                quantityGroup.style.display = 'block';
            } else {
                quantityGroup.style.display = 'none';
            }
        });

        // Đóng modal khi nhấn ra ngoài
        window.onclick = function(event) {
            var modal = document.getElementById('addProductModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 80%; 
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>

<?php
include 'Includes/templates/footer.php';
?> 
