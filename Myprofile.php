<?php
session_start();

// Check if user is logged in as customer
if (!isset($_SESSION['customer_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: index.php");
    exit();
}

// Include database connection
require_once 'Backend/dbconnect.php';

// Include appropriate navbar
if (isset($_SESSION['customer_id']) && $_SESSION['user_type'] === 'customer') {
    include 'Nevbar/nevbar2.php';
} else {
    include 'Nevbar/nevbar.php';
}

// Get customer information
$customer_id = $_SESSION['customer_id'];
$userName = $_SESSION['full_name'] ?? '';
$userEmail = $_SESSION['email'] ?? '';

// Get customer profile data
$profile_sql = "SELECT * FROM customers WHERE customer_id = ?";
$profile_stmt = $conn->prepare($profile_sql);
if (!$profile_stmt) {
    error_log("Profile query preparation failed: " . $conn->error);
    $profile_data = [];
} else {
    $profile_stmt->bind_param("s", $customer_id);
    $profile_stmt->execute();
    $profile_result = $profile_stmt->get_result();
    $profile_data = $profile_result->fetch_assoc();
}

// Set default cover photo if none exists
$cover_photo = isset($profile_data['cover_photo']) && !empty($profile_data['cover_photo'])
    ? $profile_data['cover_photo']
    : 'assets/images/default-cover.jpg';

// Get customer orders count
$orders_count_sql = "SELECT COUNT(*) as total FROM orders WHERE customer_id = ?";
$orders_count_stmt = $conn->prepare($orders_count_sql);
if (!$orders_count_stmt) {
    error_log("Orders count query preparation failed: " . $conn->error);
    $orders_count = 0;
} else {
    $orders_count_stmt->bind_param("s", $customer_id);
    $orders_count_stmt->execute();
    $orders_count_result = $orders_count_stmt->get_result();
    $orders_count = $orders_count_result->fetch_assoc()['total'];
}

// Get customer reviews count
$reviews_count_sql = "SELECT COUNT(*) as total FROM product_ratings WHERE customer_id = ?";
$reviews_count_stmt = $conn->prepare($reviews_count_sql);
if (!$reviews_count_stmt) {
    error_log("Reviews count query preparation failed: " . $conn->error);
    $reviews_count = 0;
} else {
    $reviews_count_stmt->bind_param("s", $customer_id);
    $reviews_count_stmt->execute();
    $reviews_count_result = $reviews_count_stmt->get_result();
    $reviews_count = $reviews_count_result->fetch_assoc()['total'];
}

// Get customer wishlist count
$wishlist_count_sql = "SELECT COUNT(*) as total FROM wishlists WHERE customer_id = ?";
$wishlist_count_stmt = $conn->prepare($wishlist_count_sql);
if (!$wishlist_count_stmt) {
    error_log("Wishlist count query preparation failed: " . $conn->error);
    $wishlist_count = 0;
} else {
    $wishlist_count_stmt->bind_param("s", $customer_id);
    $wishlist_count_stmt->execute();
    $wishlist_count_result = $wishlist_count_stmt->get_result();
    $wishlist_count = $wishlist_count_result->fetch_assoc()['total'];
}

// Get recent orders
$recent_orders_sql = "SELECT o.*, 
                      (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as item_count 
                      FROM orders o 
                      WHERE o.customer_id = ? 
                      ORDER BY o.created_at DESC LIMIT 3";
$recent_orders_stmt = $conn->prepare($recent_orders_sql);
if (!$recent_orders_stmt) {
    error_log("Recent orders query preparation failed: " . $conn->error);
    $recent_orders_result = null;
} else {
    $recent_orders_stmt->bind_param("s", $customer_id);
    $recent_orders_stmt->execute();
    $recent_orders_result = $recent_orders_stmt->get_result();
}

// Get recent reviews
$reviews_sql = "SELECT pr.*, p.product_name, 
               (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_path 
               FROM product_ratings pr 
               JOIN products p ON pr.product_id = p.product_id 
               WHERE pr.customer_id = ? 
               ORDER BY pr.created_at DESC LIMIT 3";
$reviews_stmt = $conn->prepare($reviews_sql);
if (!$reviews_stmt) {
    error_log("Reviews query preparation failed: " . $conn->error);
    $reviews_result = null;
} else {
    $reviews_stmt->bind_param("s", $customer_id);
    $reviews_stmt->execute();
    $reviews_result = $reviews_stmt->get_result();
}

// Get all orders for orders tab
$all_orders_sql = "SELECT o.*, 
                  (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as item_count 
                  FROM orders o 
                  WHERE o.customer_id = ? 
                  ORDER BY o.created_at DESC";
$all_orders_stmt = $conn->prepare($all_orders_sql);
if (!$all_orders_stmt) {
    error_log("All orders query preparation failed: " . $conn->error);
    $all_orders_result = null;
} else {
    $all_orders_stmt->bind_param("s", $customer_id);
    $all_orders_stmt->execute();
    $all_orders_result = $all_orders_stmt->get_result();
}

// Get all reviews for reviews tab
$all_reviews_sql = "SELECT pr.*, p.product_name, 
                   (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_path 
                   FROM product_ratings pr 
                   JOIN products p ON pr.product_id = p.product_id 
                   WHERE pr.customer_id = ? 
                   ORDER BY pr.created_at DESC";
$all_reviews_stmt = $conn->prepare($all_reviews_sql);
if (!$all_reviews_stmt) {
    error_log("All reviews query preparation failed: " . $conn->error);
    $all_reviews_result = null;
} else {
    $all_reviews_stmt->bind_param("s", $customer_id);
    $all_reviews_stmt->execute();
    $all_reviews_result = $all_reviews_stmt->get_result();
}

// Get all addresses
$addresses_sql = "SELECT * FROM shipping_addresses WHERE customer_id = ? ORDER BY is_default DESC, created_at DESC";
$addresses_stmt = $conn->prepare($addresses_sql);
if (!$addresses_stmt) {
    error_log("Addresses query preparation failed: " . $conn->error);
    $addresses_result = null;
} else {
    $addresses_stmt->bind_param("s", $customer_id);
    $addresses_stmt->execute();
    $addresses_result = $addresses_stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Traditional Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/myprofile.css">
    <link rel="stylesheet" href="nevbar/nevbar2.ss">
</head>

<body>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-cover" style="background-image: url('<?php echo htmlspecialchars($cover_photo); ?>');">
                <button class="edit-cover-btn" id="editCoverBtn"><i class="fas fa-camera"></i></button>
            </div>

            <div class="profile-info-bar">
                <div class="profile-image-section">
                    <img src="<?php echo isset($profile_data['profile_image']) ? $profile_data['profile_image'] : 'assets/images/profile-default.jpg'; ?>"
                        alt="Profile" class="profile-image">
                    <button class="edit-photo-btn" id="changeProfilePhoto">
                        <i class="fas fa-camera"></i>
                    </button>
                </div>
                <div class="profile-quick-info">
                    <h1><?php echo htmlspecialchars($userName); ?></h1>
                    <div class="profile-stats">
                        <div class="stat">
                            <span class="stat-value"><?php echo $orders_count; ?></span>
                            <span class="stat-label">Orders</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?php echo $reviews_count; ?></span>
                            <span class="stat-label">Reviews</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?php echo $wishlist_count; ?></span>
                            <span class="stat-label">Wishlist</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="profile-content">
            <!-- Left Sidebar -->
            <div class="profile-sidebar">
                <div class="profile-menu">
                    <a href="#dashboard" class="menu-item active" data-tab="dashboard">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="#personal" class="menu-item" data-tab="personal">
                        <i class="fas fa-user"></i> Personal Info
                    </a>
                    <a href="#orders" class="menu-item" data-tab="orders">
                        <i class="fas fa-shopping-bag"></i> My Orders
                    </a>
                    <a href="#reviews" class="menu-item" data-tab="reviews">
                        <i class="fas fa-star"></i> My Reviews
                    </a>
                    <a href="#addresses" class="menu-item" data-tab="addresses">
                        <i class="fas fa-map-marker-alt"></i> Addresses
                    </a>
                    <a href="#security" class="menu-item" data-tab="security">
                        <i class="fas fa-shield-alt"></i> Security
                    </a>
                    <a href="#notifications" class="menu-item" data-tab="notifications">
                        <i class="fas fa-bell"></i> Notifications
                    </a>
                </div>
            </div>

            <!-- Right Content Area -->
            <div class="profile-main-content">
                <!-- Dashboard Tab -->
                <div id="dashboard" class="tab-content active">
                    <h2>Welcome back, <?php echo htmlspecialchars($userName); ?>!</h2>
                    <p class="dashboard-intro">Here's an overview of your recent activity</p>

                    <div class="dashboard-grid">
                        <div class="dashboard-card">
                            <h3>Recent Orders</h3>
                            <div class="order-list">
                                <?php if ($recent_orders_result && $recent_orders_result->num_rows > 0): ?>
                                    <?php while ($order = $recent_orders_result->fetch_assoc()): ?>
                                        <div class="order-item">
                                            <div class="order-info">
                                                <div class="order-id">#<?php echo $order['order_id']; ?></div>
                                                <div class="order-date">
                                                    <?php echo date('M d, Y', strtotime($order['created_at'])); ?></div>
                                            </div>
                                            <div class="order-details">
                                                <div class="order-status <?php echo strtolower($order['status']); ?>">
                                                    <?php echo ucfirst($order['status']); ?>
                                                </div>
                                                <div class="order-amount">
                                                    Rs. <?php echo number_format($order['total_amount'], 2); ?>
                                                </div>
                                                <div class="order-items-count">
                                                    <?php echo $order['item_count']; ?> item(s)
                                                </div>
                                            </div>
                                            <a href="order_details.php?id=<?php echo $order['order_id']; ?>"
                                                class="view-order-btn">View Details</a>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-shopping-bag"></i>
                                        <p>You haven't placed any orders yet.</p>
                                        <a href="index2.php" class="shop-now-btn">Shop Now</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($recent_orders_result && $recent_orders_result->num_rows > 0): ?>
                                <a href="#orders" class="view-all-link" data-tab-trigger="orders">View All Orders</a>
                            <?php endif; ?>
                        </div>

                        <div class="dashboard-card">
                            <h3>Recent Reviews</h3>
                            <div class="review-list">
                                <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
                                    <?php while ($review = $reviews_result->fetch_assoc()): ?>
                                        <div class="review-item">
                                            <div class="review-product">
                                                <img src="<?php echo $review['image_path'] ?? 'assets/images/product-placeholder.png'; ?>"
                                                    alt="<?php echo htmlspecialchars($review['product_name']); ?>">
                                                <div class="review-product-info">
                                                    <h4><?php echo htmlspecialchars($review['product_name']); ?></h4>
                                                    <div class="review-rating">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i
                                                                class="fas fa-star <?php echo ($i <= $review['rating']) ? 'filled' : ''; ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="review-content">
                                                <p><?php echo htmlspecialchars($review['review'] ?? ''); ?></p>
                                                <span
                                                    class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-star"></i>
                                        <p>You haven't written any reviews yet.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <?php if ($reviews_result && $reviews_result->num_rows > 0): ?>
                                <a href="#reviews" class="view-all-link" data-tab-trigger="reviews">View All Reviews</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Personal Info Tab -->
                <div id="personal" class="tab-content">
                    <div class="info-card">
                        <h3>Personal Information</h3>
                        <form id="personalInfoForm" action="Backend/profile/update_personal_info.php" method="POST">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="fullName">Full Name</label>
                                    <input type="text" id="fullName" name="fullName"
                                        value="<?php echo htmlspecialchars($userName); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        value="<?php echo htmlspecialchars($userEmail); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" id="phone" name="phone"
                                        value="<?php echo htmlspecialchars($profile_data['phone'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" id="dob" name="dob"
                                        value="<?php echo htmlspecialchars($profile_data['dob'] ?? ''); ?>">
                                </div>
                            </div>
                            <button type="submit" class="save-btn">Save Changes</button>
                        </form>
                    </div>
                </div>

                <!-- Orders Tab -->
                <div id="orders" class="tab-content">
                    <div class="orders-filter">
                        <input type="text" id="orderSearchInput" placeholder="Search orders...">
                        <select id="orderStatusFilter">
                            <option value="all">All Orders</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div class="orders-list">
                        <?php if ($all_orders_result && $all_orders_result->num_rows > 0): ?>
                            <?php while ($order = $all_orders_result->fetch_assoc()): ?>
                                <div class="order-card" data-status="<?php echo strtolower($order['status']); ?>">
                                    <div class="order-header">
                                        <div class="order-id-date">
                                            <h3>Order #<?php echo $order['order_id']; ?></h3>
                                            <span
                                                class="order-date"><?php echo date('F d, Y', strtotime($order['created_at'])); ?></span>
                                        </div>
                                        <div class="order-status-badge <?php echo strtolower($order['status']); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </div>
                                    </div>

                                    <div class="order-details">
                                        <div class="order-info-group">
                                            <div class="order-info-item">
                                                <span class="info-label">Items</span>
                                                <span class="info-value"><?php echo $order['item_count']; ?> items</span>
                                            </div>
                                            <div class="order-info-item">
                                                <span class="info-label">Total</span>
                                                <span class="info-value">Rs.
                                                    <?php echo number_format($order['total_amount'], 2); ?></span>
                                            </div>
                                            <div class="order-info-item">
                                                <span class="info-label">Payment</span>
                                                <span class="info-value"><?php echo ucfirst($order['payment_method']); ?></span>
                                            </div>
                                            <div class="order-info-item">
                                                <span class="info-label">Payment Status</span>
                                                <span
                                                    class="info-value payment-status <?php echo strtolower($order['payment_status']); ?>">
                                                    <?php echo ucfirst($order['payment_status']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="order-actions">
                                        <a href="order_details.php?id=<?php echo $order['order_id']; ?>"
                                            class="btn-view-details">View Details</a>
                                        <?php if ($order['status'] === 'delivered'): ?>
                                            <a href="write_review.php?order_id=<?php echo $order['order_id']; ?>"
                                                class="btn-write-review">Write Review</a>
                                        <?php endif; ?>
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <button class="btn-cancel-order"
                                                data-order-id="<?php echo $order['order_id']; ?>">Cancel Order</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-shopping-bag"></i>
                                <p>You haven't placed any orders yet.</p>
                                <a href="index2.php" class="shop-now-btn">Shop Now</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div id="reviews" class="tab-content">
                    <div class="reviews-list">
                        <?php if ($all_reviews_result && $all_reviews_result->num_rows > 0): ?>
                            <?php while ($review = $all_reviews_result->fetch_assoc()): ?>
                                <div class="review-card">
                                    <div class="review-product">
                                        <img src="<?php echo $review['image_path'] ?? 'assets/images/product-placeholder.png'; ?>"
                                            alt="<?php echo htmlspecialchars($review['product_name']); ?>">
                                        <div class="review-product-info">
                                            <h4><?php echo htmlspecialchars($review['product_name']); ?></h4>
                                            <a href="productDetails.php?id=<?php echo $review['product_id']; ?>"
                                                class="view-product-link">View Product</a>
                                        </div>
                                    </div>
                                    <div class="review-content">
                                        <div class="review-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo ($i <= $review['rating']) ? 'filled' : ''; ?>"></i>
                                            <?php endfor; ?>
                                            <span
                                                class="review-date"><?php echo date('F d, Y', strtotime($review['created_at'])); ?></span>
                                        </div>
                                        <p class="review-text"><?php echo htmlspecialchars($review['review'] ?? ''); ?></p>
                                        <div class="review-actions">
                                            <button class="edit-review-btn" data-review-id="<?php echo $review['id']; ?>"
                                                data-product-id="<?php echo $review['product_id']; ?>"
                                                data-rating="<?php echo $review['rating']; ?>"
                                                data-text="<?php echo htmlspecialchars($review['review'] ?? ''); ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="delete-review-btn" data-review-id="<?php echo $review['id']; ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-star"></i>
                                <p>You haven't written any reviews yet.</p>
                                <p class="empty-state-subtitle">Reviews help other shoppers make better decisions.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Addresses Tab -->
                <div id="addresses" class="tab-content">
                    <button class="add-address-btn" id="addAddressBtn">
                        <i class="fas fa-plus"></i> Add New Address
                    </button>
                    <div class="address-grid">
                        <?php if ($addresses_result && $addresses_result->num_rows > 0): ?>
                            <?php while ($address = $addresses_result->fetch_assoc()): ?>
                                <div class="address-card <?php echo $address['is_default'] ? 'default' : ''; ?>">
                                    <?php if ($address['is_default']): ?>
                                        <div class="default-badge">Default</div>
                                    <?php endif; ?>
                                    <div class="address-content">
                                        <h3><?php echo htmlspecialchars($address['full_name']); ?></h3>
                                        <p class="address-line"><?php echo htmlspecialchars($address['address_line1']); ?></p>
                                        <?php if (!empty($address['address_line2'])): ?>
                                            <p class="address-line"><?php echo htmlspecialchars($address['address_line2']); ?></p>
                                        <?php endif; ?>
                                        <p class="address-line">
                                            <?php echo htmlspecialchars($address['city']); ?>,
                                            <?php echo htmlspecialchars($address['province']); ?>
                                            <?php echo htmlspecialchars($address['postal_code']); ?>
                                        </p>
                                        <p class="address-contact">
                                            <span><i class="fas fa-phone"></i>
                                                <?php echo htmlspecialchars($address['phone']); ?></span>
                                            <span><i class="fas fa-envelope"></i>
                                                <?php echo htmlspecialchars($address['email']); ?></span>
                                        </p>
                                    </div>
                                    <div class="address-actions">
                                        <button class="edit-address-btn" data-address-id="<?php echo $address['address_id']; ?>"
                                            data-fullname="<?php echo htmlspecialchars($address['full_name']); ?>"
                                            data-email="<?php echo htmlspecialchars($address['email']); ?>"
                                            data-phone="<?php echo htmlspecialchars($address['phone']); ?>"
                                            data-address1="<?php echo htmlspecialchars($address['address_line1']); ?>"
                                            data-address2="<?php echo htmlspecialchars($address['address_line2'] ?? ''); ?>"
                                            data-city="<?php echo htmlspecialchars($address['city']); ?>"
                                            data-province="<?php echo htmlspecialchars($address['province']); ?>"
                                            data-postal="<?php echo htmlspecialchars($address['postal_code']); ?>"
                                            data-default="<?php echo $address['is_default']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <?php if (!$address['is_default']): ?>
                                            <button class="delete-address-btn"
                                                data-address-id="<?php echo $address['address_id']; ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                            <button class="set-default-btn" data-address-id="<?php echo $address['address_id']; ?>">
                                                <i class="fas fa-check-circle"></i> Set as Default
                                            </button>

                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-map-marker-alt"></i>
                                <p>You don't have any saved addresses.</p>
                                <p class="empty-state-subtitle">Add an address to make checkout faster.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Security Tab -->
                <div id="security" class="tab-content">
                    <div class="security-settings">
                        <div class="security-card">
                            <h3>Change Password</h3>
                            <form id="changePasswordForm" action="Backend/profile/change_password.php" method="POST">
                                <div class="form-group">
                                    <label for="currentPassword">Current Password</label>
                                    <div class="password-input-group">
                                        <input type="password" id="currentPassword" name="currentPassword" required>
                                        <i class="fas fa-eye toggle-password" data-target="currentPassword"></i>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="newPassword">New Password</label>
                                    <div class="password-input-group">
                                        <input type="password" id="newPassword" name="newPassword" required>
                                        <i class="fas fa-eye toggle-password" data-target="newPassword"></i>
                                    </div>
                                    <div class="password-strength-meter">
                                        <div class="strength-bar"></div>
                                    </div>
                                    <ul class="password-requirements">
                                        <li class="requirement" id="length-req">At least 8 characters</li>
                                        <li class="requirement" id="uppercase-req">At least one uppercase letter</li>
                                        <li class="requirement" id="lowercase-req">At least one lowercase letter</li>
                                        <li class="requirement" id="number-req">At least one number</li>
                                        <li class="requirement" id="special-req">At least one special character</li>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <label for="confirmPassword">Confirm New Password</label>
                                    <div class="password-input-group">
                                        <input type="password" id="confirmPassword" name="confirmPassword" required>
                                        <i class="fas fa-eye toggle-password" data-target="confirmPassword"></i>
                                    </div>
                                </div>
                                <button type="submit" class="save-btn">Update Password</button>
                            </form>
                        </div>
                        <div class="security-card">
                            <h3>Account Security</h3>
                            <div class="security-option">
                                <div class="security-option-info">
                                    <h4>Two-Factor Authentication</h4>
                                    <p>Add an extra layer of security to your account by enabling two-factor
                                        authentication.</p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="twoFactorToggle" class="toggle-input">
                                    <label for="twoFactorToggle" class="toggle-label"></label>
                                </div>
                            </div>
                            <div class="security-option">
                                <div class="security-option-info">
                                    <h4>Login Notifications</h4>
                                    <p>Receive email notifications when someone logs into your account.</p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="loginNotificationsToggle" class="toggle-input" checked>
                                    <label for="loginNotificationsToggle" class="toggle-label"></label>
                                </div>
                            </div>
                            <div class="security-option">
                                <div class="security-option-info">
                                    <h4>Recent Login Activity</h4>
                                    <p>View and manage your recent login sessions.</p>
                                </div>
                                <button class="view-activity-btn">View Activity</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications Tab -->
                <div id="notifications" class="tab-content">
                    <div class="notification-settings">
                        <div class="notification-group">
                            <h3>Email Notifications</h3>
                            <div class="notification-option">
                                <div class="notification-info">
                                    <h4>Order Updates</h4>
                                    <p>Receive updates about your orders, including shipping and delivery notifications.
                                    </p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="orderUpdatesToggle" class="toggle-input" checked>
                                    <label for="orderUpdatesToggle" class="toggle-label"></label>
                                </div>
                            </div>
                            <div class="notification-option">
                                <div class="notification-info">
                                    <h4>Promotions and Deals</h4>
                                    <p>Get notified about special offers, discounts, and promotions.</p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="promotionsToggle" class="toggle-input">
                                    <label for="promotionsToggle" class="toggle-label"></label>
                                </div>
                            </div>
                            <div class="notification-option">
                                <div class="notification-info">
                                    <h4>Newsletter</h4>
                                    <p>Receive our weekly newsletter with product updates and traditional crafts
                                        stories.</p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="newsletterToggle" class="toggle-input" checked>
                                    <label for="newsletterToggle" class="toggle-label"></label>
                                </div>
                            </div>
                        </div>
                        <div class="notification-group">
                            <h3>SMS Notifications</h3>
                            <div class="notification-option">
                                <div class="notification-info">
                                    <h4>Order Updates</h4>
                                    <p>Receive SMS notifications about your order status.</p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="smsOrderToggle" class="toggle-input">
                                    <label for="smsOrderToggle" class="toggle-label"></label>
                                </div>
                            </div>
                            <div class="notification-option">
                                <div class="notification-info">
                                    <h4>Promotions</h4>
                                    <p>Get SMS alerts about flash sales and limited-time offers.</p>
                                </div>
                                <div class="toggle-switch">
                                    <input type="checkbox" id="smsPromotionsToggle" class="toggle-input">
                                    <label for="smsPromotionsToggle" class="toggle-label"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Modal -->
    <div id="addressModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="addressModalTitle">Add New Address</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addressForm" action="Backend/checkout/save_address.php" method="POST">
                    <input type="hidden" id="addressId" name="address_id">

                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="addressFullName" name="full_name" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="addressEmail" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="addressPhone" name="phone" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="addressLine1">Address Line 1</label>
                        <input type="text" id="addressLine1" name="address_line1" required>
                    </div>

                    <div class="form-group">
                        <label for="addressLine2">Address Line 2 (Optional)</label>
                        <input type="text" id="addressLine2" name="address_line2">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="addressCity" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="province">Province</label>
                            <select id="addressProvince" name="province" required>
                                <option value="">Select Province</option>
                                <option value="Western">Western</option>
                                <option value="Central">Central</option>
                                <option value="Southern">Southern</option>
                                <option value="Northern">Northern</option>
                                <option value="Eastern">Eastern</option>
                                <option value="North Western">North Western</option>
                                <option value="North Central">North Central</option>
                                <option value="Sabaragamuwa">Sabaragamuwa</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="postalCode">Postal Code</label>
                        <input type="text" id="addressPostalCode" name="postal_code" required>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="isDefault" name="is_default">
                        <label for="isDefault">Set as default address</label>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" id="cancelAddressBtn">Cancel</button>
                        <button type="submit" class="save-btn">Save Address</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Review Edit Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Your Review</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editReviewForm" action="Backend/profile/update_review.php" method="POST">
                    <input type="hidden" id="reviewId" name="review_id">
                    <input type="hidden" id="productId" name="product_id">

                    <div class="form-group">
                        <label>Your Rating</label>
                        <div class="star-rating">
                            <i class="fas fa-star" data-rating="1"></i>
                            <i class="fas fa-star" data-rating="2"></i>
                            <i class="fas fa-star" data-rating="3"></i>
                            <i class="fas fa-star" data-rating="4"></i>
                            <i class="fas fa-star" data-rating="5"></i>
                            <input type="hidden" id="ratingInput" name="rating" value="5">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reviewText">Your Review</label>
                        <textarea id="reviewText" name="review" rows="5" required></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="cancel-btn" id="cancelReviewBtn">Cancel</button>
                        <button type="submit" class="save-btn">Update Review</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Login Activity Modal -->
    <div id="activityModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Recent Login Activity</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="login-activity-list">
                    <div class="activity-item">
                        <div class="activity-info">
                            <div class="device-info">
                                <i class="fas fa-laptop"></i>
                                <span>Windows PC - Chrome Browser</span>
                            </div>
                            <div class="location-info">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Colombo, Sri Lanka</span>
                            </div>
                        </div>
                        <div class="activity-time">
                            <span>Today, 10:45 AM</span>
                            <span class="current-device">Current Device</span>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-info">
                            <div class="device-info">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Android Device - Mobile App</span>
                            </div>
                            <div class="location-info">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Colombo, Sri Lanka</span>
                            </div>
                        </div>
                        <div class="activity-time">
                            <span>Yesterday, 8:30 PM</span>
                        </div>
                    </div>
                </div>
                <div class="activity-actions">
                    <button class="logout-all-btn">Logout from All Devices</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Photo Modal -->
    <div id="profilePhotoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Change Profile Photo</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="profilePhotoForm" action="Backend/profile/update_profile_photo.php" method="POST"
                    enctype="multipart/form-data">
                    <div class="photo-upload-area">
                        <div class="current-photo">
                            <img src="<?php echo isset($profile_data['profile_image']) ? $profile_data['profile_image'] : 'assets/images/profile-default.png'; ?>"
                                alt="Current Profile Photo" id="currentProfilePhoto">
                        </div>
                        <div class="upload-options">
                            <label for="profilePhotoInput" class="upload-btn">
                                <i class="fas fa-upload"></i> Upload Photo
                            </label>
                            <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/*"
                                style="display: none;">
                            <button type="button" class="remove-photo-btn" id="removePhotoBtn">
                                <i class="fas fa-trash"></i> Remove Photo
                            </button>
                        </div>
                    </div>
                    <div class="photo-preview" id="photoPreview" style="display: none;">
                        <h4>Preview</h4>
                        <img src="" alt="Preview" id="photoPreviewImg">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="cancel-btn" id="cancelPhotoBtn">Cancel</button>
                        <button type="submit" class="save-btn">Save Photo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cover Photo Modal -->
    <div id="coverPhotoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Change Cover Photo</h3>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="coverPhotoForm" action="Backend/profile/update_cover_photo.php" method="POST"
                    enctype="multipart/form-data">
                    <div class="cover-upload-area">
                        <div class="upload-instructions">
                            <p>Choose a cover photo that represents you. Ideal dimensions are 1200 x 300 pixels.</p>
                        </div>
                        <label for="coverPhotoInput" class="upload-btn full-width">
                            <i class="fas fa-upload"></i> Upload Cover Photo
                        </label>
                        <input type="file" id="coverPhotoInput" name="cover_photo" accept="image/*"
                            style="display: none;">
                    </div>
                    <div class="cover-preview" id="coverPreview" style="display: none;">
                        <h4>Preview</h4>
                        <div class="cover-preview-container">
                            <img src="" alt="Cover Preview" id="coverPreviewImg">
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="cancel-btn" id="cancelCoverBtn">Cancel</button>
                        <button type="submit" class="save-btn">Save Cover Photo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notificationToast" class="notification-toast">
        <div class="toast-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="toast-message">
            <span id="toastMessage"></span>
        </div>
    </div>

    <script src="js/myprofile.js"></script>
    <?php include 'footer/footer.php'; ?>

</body>

</html>