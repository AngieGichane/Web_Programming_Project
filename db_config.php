<?php
// Include database configuration
require_once 'db_config.php';

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'mayaswan');
define('DB_NAME', 'web_app');

// Create database connection
function connectDB() {
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Sanitize input data to prevent XSS
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Register new user
function registerUser($username, $email, $password) {
    $conn = connectDB();
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Check login credentials
function loginUser($username, $password) {
    $conn = connectDB();
    
    // Use prepared statement
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Start session
            session_start();
            
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Set cookie for persistent login (30 days)
            setcookie('user_id', $user['id'], time() + (86400 * 30), "/");
            
            $stmt->close();
            $conn->close();
            return true;
        }
    }
    
    $stmt->close();
    $conn->close();
    return false;
}

// Save form data - Updated to handle personal information
function saveFormData($user_id, $full_name, $date_of_birth, $location, $address, $bio, $comments) {
    $conn = connectDB();
    
    // Use prepared statement
    $stmt = $conn->prepare("INSERT INTO form_data (user_id, full_name, date_of_birth, location, address, bio, comments) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $full_name, $date_of_birth, $location, $address, $bio, $comments);
    
    $result = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    
    return $result;
}

// Get user's form submissions - Updated to match new database structure
function getUserForms($user_id) {
    $conn = connectDB();
    
    // Use prepared statement with new column names
    $stmt = $conn->prepare("SELECT id, user_id, full_name, date_of_birth, location, address, bio, comments, submit_date 
                          FROM form_data 
                          WHERE user_id = ? 
                          ORDER BY submit_date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $forms = array();
    while ($row = $result->fetch_assoc()) {
        $forms[] = $row;
    }
    
    $stmt->close();
    $conn->close();
    
    return $forms;
}

// Check if user is logged in
function isLoggedIn() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// Logout user
function logoutUser() {
    // Initialize the session if not already started
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Unset all session variables
    $_SESSION = array();
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Delete persistent login cookie
    setcookie('user_id', '', time() - 3600, "/");
}
?>