<?php
// Include database configuration
require_once 'db_config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Get user ID and forms data
$user_id = $_SESSION['id'];
$username = $_SESSION['username'];
$forms = getUserForms($user_id);

// Check for form submission success message
$success_message = '';
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success_message = "Information submitted successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            color: #fadbd8;
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
            max-width: 1000px;
            margin: 30px auto;
        }
        
        .welcome-box {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            margin-bottom: 25px;
            border-left: 5px solid var(--highlight-purple);
        }
        
        .welcome-box h2 {
            color: var(--primary-purple);
            margin-top: 0;
        }
        
        .forms-box {
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            border-left: 5px solid var(--light-purple);
        }
        
        .forms-box h2 {
            color: var(--primary-purple);
            margin-top: 0;
        }
        
        .form-item {
            border-bottom: 1px solid #eee;
            padding: 18px 0;
            transition: background-color 0.3s ease;
        }
        
        .form-item:hover {
            background-color: var(--accent-purple);
            padding-left: 10px;
            border-radius: 5px;
        }
        
        .form-item:last-child {
            border-bottom: none;
        }
        
        .form-title {
            font-size: 18px;
            font-weight: bold;
            color: var(--primary-purple);
            margin-bottom: 8px;
        }
        
        .form-meta {
            color: #777;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .form-content {
            line-height: 1.6;
        }
        
        .form-field {
            margin-bottom: 8px;
        }
        
        .field-label {
            font-weight: bold;
            color: var(--dark-purple);
            display: inline-block;
            min-width: 100px;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            box-shadow: 0 0 8px rgba(0,0,0,0.05);
            border-left: 4px solid #28a745;
        }
        
        .btn {
            background-color: var(--primary-purple);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        
        .btn:hover {
            background-color: var(--dark-purple);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }
        
        .no-forms {
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <h1>Personal Dashboard</h1>
            </div>
            <div class="nav-links">
                <a href="submit_form.php">Submit Information</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>
    
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <div class="welcome-box">
            <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
            <p>This is your personal dashboard where you can view all your submitted information.</p>
            <p><a href="submit_form.php" class="btn">Submit Your Information</a></p>
        </div>
        
        <div class="forms-box">
            <h2>Your Personal Information</h2>
            
            <?php if (empty($forms)): ?>
                <p class="no-forms">You haven't submitted any information yet. <a href="submit_form.php">Submit your information now</a>.</p>
            <?php else: ?>
                <?php foreach ($forms as $form): ?>
                    <div class="form-item">
                        <div class="form-title"><?php echo html_entity_decode(htmlspecialchars($form['full_name'])); ?></div>
                        <div class="form-meta">
                            Submitted: <?php echo date('F j, Y, g:i a', strtotime($form['submit_date'])); ?>
                        </div>
                        <div class="form-content">
                            <div class="form-field">
                                <span class="field-label">Location:</span>
                                <?php echo html_entity_decode(htmlspecialchars($form['location'])); ?>
                            </div>
                            
                            <div class="form-field">
                                <span class="field-label">Date of Birth:</span>
                                <?php echo html_entity_decode(htmlspecialchars($form['date_of_birth'])); ?>
                            </div>
                            
                            <div class="form-field">
                                <span class="field-label">Address:</span>
                                <?php echo html_entity_decode(htmlspecialchars($form['address'])); ?>
                            </div>
                            
                            <div class="form-field">
                                <span class="field-label">Bio:</span>
                                <?php echo html_entity_decode(htmlspecialchars($form['bio'])); ?>
                            </div>
                            
                            <?php if(!empty($form['comments'])): ?>
                            <div class="form-field">
                                <span class="field-label">Comments:</span>
                                <?php echo html_entity_decode(htmlspecialchars($form['comments'])); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>