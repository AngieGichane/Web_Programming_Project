<?php
// Include database configuration
require_once 'db_config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Initialize variables for form data
$title = $content = $category = "";
$titleErr = $contentErr = $categoryErr = "";
$formSubmitted = false;

// Process form data when submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title (now full name)
    if (empty($_POST["title"])) {
        $titleErr = "Full name is required";
    } else {
        $title = sanitize($_POST["title"]);
    }
    
    // Validate content (now bio)
    if (empty($_POST["content"])) {
        $contentErr = "Bio is required";
    } else {
        $content = sanitize($_POST["content"]);
    }
    
    // Validate category (now location)
    if (empty($_POST["category"])) {
        $categoryErr = "Location is required";
    } else {
        $category = sanitize($_POST["category"]);
    }
    
    // Additional fields - future implementation can store these as JSON in content field
    $dob = isset($_POST["dob"]) ? sanitize($_POST["dob"]) : "";
    $address = isset($_POST["address"]) ? sanitize($_POST["address"]) : "";
    $comments = isset($_POST["comments"]) ? sanitize($_POST["comments"]) : "";
    
    // If no errors, proceed with form submission
    if (empty($titleErr) && empty($contentErr) && empty($categoryErr)) {
        // Create enhanced content with all information
        $enhancedContent = "Bio: $content\n\nDate of Birth: $dob\n\nAddress: $address\n\nComments: $comments";
        
        // Save form data using the updated function in db_config.php that expects 7 parameters
        if (saveFormData($_SESSION['id'], $title, $dob, $category, $address, $content, $comments)) {
            // Redirect to dashboard with success message
            header("Location: dashboard.php?success=1");
            exit;
        } else {
            $error = "Failed to submit information. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Information</title>
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
        }
        
        header {
            color: #fadbd8;
            padding: 15px 0;
            border: none;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 90%;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            padding: 8px 15px;
            border-radius: 20px;
        }
        
        .nav-links a:hover {
            background-color: var(--light-purple);
            text-decoration: none;
            box-shadow: 0 0 5px rgba(255,255,255,0.3);
        }
        
        .container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            padding: 25px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            border-left: 5px solid var(--highlight-purple);
        }
        
        h2 {
            color: var(--primary-purple);
            margin-top: 0;
            margin-bottom: 25px;
            text-align: center;
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
        input[type="date"],
        select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus, textarea:focus {
            border-color: var(--light-purple);
            outline: none;
            box-shadow: 0 0 8px rgba(155, 89, 182, 0.5);
        }
        
        textarea {
            height: 120px;
            resize: vertical;
        }
        
        .btn {
            background-color: var(--primary-purple);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
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
            font-size: 0.9em;
            margin-top: 5px;
            background-color: #fadbd8;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }
        
        /* Form section styling */
        .form-section {
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .form-section-title {
            color: var(--primary-purple);
            margin-bottom: 15px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <h1>Personal Information Form</h1> 
            </div>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a> 
            </div>
        </div>
    </header>
    
    <div class="container">
        <h2>Submit Your Information</h2>
        
        <?php if (isset($error)): ?>
            <div class="error" style="margin-bottom: 15px; text-align: center;"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form id="submissionForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="return validateForm()">
            <div class="form-section">
                <div class="form-section-title">Personal Details</div>
                
                <div class="form-group">
                    <label for="title">Full Name:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title); ?>" placeholder="Enter your full name">
                    <span class="error" id="titleError"><?php echo $titleErr; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" value="<?php echo isset($_POST['dob']) ? htmlspecialchars($_POST['dob']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-section-title">Location Information</div>
                
                <div class="form-group">
                    <label for="category">Location:</label>
                    <select id="category" name="category">
                        <option value="" <?php if (empty($category)) echo "selected"; ?>>Select your location</option>
                        <option value="North America" <?php if ($category == "North America") echo "selected"; ?>>North America</option>
                        <option value="South America" <?php if ($category == "South America") echo "selected"; ?>>South America</option>
                        <option value="Europe" <?php if ($category == "Europe") echo "selected"; ?>>Europe</option>
                        <option value="Asia" <?php if ($category == "Asia") echo "selected"; ?>>Asia</option>
                        <option value="Africa" <?php if ($category == "Africa") echo "selected"; ?>>Africa</option>
                        <option value="Australia/Oceania" <?php if ($category == "Australia/Oceania") echo "selected"; ?>>Australia/Oceania</option>
                    </select>
                    <span class="error" id="categoryError"><?php echo $categoryErr; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" placeholder="Enter your address"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-section-title">About You</div>
                
                <div class="form-group">
                    <label for="content">Bio:</label>
                    <textarea id="content" name="content" placeholder="Tell us about yourself"><?php echo htmlspecialchars($content); ?></textarea>
                    <span class="error" id="contentError"><?php echo $contentErr; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="comments">Additional Comments:</label>
                    <textarea id="comments" name="comments" placeholder="Any additional information you'd like to share"><?php echo isset($_POST['comments']) ? htmlspecialchars($_POST['comments']) : ''; ?></textarea>
                </div>
            </div>
            
            <div class="form-group" style="text-align: center; margin-top: 30px;">
                <button type="submit" class="btn">Submit Information</button>
            </div>
        </form>
    </div>
    
    <script>
        function validateForm() {
            let isValid = true;
            
            // Reset error messages
            document.getElementById("titleError").textContent = "";
            document.getElementById("contentError").textContent = "";
            document.getElementById("categoryError").textContent = "";
            
            // Validate full name
            const title = document.getElementById("title").value.trim();
            if (title === "") {
                document.getElementById("titleError").textContent = "Full name is required";
                isValid = false;
            } else if (title.length < 3) {
                document.getElementById("titleError").textContent = "Full name must be at least 3 characters";
                isValid = false;
            }
            
            // Validate bio
            const content = document.getElementById("content").value.trim();
            if (content === "") {
                document.getElementById("contentError").textContent = "Bio is required";
                isValid = false;
            } else if (content.length < 10) {
                document.getElementById("contentError").textContent = "Bio must be at least 10 characters";
                isValid = false;
            }
            
            // Validate location
            const category = document.getElementById("category").value;
            if (category === "") {
                document.getElementById("categoryError").textContent = "Please select a location";
                isValid = false;
            }
            
            return isValid;
        }
    </script>
</body>
</html>