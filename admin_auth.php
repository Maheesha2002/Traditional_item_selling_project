<?php
session_start();
require_once 'Backend/dbconnect.php';

// Handle form submissions
$error = '';
$success = '';


// Handle login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "All fields are required";
    } else {
        // Check admin credentials
        $query = "SELECT * FROM admins WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $admin['password'])) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['user_type'] = 'admin';

                // Redirect to dashboard
                header("Location: Adminpanal/Dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}

// Handle signup
if (isset($_POST['signup'])) {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $adminKey = $_POST['admin_key'];

    // Validate input
    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword) || empty($adminKey)) {
        $error = "All fields are required";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match";
    } elseif ($adminKey !== "2025") { // Simple admin key for demonstration
        $error = "Invalid admin key";
    } else {
        // Check if email already exists
        $check_query = "SELECT * FROM admins WHERE email = ?";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error = "Email already exists";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new admin
            $insert_query = "INSERT INTO admins (full_name, email, password, created_at) VALUES (?, ?, ?, NOW())";
            $insert_stmt = $conn->prepare($insert_query);
            $insert_stmt->bind_param("sss", $fullName, $email, $hashed_password);

            if ($insert_stmt->execute()) {
                $success = "Admin account created successfully. You can now login.";
            } else {
                $error = "Failed to create account: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Authentication | Heritage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 900px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: #e67e22;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid #eee;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 15px;
            cursor: pointer;
            font-weight: 600;
            color: #555;
            transition: all 0.3s ease;
        }

        .tab.active {
            color: #e67e22;
            border-bottom: 2px solid #e67e22;
        }

        .tab:hover:not(.active) {
            background: #f9f9f9;
        }

        .form-container {
            padding: 30px;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border 0.3s ease;
        }

        .form-group input:focus {
            border-color: #e67e22;
            outline: none;
        }

        .form-group .input-with-icon {
            position: relative;
        }

        .form-group .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .form-group .input-with-icon input {
            padding-left: 45px;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background: #e67e22;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: #d35400;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .form-footer a {
            color: #e67e22;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .logo img {
            height: 50px;
            margin-right: 10px;
        }

        @media (max-width: 768px) {
            .container {
                max-width: 100%;
            }

            .form-container {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="assets/images/Main-logo.png" alt="Heritage Logo">
                <h1>Heritage Admin</h1>
            </div>
            <p>Access the admin dashboard to manage products, orders, and more</p>
        </div>

        <div class="tabs">
            <div class="tab active" data-tab="login">Login</div>
            <div class="tab" data-tab="signup">Sign Up</div>
        </div>

        <div class="form-container">
            <!-- Login Form -->
            <div class="form-section active" id="login-form">
                <?php if ($error && isset($_POST['login'])): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success-message"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="login-email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="login-email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="login-password" name="password" placeholder="Enter your password"
                                required>
                        </div>
                    </div>

                    <button type="submit" name="login" class="submit-btn">Login</button>
                </form>

                <div class="form-footer">
                    <p>Don't have an admin account? <a href="#" class="switch-form" data-form="signup">Sign Up</a></p>
                </div>
            </div>

            <!-- Signup Form -->
            <div class="form-section" id="signup-form">
                <?php if ($error && isset($_POST['signup'])): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="full-name">Full Name</label>
                        <div class="input-with-icon">
                            <i class="fas fa-user"></i>
                            <input type="text" id="full-name" name="full_name" placeholder="Enter your full name"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signup-email">Email Address</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="signup-email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="signup-password">Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="signup-password" name="password" placeholder="Create a password"
                                required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">Confirm Password</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm-password" name="confirm_password"
                                placeholder="Confirm your password" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="admin-key">Admin Key</label>
                        <div class="input-with-icon">
                            <i class="fas fa-key"></i>
                            <input type="password" id="admin-key" name="admin_key" placeholder="Enter admin key"
                                required>
                        </div>
                    </div>

                    <button type="submit" name="signup" class="submit-btn">Create Account</button>
                </form>

                <div class="form-footer">
                    <p>Already have an account? <a href="#" class="switch-form" data-form="login">Login</a></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching
        const tabs = document.querySelectorAll('.tab');
        const formSections = document.querySelectorAll('.form-section');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const targetTab = tab.getAttribute('data-tab');

                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                // Show corresponding form
                formSections.forEach(form => {
                    form.classList.remove('active');
                    if (form.id === `${targetTab}-form`) {
                        form.classList.add('active');
                    }
                });
            });
        });

        // Form switching links
        const switchLinks = document.querySelectorAll('.switch-form');

        switchLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const targetForm = link.getAttribute('data-form');

                // Update active tab
                tabs.forEach(t => {
                    t.classList.remove('active');
                    if (t.getAttribute('data-tab') === targetForm) {
                        t.classList.add('active');
                    }
                });

                // Show corresponding form
                formSections.forEach(form => {
                    form.classList.remove('active');
                    if (form.id === `${targetForm}-form`) {
                        form.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>

</html>