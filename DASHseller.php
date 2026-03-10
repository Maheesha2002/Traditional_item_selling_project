<?php
session_start();

// Check if user is logged in as a customer
if (!isset($_SESSION['customer_id']) || $_SESSION['user_type'] !== 'customer') {
    header("Location: index.html");
    exit();
}

require_once 'Backend/dbconnect.php';
$customer_id = $_SESSION['customer_id'];

// Check if this customer has any shops
$shops_query = "SELECT * FROM sellers WHERE customer_id = ?";
$shops_stmt = $conn->prepare($shops_query);

// Add error checking for prepare
if ($shops_stmt === false) {
    error_log("Prepare failed: " . $conn->error);
    echo "Database error: " . $conn->error;
    exit();
}

$shops_stmt->bind_param("s", $customer_id);
$shops_stmt->execute();
$shops_result = $shops_stmt->get_result();

$has_shops = $shops_result->num_rows > 0;
$shops = [];

if ($has_shops) {
    while ($row = $shops_result->fetch_assoc()) {
        $shops[] = $row;
    }

    // Get the active shop (first shop by default or from session)
    $active_shop_id = isset($_GET['shop_id']) ? $_GET['shop_id'] : ($_SESSION['active_shop_id'] ?? $shops[0]['seller_id']);

    // Store active shop in session
    $_SESSION['active_shop_id'] = $active_shop_id;

    // Get active shop details
    $active_shop = null;
    foreach ($shops as $shop) {
        if ($shop['seller_id'] === $active_shop_id) {
            $active_shop = $shop;
            break;
        }
    }

    // If shop is not approved, redirect to index2.php with appropriate message
    if ($active_shop && $active_shop['status'] === 'pending') {
        $_SESSION['notification'] = [
            'type' => 'info',
            'message' => 'Your shop is pending approval. Please wait until an admin approves your shop.'
        ];
        header("Location: index2.php");
        exit();
    } else if ($active_shop && $active_shop['status'] === 'rejected') {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Your shop registration has been rejected. Please contact support for more information.'
        ];
        header("Location: index2.php");
        exit();
    }
}

// Add notification_seen column to sellers table if it doesn't exist
$check_column_sql = "SHOW COLUMNS FROM sellers LIKE 'notification_seen'";
$check_column_result = $conn->query($check_column_sql);
if ($check_column_result->num_rows === 0) {
    $add_column_sql = "ALTER TABLE sellers ADD COLUMN notification_seen TINYINT(1) DEFAULT 0";
    $conn->query($add_column_sql);
}

