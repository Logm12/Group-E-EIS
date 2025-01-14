<?php
ob_start();
session_start();

$pageTitle = 'Users';

if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    include 'connect.php';
    include 'Includes/functions/functions.php';
    include 'Includes/templates/header.php';
    include 'Includes/templates/navbar.php';

    function analytics($con)
    {
        try {
            $stmtUsers = $con->prepare("SELECT COUNT(*) AS TotalUsers FROM Users");
            $stmtUsers->execute();
            $totalUsers = $stmtUsers->fetch()['TotalUsers'];

            $stmtRoles = $con->prepare("
                SELECT r.RoleName, COUNT(u.UserID) AS RoleCount 
                FROM Roles r 
                LEFT JOIN Users u ON r.RoleID = u.RoleID 
                GROUP BY r.RoleName
            ");
            $stmtRoles->execute();
            $rolesData = $stmtRoles->fetchAll();

            $stmtDaily = $con->prepare("
                SELECT CreatedAtDate, COUNT(UserID) AS DailyCount 
                FROM Users 
                GROUP BY CreatedAtDate 
                ORDER BY CreatedAtDate DESC 
                LIMIT 7
            ");
            $stmtDaily->execute();
            $dailyData = $stmtDaily->fetchAll();

            echo '<div class="card">';
            echo '    <div class="card-header">User Analytics</div>';
            echo '    <div class="card-body">';

            echo '        <h5>Total Users: ' . htmlspecialchars($totalUsers) . '</h5>';

            echo '        <h5>User Count by Role:</h5>';
            echo '        <ul>';
            foreach ($rolesData as $role) {
                echo '            <li>' . htmlspecialchars($role['RoleName']) . ': ' . htmlspecialchars($role['RoleCount']) . '</li>';
            }
            echo '        </ul>';

            echo '        <h5>Daily Registrations (Last 7 Days):</h5>';
            echo '        <table class="table table-bordered">';
            echo '            <thead>';
            echo '                <tr>';
            echo '                    <th>Date</th>';
            echo '                    <th>Registrations</th>';
            echo '                </tr>';
            echo '            </thead>';
            echo '            <tbody>';
            foreach ($dailyData as $daily) {
                echo '                <tr>';
                echo '                    <td>' . htmlspecialchars($daily['CreatedAtDate']) . '</td>';
                echo '                    <td>' . htmlspecialchars($daily['DailyCount']) . '</td>';
                echo '                </tr>';
            }
            echo '            </tbody>';
            echo '        </table>';

            echo '    </div>';
            echo '</div>';
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    analytics($con);

    include 'Includes/templates/footer.php';
} else {
    header('Location: index.php');
    exit();
}
?>
