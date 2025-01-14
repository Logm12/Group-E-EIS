<?php
ob_start();
session_start();

$pageTitle = 'Users';

if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    include 'connect.php';
    include 'Includes/functions/functions.php'; 
    include 'Includes/templates/header.php';
    include 'Includes/templates/navbar.php';

    $do = isset($_GET['do']) ? $_GET['do'] : 'Manage';
    ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
        var vertical_menu = document.getElementById("vertical-menu");
        var current = vertical_menu.getElementsByClassName("active_link");

        if (current.length > 0) {
            current[0].classList.remove("active_link");   
        }
        
        vertical_menu.getElementsByClassName('users_link')[0].className += " active_link";
    </script>

    <?php
if ($do == "Manage") {
    // Fetch users from the database
    $stmt = $con->prepare("SELECT UserID, username, created_at, role 
    FROM UserRolesPermissions");
    $stmt->execute();
    $users = $stmt->fetchAll();

    // Fetch user count by role
    $roleCountStmt = $con->prepare("SELECT role, COUNT(UserID) as UserCount 
    FROM UserRolesPermissions 
    GROUP BY role");
    $roleCountStmt->execute();
    $roleCounts = $roleCountStmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><?php echo $pageTitle; ?></span>
            <a href="users.php?do=Add" class="btn btn-primary btn-sm">Add User</a>
        </div>
        <div class="card-body">
            <table class="table table-bordered users-table">
                <thead>
                    <tr>
                        <th scope="col">Username</th>
                        <th scope="col">Role</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($users)) {
                        foreach ($users as $user) {
                            echo "<tr>";
                            echo "<td>" . $user['username'] . "</td>";
                            echo "<td>" . $user['role'] . "</td>";
                            echo "<td>" . $user['created_at'] . "</td>";
                            echo "<td>";
                            echo "<a href='users.php?do=Edit&user_id=" . $user['UserID'] . "' class='btn btn-success btn-sm rounded-0' style='color: white;'>";
                            echo "<i class='fa fa-edit'></i>";
                            echo "</a> ";
                            echo "<a href='users.php?do=Delete&user_id=" . $user['UserID'] . "' class='btn btn-danger btn-sm rounded-0' style='color: white;' onclick='return confirm(\"Are you sure you want to delete this user?\");'>";
                            echo "<i class='fa fa-trash'></i>";
                            echo "</a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

<!-- Chart Section -->
<div class="chart-container mt-4">
    <h2>User Role Distribution</h2>
    <canvas id="roleDistributionChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('roleDistributionChart').getContext('2d');

        // Lấy dữ liệu từ PHP
        const roleLabels = <?php echo json_encode(array_column($roleCounts, 'role')); ?>;
        const roleData = <?php echo json_encode(array_column($roleCounts, 'User Count')); ?>;

        // Tạo biểu đồ
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: roleLabels,
                datasets: [{
                    label: 'User  Count by Role',
                    data: roleData,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(201, 203, 207, 0.2)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(201, 203, 207, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
    <?php
} elseif ($do == 'Add') {
    ?>
    <div class="card">
        <div class="card-header">
            Add User
        </div>
        <div class="card-body">
            <form method="POST" action="users.php?do=Add">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="role">Role:</label>
                    <select name="role" id="role" class="form-control">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="customer">Customer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" name="add_user_sbmt" class="btn btn-primary">Add User</button>
            </form>
        </div>
    </div>
    <?php

    if (isset($_POST['add_user_sbmt']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_name = test_input($_POST['username']);
        $role = test_input($_POST['role']);
        $user_password = sha1(test_input($_POST['password']));

        $stmt = $con->prepare("INSERT INTO UserRolesPermissions (username, password, role, created_at) VALUES (?, ?, ?, CURDATE())");
        $stmt->execute(array($user_name, $user_password, $role));

        echo "<script>
            swal('Add User', 'User  has been added successfully', 'success')
            .then(() => {
                window.location.replace('users.php');
            });
        </script>";
        exit();
    }
} elseif ($do == 'Edit') {
    $user_id = (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) ? intval($_GET['user_id']) : 0;

    if ($user_id) {
        $stmt = $con->prepare("SELECT UserID, username, role 
        FROM UserRolesPermissions WHERE UserID = ?");
        $stmt->execute(array($user_id));
        $user = $stmt->fetch();

        if ($user) {
            ?>
            <div class="card">
                <div class="card-header">
                    Edit User
                </div>
                <div class="card-body">
                    <form method="POST" action="users.php?do=Edit&user_id=<?php echo $user['User ID']; ?>">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" name="username" id="username" class="form-control" value="<?php echo $user['username']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role:</label>
                            <select name="role" id="role" class="form-control">
                                <option value="">Select Role</option>
                                <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="manager" <?php echo ($user['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
                                <option value="customer" <?php echo ($user['role'] == 'customer') ? 'selected' : ''; ?>>Customer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password (leave blank to keep unchanged):</label>
                            <input type="password" name="user_password" id="password" class="form-control">
                        </div>
                        <button type="submit" name="edit_user_sbmt" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
            <?php
        } else {
            echo "User  not found.";
        }
    } else {
        header('Location: users.php');
        exit();
    }

    if (isset($_POST['edit_user_sbmt']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_name = test_input($_POST['username']);
        $role = test_input($_POST['role']);
        $user_password = $_POST['user_password'] ?? '';

        if (empty($user_password)) {
            $stmt = $con->prepare("UPDATE UserRolesPermissions SET username = ?, role = ?, created_at = CURDATE() WHERE UserID = ?");
            $stmt->execute(array($user_name, $role, $user_id));
        } else {
            $user_password = sha1($user_password);
            $stmt = $con->prepare("UPDATE UserRolesPermissions SET username = ?, password = ?, role = ?, created_at = CURDATE() WHERE UserID = ?");
            $stmt->execute(array($user_name, $user_password, $role, $user_id));
        }

        echo "<script>
            swal('Edit User', 'User  has been updated successfully', 'success')
            .then(() => {
                window.location.replace('users.php');
            });
        </script>";
        exit();
    }

} elseif ($do == 'Delete') {
    $user_id = (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) ? intval($_GET['user_id']) : 0;

    if ($user_id) {
        $stmt = $con->prepare("DELETE FROM UserRolesPermissions WHERE UserID = ?");
        $stmt->execute(array($user_id));

        echo "<script>
            swal('Delete User', 'User  has been deleted successfully', 'success')
            .then(() => {
                window.location.replace('users.php');
            });
        </script>";
        exit();
    } else {
        header('Location: users.php');
        exit();
    }
}

include 'Includes/templates/footer.php';
} else {
    header('Location: index.php');
    exit();
}