// Add offer_price column to products table if it doesn't exist
$check_offer_column_sql = "SHOW COLUMNS FROM products LIKE 'offer_price'";
$check_offer_column_result = $conn->query($check_offer_column_sql);
if ($check_offer_column_result->num_rows === 0) {
    $add_offer_column_sql = "ALTER TABLE products ADD COLUMN offer_price DECIMAL(10,2) DEFAULT NULL";
    $conn->query($add_offer_column_sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard | Heritage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="nevbar/nevbar2.cs">
    <link rel="stylesheet" href="css/DASHseller.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .payment-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-align: center;
        }

        .payment-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .payment-badge.paid {
            background: #d4edda;
            color: #155724;
        }

        .payment-badge.refunded {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
    <?php include 'Nevbar/nevbar2.php'; ?> 
</head>


<body>
    

    <div class="dashboard-container">
        <?php if (!$has_shops): ?>
            <!-- No shops view -->
            <div class="no-shops-container">
                <div class="no-shops-content">
                    <i class="fas fa-store-slash"></i>
                    <h2>You don't have any shops yet</h2>
                    <p>Start selling your traditional products by creating your first shop.</p>
                    <a href="seller_registration.php" class="create-shop-btn">Create Your Shop</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Seller Dashboard -->
            <div class="dashboard-sidebar">
                <div class="shop-selector">
                    <div class="current-shop">
                        <div class="shop-avatar">
                            <?php if ($active_shop['profile_photo']): ?>
                                <img src="<?php echo htmlspecialchars($active_shop['profile_photo']); ?>"
                                    alt="<?php echo htmlspecialchars($active_shop['shop_name']); ?>">
                            <?php else: ?>
                                <div class="shop-avatar-placeholder">
                                    <?php echo strtoupper(substr($active_shop['shop_name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="shop-info">
                            <h3><?php echo htmlspecialchars($active_shop['shop_name']); ?></h3>
                            <span class="shop-status <?php echo $active_shop['status']; ?>">
                                <?php echo ucfirst($active_shop['status']); ?>
                            </span>
                        </div>
                        <button class="shop-dropdown-btn" id="shopDropdownBtn">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="shop-dropdown" id="shopDropdown">
                        <?php foreach ($shops as $shop): ?>
                            <a href="?shop_id=<?php echo $shop['seller_id']; ?>"
                                class="shop-option <?php echo ($shop['seller_id'] === $active_shop_id) ? 'active' : ''; ?>">
                                <div class="shop-option-avatar">
                                    <?php if ($shop['profile_photo']): ?>
                                        <img src="<?php echo htmlspecialchars($shop['profile_photo']); ?>"
                                            alt="<?php echo htmlspecialchars($shop['shop_name']); ?>">
                                    <?php else: ?>
                                        <div class="shop-avatar-placeholder">
                                            <?php echo strtoupper(substr($shop['shop_name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="shop-option-info">
                                    <span class="shop-option-name"><?php echo htmlspecialchars($shop['shop_name']); ?></span>
                                    <span class="shop-option-status <?php echo $shop['status']; ?>">
                                        <?php echo ucfirst($shop['status']); ?>
                                    </span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                        <div class="shop-dropdown-footer">
                            <a href="seller_registration.php" class="add-shop-btn">
                                <i class="fas fa-plus"></i> Add New Shop
                            </a>
                        </div>
                    </div>
                </div>
                <nav class="dashboard-nav">
                    <a href="#dashboard" class="nav-item active" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="#products" class="nav-item" data-section="products">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a href="#orders" class="nav-item" data-section="orders">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                    <a href="#profile" class="nav-item" data-section="profile">
                        <i class="fas fa-store"></i> Shop Profile
                    </a>
                </nav>
            </div>

            <div class="dashboard-content">
                <!-- Dashboard Overview Section -->
                <section id="dashboard-section" class="content-section active">
                    <div class="section-header">
                        <h2>Dashboard Overview</h2>
                        <div class="period-selector">
                            <select id="statsPeriod">
                                <option value="week">Last 7 Days</option>
                                <option value="month" selected>Last 30 Days</option>
                                <option value="year">Last Year</option>
                            </select>
                        </div>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-icon orders">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Orders</h3>
                                <div class="stat-value" id="totalOrders">0</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span id="ordersChange">0%</span> from previous period
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon revenue">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Revenue</h3>
                                <div class="stat-value" id="totalRevenue">LKR 0</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span id="revenueChange">0%</span> from previous period
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon customers">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Customers</h3>
                                <div class="stat-value" id="totalCustomers">0</div>
                                <div class="stat-change positive">
                                    <i class="fas fa-arrow-up"></i>
                                    <span id="customersChange">0%</span> from previous period
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon products">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="stat-info">
                                <h3>Total Products</h3>
                                <div class="stat-value" id="totalProducts">0</div>
                                <div class="stat-change neutral">
                                    <i class="fas fa-minus"></i>
                                    <span>0%</span> from previous period
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="charts-grid">
                        <div class="chart-card sales-chart">
                            <div class="chart-header">
                                <h3>Sales Overview</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>

                        <div class="chart-card order-status-chart">
                            <div class="chart-header">
                                <h3>Order Status</h3>
                            </div>
                            <div class="chart-container">
                                <canvas id="orderStatusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-sections">
                        <div class="section-card">
                            <div class="section-header">
                                <h3>Top Selling Products</h3>
                                <a href="#products" class="view-all" data-section="products">View All</a>
                            </div>
                            <div class="top-products" id="topProducts">
                                <div class="loading">Loading...</div>
                            </div>
                        </div>

                        <div class="section-card">
                            <div class="section-header">
                                <h3>Recent Orders</h3>
                                <a href="#orders" class="view-all" data-section="orders">View All</a>
                            </div>
                            <div class="recent-orders" id="recentOrders">
                                <div class="loading">Loading...</div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Products Section -->
                <section id="products-section" class="content-section">
                    <div class="section-header">
                        <h2>Products</h2>
                        <button class="primary-btn" id="addProductBtn">
                            <i class="fas fa-plus"></i> Add New Product
                        </button>
                    </div>

                    <div class="filter-bar">
                        <div class="search-box">
                            <input type="text" id="productSearch" placeholder="Search products...">
                            <button id="searchBtn"><i class="fas fa-search"></i></button>
                        </div>

                        <div class="filter-options">
                            <select id="categoryFilter">
                                <option value="">All Categories</option>
                                <!-- Categories will be loaded dynamically -->
                            </select>
                        </div>
                    </div>

                    <div class="products-grid" id="productsGrid">
                        <div class="loading">Loading products...</div>
                    </div>

                    <div class="pagination" id="productsPagination">
                        <!-- Pagination will be added dynamically -->
                    </div>
                </section>

                <!-- Orders Section -->
                <section id="orders-section" class="content-section">
                    <div class="section-header">
                        <h2>Orders</h2>
                        <div class="order-filters">
                            <select id="orderStatusFilter">
                                <option value="all">All Orders</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="orders-list" id="ordersList">
                        <div class="loading">Loading orders...</div>
                    </div>

                    <div class="pagination" id="ordersPagination">
                        <!-- Pagination will be added dynamically -->
                    </div>
                </section>

                <!-- Shop Profile Section -->
                <section id="profile-section" class="content-section">
                    <div class="section-header">
                        <h2>Shop Profile</h2>
                        <button class="primary-btn" id="saveProfileBtn">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>

                    <div class="profile-container">
                        <div class="profile-header">
                            <div class="cover-photo">
                                <?php if ($active_shop['cover_photo']): ?>
                                    <img src="<?php echo htmlspecialchars($active_shop['cover_photo']); ?>" alt="Cover Photo">
                                <?php else: ?>
                                    <div class="cover-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>Add Cover Photo</span>
                                    </div>
                                <?php endif; ?>
                                <label for="coverPhotoInput" class="edit-cover-btn">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" id="coverPhotoInput" accept="image/*" style="display: none;">
                            </div>

                            <div class="profile-photo">
                                <?php if ($active_shop['profile_photo']): ?>
                                    <img src="<?php echo htmlspecialchars($active_shop['profile_photo']); ?>"
                                        alt="Profile Photo">
                                <?php else: ?>
                                    <div class="profile-placeholder">
                                        <?php echo strtoupper(substr($active_shop['shop_name'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                <label for="profilePhotoInput" class="edit-profile-btn">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" id="profilePhotoInput" accept="image/*" style="display: none;">
                            </div>
                        </div>

                        <form id="shopProfileForm">
                            <input type="hidden" id="shopId" name="shop_id" value="<?php echo $active_shop_id; ?>">

                            <div class="form-section">
                                <h3>Shop Information</h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="shopName">Shop Name</label>
                                        <input type="text" id="shopName" name="shop_name"
                                            value="<?php echo htmlspecialchars($active_shop['shop_name']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="mainCategory">Main Category</label>
                                        <select id="mainCategory" name="main_category" required>
                                            <option value="">Select a category</option>
                                            <optgroup label="Crafting Products">
                                                <option value="Traditional Masks" <?php echo ($active_shop['main_category'] === 'Traditional Masks') ? 'selected' : ''; ?>>Traditional Masks</option>
                                                <option value="Batik Products" <?php echo ($active_shop['main_category'] === 'Batik Products') ? 'selected' : ''; ?>>Batik Products</option>
                                                <option value="Brass Items" <?php echo ($active_shop['main_category'] === 'Brass Items') ? 'selected' : ''; ?>>
                                                    Brass Items</option>
                                                <option value="Cane Products" <?php echo ($active_shop['main_category'] === 'Cane Products') ? 'selected' : ''; ?>>
                                                    Cane Products</option>
                                                <option value="Traditional Jewelry" <?php echo ($active_shop['main_category'] === 'Traditional Jewelry') ? 'selected' : ''; ?>>Traditional Jewelry</option>
                                            </optgroup>
                                            <optgroup label="Natural Products">
                                                <option value="Ceylon Tea" <?php echo ($active_shop['main_category'] === 'Ceylon Tea') ? 'selected' : ''; ?>>
                                                    Ceylon Tea</option>
                                                <option value="Spices" <?php echo ($active_shop['main_category'] === 'Spices') ? 'selected' : ''; ?>>Spices</option>
                                                <option value="Ceylon Cinnamon" <?php echo ($active_shop['main_category'] === 'Ceylon Cinnamon') ? 'selected' : ''; ?>>Ceylon Cinnamon</option>
                                                <option value="Coconut Products" <?php echo ($active_shop['main_category'] === 'Coconut Products') ? 'selected' : ''; ?>>Coconut Products</option>
                                                <option value="Kithul Products" <?php echo ($active_shop['main_category'] === 'Kithul Products') ? 'selected' : ''; ?>>Kithul Products</option>
                                            </optgroup>
                                            <optgroup label="Premium Items">
                                                <option value="Ceylon Gems" <?php echo ($active_shop['main_category'] === 'Ceylon Gems') ? 'selected' : ''; ?>>
                                                    Ceylon Gems</option>
                                                <option value="Silver Crafts" <?php echo ($active_shop['main_category'] === 'Silver Crafts') ? 'selected' : ''; ?>>
                                                    Silver Crafts</option>
                                                <option value="Leather Products" <?php echo ($active_shop['main_category'] === 'Leather Products') ? 'selected' : ''; ?>>Leather Products</option>
                                                <option value="Traditional Pottery" <?php echo ($active_shop['main_category'] === 'Traditional Pottery') ? 'selected' : ''; ?>>Traditional Pottery</option>
                                                <option value="Other" <?php echo ($active_shop['main_category'] === 'Other') ? 'selected' : ''; ?>>Other products</option>
                                            </optgroup>
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="form-section">
                                <h3>Shop Address</h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="streetAddress">Street Address</label>
                                        <input type="text" id="streetAddress" name="street_address"
                                            value="<?php echo htmlspecialchars($active_shop['street_address']); ?>"
                                            required>
                                    </div>

                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" id="city" name="city"
                                            value="<?php echo htmlspecialchars($active_shop['city']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="district">District</label>
                                        <input type="text" id="district" name="district"
                                            value="<?php echo htmlspecialchars($active_shop['district']); ?>" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="province">Province</label>
                                        <select id="province" name="province" required>
                                            <option value="Western" <?php echo ($active_shop['province'] === 'Western') ? 'selected' : ''; ?>>Western Province</option>
                                            <option value="Central" <?php echo ($active_shop['province'] === 'Central') ? 'selected' : ''; ?>>Central Province</option>
                                            <option value="Southern" <?php echo ($active_shop['province'] === 'Southern') ? 'selected' : ''; ?>>Southern Province</option>
                                            <option value="Northern" <?php echo ($active_shop['province'] === 'Northern') ? 'selected' : ''; ?>>Northern Province</option>
                                            <option value="Eastern" <?php echo ($active_shop['province'] === 'Eastern') ? 'selected' : ''; ?>>Eastern Province</option>
                                            <option value="North-Western" <?php echo ($active_shop['province'] === 'North-Western') ? 'selected' : ''; ?>>
                                                North-Western Province</option>
                                            <option value="North-Central" <?php echo ($active_shop['province'] === 'North-Central') ? 'selected' : ''; ?>>
                                                North-Central Province</option>
                                            <option value="Uva" <?php echo ($active_shop['province'] === 'Uva') ? 'selected' : ''; ?>>Uva Province</option>
                                            <option value="Sabaragamuwa" <?php echo ($active_shop['province'] === 'Sabaragamuwa') ? 'selected' : ''; ?>>
                                                Sabaragamuwa Province</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <h3>Business Information</h3>
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label for="businessName">Business Name</label>
                                        <input type="text" id="businessName" name="business_name"
                                            value="<?php echo htmlspecialchars($active_shop['business_name'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="businessRegNo">Business Registration Number</label>
                                        <input type="text" id="businessRegNo" name="business_reg_no"
                                            value="<?php echo htmlspecialchars($active_shop['business_reg_no'] ?? ''); ?>">
                                    </div>

                                    <div class="form-group full-width">
                                        <label for="businessDescription">Business Description</label>
                                        <textarea id="businessDescription" name="business_description"
                                            rows="4"><?php echo htmlspecialchars($active_shop['business_description'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Business Registration Document</label>
                                        <div class="file-upload">
                                            <button type="button" class="file-upload-btn">Choose File</button>
                                            <input type="file" id="businessDoc" name="business_doc"
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <span class="file-name" id="businessDocName">
                                                <?php echo $active_shop['business_doc_path'] ? basename($active_shop['business_doc_path']) : 'No file chosen'; ?>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>

            <!-- Product Modal -->
            <div class="modal" id="productModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 id="productModalTitle">Add New Product</h3>
                        <button class="close-btn" id="closeProductModal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <form id="productForm">
                            <input type="hidden" id="productId" name="product_id">
                            <input type="hidden" id="sellerId" name="seller_id" value="<?php echo $active_shop_id; ?>">

                            <div class="form-group">
                                <label for="productName">Product Name</label>
                                <input type="text" id="productName" name="product_name" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="productCategory">Category</label>
                                    <select id="productCategory" name="main_category" required>
                                        <option value="">Select a category</option>
                                        <optgroup label="Crafting Products">
                                            <option value="Traditional Masks">Traditional Masks</option>
                                            <option value="Batik Products">Batik Products</option>
                                            <option value="Brass Items">Brass Items</option>
                                            <option value="Cane Products">Cane Products</option>
                                            <option value="Traditional Jewelry">Traditional Jewelry</option>
                                        </optgroup>
                                        <optgroup label="Natural Products">
                                            <option value="Ceylon Tea">Ceylon Tea</option>
                                            <option value="Spices">Spices</option>
                                            <option value="Ceylon Cinnamon">Ceylon Cinnamon</option>
                                            <option value="Coconut Products">Coconut Products</option>
                                            <option value="Kithul Products">Kithul Products</option>
                                        </optgroup>
                                        <optgroup label="Premium Items">
                                            <option value="Ceylon Gems">Ceylon Gems</option>
                                            <option value="Silver Crafts">Silver Crafts</option>
                                            <option value="Leather Products">Leather Products</option>
                                            <option value="Traditional Pottery">Traditional Pottery</option>
                                            <option value="Other">Other Product</option>
                                        </optgroup>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="productSubCategory">Sub Category</label>
                                    <select id="productSubCategory" name="sub_category" required>
                                        <option value="">First select main category</option>
                                    </select>
                                </div>
                            </div>


                            <div class="form-row">
                                <div class="form-group">
                                    <label for="productPrice">Price (LKR)</label>
                                    <input type="number" id="productPrice" name="price" min="0" step="0.01" required>
                                </div>

                                <div class="form-group">
                                    <label for="productOfferPrice">Offer Price (LKR, optional)</label>
                                    <input type="number" id="productOfferPrice" name="offer_price" min="0" step="0.01">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="productQuantity">Quantity</label>
                                    <input type="number" id="productQuantity" name="quantity" min="0" required>
                                </div>

                                <div class="form-group">
                                    <label for="productWeight">Weight (g, optional)</label>
                                    <input type="number" id="productWeight" name="weight" min="0" step="0.01">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="productDescription">Description</label>
                                <textarea id="productDescription" name="description" rows="4" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>Product Images</label>
                                <div class="product-images-container" id="productImagesContainer">
                                    <div class="product-image-upload">
                                        <input type="file" class="product-image-input" name="product_images[]"
                                            accept="image/*" multiple>
                                        <div class="upload-placeholder">
                                            <i class="fas fa-plus"></i>
                                            <span>Add Images</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-images-preview" id="productImagesPreview"></div>
                            </div>

                            <div class="form-actions">
                                <button type="button" class="secondary-btn" id="cancelProductBtn">Cancel</button>
                                <button type="submit" class="primary-btn">Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Order Detail Modal -->
            <div class="modal" id="orderDetailModal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Order Details</h3>
                        <button class="close-btn" id="closeOrderModal">&times;</button>
                    </div>
                    <div class="modal-body" id="orderDetailContent">
                        <!-- Order details will be loaded dynamically -->
                    </div>
                </div>
            </div>

            <!-- Notification -->
            <div class="notification" id="seller-notification">
                <i class="fas fa-check-circle"></i>
                <span id="seller-notificationMessage"></span>
            </div>


            <!-- Congratulations Modal -->
            <div class="congrats-modal" id="congratsModal">
                <div class="congrats-content">
                    <div class="congrats-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <h2 class="congrats-title">Congratulations!</h2>
                    <p class="congrats-message">Your first product has been added successfully. Keep adding more products to
                        grow your shop!</p>
                    <button class="congrats-btn" id="congratsBtn">Continue</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="js/DASHseller.js"></script>

    <!-- <?php include 'footer/footer.ph'; ?> -->

    <script>
        document.getElementById('productCategory').addEventListener('change', function () {
            const subCategorySelect = document.getElementById('productSubCategory');
            const mainCategory = this.value;

            const subCategories = {
                'Traditional Masks': ['Kolam Masks', 'Raksha Masks', 'Sanni Masks', 'Decorative Masks'],
                'Batik Products': ['Batik Clothing', 'Batik Wall Hangings', 'Batik Table Linen', 'Batik Cushion Covers'],
                'Brass Items': ['Brass Lamps', 'Brass Ornaments', 'Brass Kitchenware', 'Brass Religious Items'],
                'Cane Products': ['Cane Furniture', 'Cane Baskets', 'Cane Mats', 'Cane Decorative Items'],
                'Traditional Jewelry': ['Kandyan Jewelry', 'Silver Filigree', 'Beaded Jewelry', 'Coconut Shell Jewelry'],
                'Ceylon Tea': ['Black Tea', 'Green Tea', 'White Tea', 'Flavored Tea'],
                'Spices': ['Pepper', 'Cinnamon', 'Cardamom', 'Curry Blends'],
                'Ceylon Cinnamon': ['Cinnamon Sticks', 'Cinnamon Powder', 'Cinnamon Oil', 'Cinnamon Supplements'],
                'Coconut Products': ['Coconut Oil', 'Coir Products', 'Coconut Shell Crafts', 'Coconut Milk & Cream'],
                'Kithul Products': ['Kithul Treacle', 'Kithul Jaggery', 'Kithul Flour', 'Kithul Honey'],
                'Ceylon Gems': ['Blue Sapphires', 'Pink Sapphires', 'Star Sapphires', "Cat's Eye"],
                'Silver Crafts': ['Silver Jewelry', 'Silver Ornaments', 'Silver Tableware', 'Silver Religious Items'],
                'Leather Products': ['Leather Bags', 'Leather Footwear', 'Leather Accessories', 'Leather Wallets'],
                'Traditional Pottery': ['Decorative Pottery', 'Clay Cookware', 'Ceremonial Pottery', 'Glazed Pottery'],
                'Other Product': ['Other']
            };

            subCategorySelect.innerHTML = '<option value="">Select sub category</option>';

            if (mainCategory && subCategories[mainCategory]) {
                subCategories[mainCategory].forEach(subCategory => {
                    const option = document.createElement('option');
                    option.value = subCategory;
                    option.textContent = subCategory;
                    subCategorySelect.appendChild(option);
                });
                subCategorySelect.disabled = false;
            } else {
                subCategorySelect.disabled = true;
            }
        });
    </script>

</body>

</html>