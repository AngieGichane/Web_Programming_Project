<?php
// Include database configuration
require_once 'db_config.php';

// Check if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$success = false;

// Process registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = sanitize($_POST["username"]);
    $email = sanitize($_POST["email"]);
    $password = $_POST["password"]; // Don't sanitize password before hashing
    $confirm_password = $_POST["confirm_password"];
    
    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
    } else {
        // Check if username already exists
        $conn = connectDB();
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username already exists";
        } else {
            // Attempt to register
            if (registerUser($username, $email, $password)) {
                $success = true;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
        
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        
        .success {
            color: #27ae60;
            margin-bottom: 20px;
            text-align: center;
            padding: 15px;
            background-color: #d4edda;
            border-radius: 5px;
            border-left: 4px solid #2ecc71;
            font-weight: bold;
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
        <h1>Register</h1> 
    </header>
    
    <div class="container">
        <h2>Create Account</h2>
        
        <?php if ($success): ?>
            <div class="success">
                Registration successful! <a href="index.php">Login here</a>
            </div>
        <?php else: ?>
            <?php if (!empty($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form id="registerForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required minlength="3">
                    <span id="username-error" class="error"></span>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                    <span id="email-error" class="error"></span>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required minlength="6">
                    <span id="password-error" class="error"></span>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <span id="confirm-password-error" class="error"></span>
                </div>
                
                <button type="submit" class="btn">Register</button>
            </form>
            
            <div class="nav-links">
                <p>Already have an account? <a href="index.php">Login</a></p>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Client-side validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            let isValid = true;
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('confirm_password').value.trim();
            
            // Reset error messages
            document.querySelectorAll('.error').forEach(el => el.textContent = '');
            
            // Validate username
            if (username === '') {
                document.getElementById('username-error').textContent = 'Username is required';
                isValid = false;
            } else if (username.length < 3) {
                document.getElementById('username-error').textContent = 'Username must be at least 3 characters';
                isValid = false;
            }
            
            // Validate email
            if (email === '') {
                document.getElementById('email-error').textContent = 'Email is required';
                isValid = false;
            } else if (!/^\S+@\S+\.\S+$/.test(email)) {
                document.getElementById('email-error').textContent = 'Invalid email format';
                isValid = false;
            }
            
            // Validate password
            if (password === '') {
                document.getElementById('password-error').textContent = 'Password is required';
                isValid = false;
            } else if (password.length < 6) {
                document.getElementById('password-error').textContent = 'Password must be at least 6 characters';
                isValid = false;
            }
            
            // Validate confirm password
            if (confirmPassword === '') {
                document.getElementById('confirm-password-error').textContent = 'Please confirm your password';
                isValid = false;
            } else if (password !== confirmPassword) {
                document.getElementById('confirm-password-error').textContent = 'Passwords do not match';
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>

