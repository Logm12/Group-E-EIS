<?php 
session_start();
$pageTitle = 'User Login';

// Check if user is already logged in
if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
    header('Location: dashboard.php');
}

// Include required files
include 'connect.php';
include 'includes/functions/functions.php';
include 'includes/templates/header.php';
?>

<div class="login-container">
    <form class="login-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="return validateLoginForm()">
        <!-- Logo -->
        <div class="logo-container">
            <img src="Includes/templates/Shopee.png" alt="SHOPEE ORDER MANAGEMENT SYSTEM">
        </div>
        
        <h2>LOGIN</h2>

        <?php
        if (isset($_POST['login'])) {
            $username = test_input($_POST['username']);
            $password = test_input($_POST['password']);
            
            $stmt = $con->prepare("SELECT UserID, Username, Password FROM UserRolesPermissions WHERE Username = ? AND Password = ?");
            $stmt->execute(array($username, $password));
            $row = $stmt->fetch();
            $count = $stmt->rowCount();

            if ($count > 0) {
                $_SESSION['username'] = $username;
                $_SESSION['password'] = $password;
                $_SESSION['userid'] = $row['UserID'];
                header('Location: dashboard.php');
                exit();
            } else {
                echo '<div class="error-message">Invalid username or password!</div>';
            }
        }
        ?>

        <!-- Username Input -->
        <div class="form-group">
            <div class="input-with-icon">
                <i class="user-icon"></i>
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username"
                    class="form-control" 
                    oninput="hideError('username_error')"
                    required
                >
            </div>
            <span class="error-message" id="username_error"></span>
        </div>

        <!-- Password Input -->
        <div class="form-group">
            <div class="input-with-icon">
                <i class="password-icon"></i>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password"
                    class="form-control" 
                    oninput="hideError('password_error')"
                    required
                >
            </div>
            <span class="error-message" id="password_error"></span>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="form-options">
            <label class="remember-me">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <a href="forgot-password.php" class="forgot-password">Forgot password?</a>
        </div>

        <!-- Login Button -->
        <button type="submit" name="login" class="login-btn">Login</button>

        <!-- OR Divider -->
        <div class="divider">
            <span>OR</span>
        </div>

        <!-- Google Login -->
        <button type="button" class="google-btn">
            <img src="Includes/templates/Google.png" alt="Google">
            Continue with Google
        </button>

        <!-- Sign Up Link -->
        <p class="signup-link">
            Don't have an account? <a href="signup.php">Sign up here</a>
        </p>
    </form>
</div>

<style>
.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #ffe6e6;
    padding: 20px;
}

.login-form {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.logo-container {
    margin-bottom: 20px;
}

.logo-container img {
    max-width: 150px;
    height: auto;
}

h2 {
    margin-bottom: 30px;
    color: #333;
    font-weight: 500;
}

.form-group {
    margin-bottom: 20px;
}

.input-with-icon {
    position: relative;
    margin-bottom: 15px;
}

.form-control {
    width: 100%;
    padding: 10px 15px 10px 40px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    font-size: 14px;
}

.login-btn {
    width: 100%;
    padding: 12px;
    background: #333;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    margin-bottom: 20px;
}

.divider {
    position: relative;
    text-align: center;
    margin: 20px 0;
}

.divider span {
    background: white;
    padding: 0 10px;
    color: #666;
    font-size: 14px;
}

.google-btn {
    width: 100%;
    padding: 12px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
}

.google-btn img {
    width: 20px;
    height: 20px;
}

.signup-link {
    margin-top: 20px;
    font-size: 14px;
}

.signup-link a {
    color: #ee4d2d;
    text-decoration: none;
}

.error-message {
    color: #ff4d4f;
    font-size: 14px;
    margin-top: 5px;
    text-align: left;
}
</style>

<script>
function validateLoginForm() {
    let isValid = true;
    const username = document.querySelector('input[name="username"]');
    const password = document.querySelector('input[name="password"]');

    if (!username.value.trim()) {
        document.getElementById('username_error').textContent = 'Username is required!';
        isValid = false;
    }

    if (!password.value.trim()) {
        document.getElementById('password_error').textContent = 'Password is required!';
        isValid = false;
    }

    return isValid;
}

function hideError(elementId) {
    document.getElementById(elementId).textContent = '';
}
</script>

<?php include 'includes/templates/footer.php'; ?>