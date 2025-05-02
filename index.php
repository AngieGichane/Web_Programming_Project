<?php
// Include database configuration
require_once 'db_config.php';

// Check if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

// Process logout
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    // This will be handled by the logout.php file now
    header("Location: logout.php");
    exit;
}

$error = "";

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = sanitize($_POST["username"]);
    $password = $_POST["password"]; // Don't sanitize password before verification
    
    // Validate input
    if (empty($username) || empty($password)) {
        $error = "Username and password are required";
    } else {
        // Attempt to login
        if (loginUser($username, $password)) {
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        :root {
            --primary-purple: #6a0dad;
            --light-purple: #9b59b6;
            --dark-purple: #4a148c;
            --accent-purple: #e6e6fa;
            --highlight-purple: #8e44ad;
        }
        
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background: linear-gradient(90deg, #9370db, #6a5acd); 
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        header {
            color: #fadbd8;
            padding: 15px 0;
            text-align: center;
            border: none;
        }
        
        .container {
            width: 90%;
            max-width: 500px;
            margin: 30px auto;
            padding: 25px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            border-left: 5px solid var(--highlight-purple);
            flex-grow: 0;
        }
        
        h2 {
            color: var(--primary-purple);
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--dark-purple);
        }
        
        input[type="text"],
        input[type="password"],
        input[type="email"],
        select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus,
        select:focus, textarea:focus {
            border-color: var(--light-purple);
            outline: none;
            box-shadow: 0 0 5px rgba(155, 89, 182, 0.5);
        }
        
        .btn {
            background-color: var(--primary-purple);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .btn:hover {
            background-color: var(--dark-purple);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
            padding: 8px;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .nav-links {
            text-align: center;
            margin-top: 25px;
        }
        
        .nav-links a {
            color: var(--primary-purple);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        
        .nav-links a:hover {
            color: var(--highlight-purple);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <h1>Login</h1> 
    </header>
    
    <div class="container">
        <h2>Welcome Back</h2>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form id="loginForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <span id="username-error" class="error"></span>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <span id="password-error" class="error"></span>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="nav-links">
            <p>Don't have an account? <a href="register.php">Register</a></p>
        </div>
    </div>
    
    <script>
        // Client-side validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            let isValid = true;
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            // Reset error messages
            document.getElementById('username-error').textContent = '';
            document.getElementById('password-error').textContent = '';
            
            // Validate username
            if (username === '') {
                document.getElementById('username-error').textContent = 'Username is required';
                isValid = false;
            }
            
            // Validate password
            if (password === '') {
                document.getElementById('password-error').textContent = 'Password is required';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>