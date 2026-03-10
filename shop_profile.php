<?php
session_start();
require_once 'Backend/dbconnect.php';

// Get seller ID from URL
$seller_id = isset($_GET['seller_id']) ? $_GET['seller_id'] : null;

if (!$seller_id) {
    header('Location: index.php');
    exit();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['customer_id']);
$customer_id = $is_logged_in ? $_SESSION['customer_id'] : '';

// Get seller information with total products count and average rating
$seller_sql = "SELECT s.*, 
               COUNT(DISTINCT p.product_id) as total_products,
               AVG(pr.rating) as average_rating,
               COUNT(DISTINCT pr.id) as total_ratings
               FROM sellers s 
               LEFT JOIN products p ON s.seller_id = p.seller_id 
               LEFT JOIN product_ratings pr ON p.product_id = pr.product_id
               WHERE s.seller_id = ? AND s.status = 'approved'
               GROUP BY s.seller_id";
$seller_stmt = $conn->prepare($seller_sql);
$seller_stmt->bind_param("s", $seller_id);
$seller_stmt->execute();
$seller = $seller_stmt->get_result()->fetch_assoc();

// Get seller's products with sorting
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
$sort_sql = match($sort) {
    'price_low' => 'ORDER BY COALESCE(p.offer_price, p.price) ASC',
    'price_high' => 'ORDER BY COALESCE(p.offer_price, p.price) DESC',
    'oldest' => 'ORDER BY p.created_at ASC',
    'rating_high' => 'ORDER BY avg_rating DESC',
    default => 'ORDER BY p.created_at DESC'
};

$products_sql = "SELECT p.*, pi.image_path,
                (SELECT AVG(rating) FROM product_ratings WHERE product_id = p.product_id) as avg_rating,
                (SELECT COUNT(*) FROM product_ratings WHERE product_id = p.product_id) as rating_count,
                (SELECT COUNT(*) FROM product_likes WHERE product_id = p.product_id) as like_count,
                (SELECT COUNT(*) FROM product_comments WHERE product_id = p.product_id) as comment_count
                FROM products p
                LEFT JOIN product_images pi ON p.product_id = pi.product_id
                WHERE p.seller_id = ? AND p.status = 'active'
                GROUP BY p.product_id " . $sort_sql;
$products_stmt = $conn->prepare($products_sql);
$products_stmt->bind_param("s", $seller_id);
$products_stmt->execute();
$products = $products_stmt->get_result();

// Get wishlist items
$wishlist_products = [];
if ($is_logged_in) {
    $wishlist_sql = "SELECT product_id FROM wishlists WHERE customer_id = ?";
    $wishlist_stmt = $conn->prepare($wishlist_sql);
    $wishlist_stmt->bind_param("s", $customer_id);
    $wishlist_stmt->execute();
    $wishlist_result = $wishlist_stmt->get_result();
    while ($row = $wishlist_result->fetch_assoc()) {
        $wishlist_products[] = $row['product_id'];
    }
}

// Get liked products
$liked_products = [];
if ($is_logged_in) {
    $likes_sql = "SELECT product_id FROM product_likes WHERE customer_id = ?";
    $likes_stmt = $conn->prepare($likes_sql);
    $likes_stmt->bind_param("s", $customer_id);
    $likes_stmt->execute();
    $likes_result = $likes_stmt->get_result();
    while ($row = $likes_result->fetch_assoc()) {
        $liked_products[] = $row['product_id'];
    }
}

// Get total sales count
$sales_sql = "SELECT COUNT(*) as total_sales FROM order_items oi 
              JOIN products p ON oi.product_id = p.product_id 
              WHERE p.seller_id = ?";
$sales_stmt = $conn->prepare($sales_sql);
$sales_stmt->bind_param("s", $seller_id);
$sales_stmt->execute();
$sales_result = $sales_stmt->get_result()->fetch_assoc();
$total_sales = $sales_result['total_sales'] ?? 0;

// Get top categories
$categories_sql = "SELECT p.main_category, COUNT(*) as category_count 
                  FROM products p 
                  WHERE p.seller_id = ? AND p.status = 'active'
                  GROUP BY p.main_category 
                  ORDER BY category_count DESC 
                  LIMIT 3";
$categories_stmt = $conn->prepare($categories_sql);
$categories_stmt->bind_param("s", $seller_id);
$categories_stmt->execute();
$categories_result = $categories_stmt->get_result();
$top_categories = [];
while ($row = $categories_result->fetch_assoc()) {
    $top_categories[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($seller['shop_name']); ?> | Heritage</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/shop_profile.css">
    <link rel="stylesheet" href="css/category.cs">

    <link rel="stylesheet" href="Nevbar/nevbar2.css">
    <style>
       
    </style>
</head>
<body class="<?php echo $is_logged_in ? 'logged-in' : ''; ?>">
    <?php include 'Nevbar/nevbar2.php'; ?>

    <div class="shop-profile">
        <div class="shop-header">
        <div class="cover-photo" style="background-image: url('<?php echo $seller['cover_photo'] ?? "https://flowbite.com/docs/images/examples/image-2@2x.jpg"; ?>')"></div>
            <div class="shop-info">
                <div class="profile-photo">
                <img src="<?php echo $seller['profile_photo'] ?? 'https://cdn.pixabay.com/photo/2016/05/25/20/17/icon-1415760_1280.png'; ?>" alt="Seller Profile" width="80">
                </div>
                <div class="shop-details">
                    <h1><?php echo htmlspecialchars($seller['shop_name']); ?></h1>
                    
                    <!-- Shop Rating -->
                    <div class="shop-rating">
                        <div class="stars">
                            <?php
                            $avg_rating = round($seller['average_rating'] ?? 0, 1);
                            $full_stars = floor($avg_rating);
                            $half_star = $avg_rating - $full_stars >= 0.5;
                            
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $full_stars) {
                                    echo '<i class="fas fa-star"></i>';
                                } elseif ($i == $full_stars + 1 && $half_star) {
                                    echo '<i class="fas fa-star-half-alt"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <span class="rating-text"><?php echo $avg_rating; ?> (<?php echo $seller['total_ratings'] ?? 0; ?> ratings)</span>
                    </div>
                    
                    <div class="shop-meta">
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($seller['city'] . ', ' . $seller['district']); ?></span>
                        <span><i class="fas fa-store"></i> <?php echo htmlspecialchars($seller['main_category']); ?></span>
                        <span><i class="fas fa-box"></i> <?php echo $seller['total_products']; ?> Products</span>
                    </div>
                    
                    <!-- Top Categories -->
                    <div class="shop-categories">
                        <?php foreach ($top_categories as $category): ?>
                            <span class="category-tag">
                                <?php echo htmlspecialchars($category['main_category']); ?>
                                (<?php echo $category['category_count']; ?>)
                            </span>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($seller['business_description']): ?>
                        <p class="description"><?php echo htmlspecialchars($seller['business_description']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Shop Statistics -->
        <div class="shop-stats">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-value"><?php echo $seller['total_products']; ?></div>
                <div class="stat-label">Total Products</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
                <div class="stat-value"><?php echo number_format($avg_rating, 1); ?></div>
                <div class="stat-label">Average Rating</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-value"><?php echo $total_sales; ?></div>
                <div class="stat-label">Total Sales</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-value">
                    <?php 
                    $date = new DateTime($seller['registration_date']);
                    echo $date->format('M Y'); 
                    ?>
                </div>
                <div class="stat-label">Member Since</div>
            </div>
        </div>
        
        <!-- Shop Tabs -->
        <div class="shop-tabs">
            <button class="shop-tab active" data-tab="products">Products</button>
            <button class="shop-tab" data-tab="about">About</button>
            <button class="shop-tab" data-tab="contact">Contact Information</button>
        </div>
        
        <!-- Products Tab -->
        <div id="products-tab" class="tab-content active">
            <div class="section-header">
                <h2>Products</h2>
                <select id="sortSelect" onchange="window.location.href=this.value">
                    <option value="?seller_id=<?php echo $seller_id; ?>&sort=newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="?seller_id=<?php echo $seller_id; ?>&sort=price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="?seller_id=<?php echo $seller_id; ?>&sort=price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="?seller_id=<?php echo $seller_id; ?>&sort=rating_high" <?php echo $sort === 'rating_high' ? 'selected' : ''; ?>>Highest Rated</option>
                </select>
            </div>

            <div class="products-grid">
                <?php if ($products->num_rows === 0): ?>
                    <div class="no-products">
                        <p>This seller has no products available at the moment.</p>
                    </div>
                <?php else: ?>
                    <?php while ($product = $products->fetch_assoc()): 
                        $product_id = $product['product_id'];
                        $avg_rating = $product['avg_rating'] ? round($product['avg_rating'], 1) : 0;
                        $rating_count = $product['rating_count'] ? $product['rating_count'] : 0;
                        $like_count = $product['like_count'] ? $product['like_count'] : 0;
                        $comment_count = $product['comment_count'] ? $product['comment_count'] : 0;
                        $is_liked = in_array($product_id, $liked_products);
                    ?>
                        <div class="product-card" data-product-id="<?php echo $product_id; ?>">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                                     alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <?php if ($product['offer_price']): ?>
                                    <div class="offer-badge">
                                        <?php 
                                        $discount = (($product['price'] - $product['offer_price']) / $product['price']) * 100;
                                        echo round($discount) . '% OFF';
                                        ?>
                                    </div>
                                <?php endif; ?>
                                <button class="add-wishlist <?php echo in_array($product_id, $wishlist_products) ? 'active' : ''; ?>"
                                        data-product-id="<?php echo $product_id; ?>">
                                    <i class="fas fa-heart"></i>
                                </button>
                            </div>
                            <div class="product-info">
                                <a href="productDetails.php?id=<?php echo $product_id; ?>" class="product-name-link">
                                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                </a>
                                
                                <!-- Product Rating -->
                                <div class="product-rating">
                                    <div class="stars">
                                        <?php
                                        $full_stars = floor($avg_rating);
                                        $half_star = $avg_rating - $full_stars >= 0.5;
                                        
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $full_stars) {
                                                echo '<i class="fas fa-star"></i>';
                                            } elseif ($i == $full_stars + 1 && $half_star) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            } else {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <span class="rating-count"><?php echo $avg_rating; ?> (<?php echo $rating_count; ?>)</span>
                                </div>
                                
                                <div class="price-container">
                                    <?php if ($product['offer_price']): ?>
                                        <span class="original-price">LKR <?php echo number_format($product['price'], 2); ?></span>
                                        <span class="offer-price">LKR <?php echo number_format($product['offer_price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="price">LKR <?php echo number_format($product['price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="stock-status">
                                    <?php if ($product['quantity'] > 0): ?>
                                        <span class="in-stock"><i class="fas fa-check-circle"></i> In Stock (<?php echo $product['quantity']; ?>)</span>
                                    <?php else: ?>
                                        <span class="out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</span>
                                    <?php endif; ?>
                                </div>
                                <button class="add-cart-btn" 
                                        onclick="addToCart(this)"
                                        <?php echo $product['quantity'] <= 0 ? 'disabled' : ''; ?>
                                        data-product='<?php echo json_encode([
                                            "product_id" => $product["product_id"],
                                            "name" => $product["product_name"],
                                            "price" => $product["offer_price"] ?? $product["price"],
                                            "quantity" => 1
                                        ]); ?>'>
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                                
                                <!-- Product Social Actions -->
                                <div class="product-social">
                                    <button class="like-btn <?php echo $is_liked ? 'active' : ''; ?>" 
                                            data-product-id="<?php echo $product_id; ?>">
                                        <i class="<?php echo $is_liked ? 'fas' : 'far'; ?> fa-thumbs-up"></i>
                                        <span class="like-count"><?php echo $like_count; ?></span> Likes
                                    </button>
                                    
                                    <button class="rate-btn" data-product-id="<?php echo $product_id; ?>" 
                                            data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>">
                                        <i class="far fa-star"></i> Rate
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- About Tab -->
        <div id="about-tab" class="tab-content">
            <div class="about-section">
                <h3><i class="fas fa-info-circle"></i> About <?php echo htmlspecialchars($seller['shop_name']); ?></h3>
                <?php if ($seller['business_description']): ?>
                    <p><?php echo htmlspecialchars($seller['business_description']); ?></p>
                <?php else: ?>
                    <p>No description available for this shop.</p>
                <?php endif; ?>
            </div>
            
            <div class="about-section">
                <h3><i class="fas fa-history"></i> History</h3>
                <p>Member since: <?php echo date('F Y', strtotime($seller['registration_date'])); ?></p>
                <p>Total products: <?php echo $seller['total_products']; ?></p>
                <p>Total sales: <?php echo $total_sales; ?></p>
            </div>
            
            <div class="about-section">
                <h3><i class="fas fa-tags"></i> Categories</h3>
                <div class="shop-categories">
                    <?php foreach ($top_categories as $category): ?>
                        <span class="category-tag">
                            <?php echo htmlspecialchars($category['main_category']); ?>
                            (<?php echo $category['category_count']; ?>)
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Contact Information Tab -->
        <div id="contact-tab" class="tab-content">
            <div class="contact-info">
                <h2><i class="fas fa-address-card"></i> Contact Information</h2>
                <div class="contact-grid">
                    <div class="contact-item">
                        <i class="fas fa-building"></i>
                        <div>
                            <strong>Business Name</strong>
                            <span><?php echo htmlspecialchars($seller['business_name'] ?? 'Not specified'); ?></span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marked-alt"></i>
                        <div>
                            <strong>Address</strong>
                            <span><?php echo htmlspecialchars($seller['street_address'] . ', ' . $seller['city'] . ', ' . $seller['district']); ?></span>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-id-card"></i>
                        <div>
                            <strong>Business Registration</strong>
                            <span><?php echo htmlspecialchars($seller['business_reg_no'] ?? 'Not specified'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Modal -->
    <div class="rating-modal" id="ratingModal">
        <div class="rating-modal-content">
            <div class="rating-modal-header">
                <h3 class="rating-modal-title">Rate this product</h3>
                <button class="rating-close">&times;</button>
            </div>
            <div class="rating-stars">
                <i class="far fa-star" data-rating="1"></i>
                <i class="far fa-star" data-rating="2"></i>
                <i class="far fa-star" data-rating="3"></i>
                <i class="far fa-star" data-rating="4"></i>
                <i class="far fa-star" data-rating="5"></i>
            </div>
            <textarea class="rating-review" placeholder="Write your review (optional)"></textarea>
            <button class="rating-submit" disabled>Submit Rating</button>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message"></span>
    </div>

    <?php include 'footer/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabs = document.querySelectorAll('.shop-tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to current tab and content
                this.classList.add('active');
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });
        
        // Wishlist functionality
        document.querySelectorAll('.add-wishlist').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const isActive = this.classList.contains('active');
                
                // Check if user is logged in
                const isLoggedIn = document.body.classList.contains('logged-in');
                
                if (!isLoggedIn) {
                    showNotification('Please login to add items to your wishlist', 'warning');
                    return;
                }
                
                fetch(`Backend/wishlist/${isActive ? 'remove_from_wishlist.php' : 'add_to_wishlist.php'}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.classList.toggle('active');
                        showNotification(
                            isActive ? 'Item removed from wishlist' : 'Item added to wishlist',
                            'success'
                        );
                        updateWishlistCount(data.count);
                    } else {
                        showNotification(data.message, 'warning');
                    }
                })
                .catch(error => showNotification('An error occurred', 'error'));
            });
        });

        // Cart functionality
        window.addToCart = function(button) {
            if (button.disabled) return;
            
            // Check if user is logged in
            const isLoggedIn = document.body.classList.contains('logged-in');
            
            if (!isLoggedIn) {
                showNotification('Please login to add items to your cart', 'warning');
                return;
            }
            
            const productData = JSON.parse(button.getAttribute('data-product'));
            
            fetch('Backend/cart/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productData.product_id,
                    quantity: productData.quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Item added to cart successfully', 'success');
                    updateCartCount(data.count);
                } else {
                    showNotification(data.message, 'warning');
                }
            })
            .catch(error => showNotification('An error occurred', 'error'));
        };
        
        // Like functionality
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const isLiked = this.classList.contains('active');
                const likeCountElement = this.querySelector('.like-count');
                
                // Check if user is logged in
                const isLoggedIn = document.body.classList.contains('logged-in');
                
                if (!isLoggedIn) {
                    showNotification('Please login to like products', 'warning');
                    return;
                }
                
                fetch(`Backend/products/toggle_like.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        product_id: productId,
                        action: isLiked ? 'unlike' : 'like'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Toggle active class
                        this.classList.toggle('active');
                        
                        // Update icon
                        const icon = this.querySelector('i');
                        icon.className = this.classList.contains('active') ? 'fas fa-thumbs-up' : 'far fa-thumbs-up';
                        
                        // Update count
                        likeCountElement.textContent = data.count;
                        
                        showNotification(
                            isLiked ? 'Product unliked' : 'Product liked',
                            'success'
                        );
                    } else {
                        showNotification(data.message, 'warning');
                    }
                })
                .catch(error => showNotification('An error occurred', 'error'));
            });
        });
        
        // Rating functionality
        document.querySelectorAll('.rate-btn').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                
                // Check if user is logged in
                const isLoggedIn = document.body.classList.contains('logged-in');
                
                if (!isLoggedIn) {
                    showNotification('Please login to rate products', 'warning');
                    return;
                }
                
                openRatingModal(productId, productName);
            });
        });
        
        // Initialize rating modal
        initializeRatingModal();
    });
    
    // Initialize rating modal
    function initializeRatingModal() {
        const ratingModal = document.getElementById('ratingModal');
        
        if (ratingModal) {
            // Close button functionality
            const closeBtn = ratingModal.querySelector('.rating-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', closeRatingModal);
            }
            
            // Close when clicking outside the modal content
            window.addEventListener('click', function(event) {
                if (event.target === ratingModal) {
                    closeRatingModal();
                }
            });
            
            // Star rating functionality
            const ratingStars = ratingModal.querySelectorAll('.rating-stars i');
            const ratingSubmit = ratingModal.querySelector('.rating-submit');
            
            let selectedRating = 0;
            
            ratingStars.forEach(star => {
                // Hover effect
                star.addEventListener('mouseover', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    highlightModalStars(ratingStars, rating);
                });
                
                // Mouse leave - restore selected rating
                star.addEventListener('mouseout', function() {
                    highlightModalStars(ratingStars, selectedRating);
                });
                
                // Click to select rating
                star.addEventListener('click', function() {
                    selectedRating = parseInt(this.getAttribute('data-rating'));
                    highlightModalStars(ratingStars, selectedRating);
                    
                    // Enable submit button
                    if (ratingSubmit) {
                        ratingSubmit.disabled = false;
                    }
                });
            });
            
            // Submit rating
            if (ratingSubmit) {
                ratingSubmit.addEventListener('click', function() {
                    const productId = ratingModal.getAttribute('data-product-id');
                    const reviewText = ratingModal.querySelector('.rating-review').value.trim();
                    
                    submitProductRating(productId, selectedRating, reviewText);
                });
            }
        }
    }
    
    // Open rating modal
    function openRatingModal(productId, productName) {
        const ratingModal = document.getElementById('ratingModal');
        
        if (ratingModal) {
            // Set product ID and reset form
            ratingModal.setAttribute('data-product-id', productId);
            
            // Update title if needed
            const modalTitle = ratingModal.querySelector('.rating-modal-title');
            if (modalTitle && productName) {
                modalTitle.textContent = `Rate ${productName}`;
            }
            
            // Reset stars
            const ratingStars = ratingModal.querySelectorAll('.rating-stars i');
            highlightModalStars(ratingStars, 0);
            
            // Reset review text
            const reviewTextarea = ratingModal.querySelector('.rating-review');
            if (reviewTextarea) {
                reviewTextarea.value = '';
            }
            
            // Disable submit button
            const submitBtn = ratingModal.querySelector('.rating-submit');
            if (submitBtn) {
                submitBtn.disabled = true;
            }
            
            // Show modal
            ratingModal.style.display = 'flex';
            
            // Add animation class
            setTimeout(() => {
                ratingModal.classList.add('active');
            }, 10);
            
            // Prevent body scrolling
            document.body.classList.add('modal-open');
        }
    }
    
    // Close rating modal
    function closeRatingModal() {
        const ratingModal = document.getElementById('ratingModal');
        
        if (ratingModal) {
            // Remove active class for animation
            ratingModal.classList.remove('active');
            
            // Hide modal after animation
            setTimeout(() => {
                ratingModal.style.display = 'none';
            }, 300);
            
            // Allow body scrolling
            document.body.classList.remove('modal-open');
        }
    }
    
    // Highlight stars in modal
    function highlightModalStars(stars, rating) {
        stars.forEach(star => {
            const starRating = parseInt(star.getAttribute('data-rating'));
            
            if (starRating <= rating) {
                star.className = 'fas fa-star';
            } else {
                star.className = 'far fa-star';
            }
        });
    }
    
    // Submit product rating
    function submitProductRating(productId, rating, review) {
        // Show loading state
        const submitBtn = document.querySelector('.rating-submit');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Submitting...';
        submitBtn.disabled = true;
        
        // Send rating to server
        fetch('Backend/products/rate_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                product_id: productId,
                rating: rating,
                review: review
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                closeRatingModal();
                
                // Show success notification
                showNotification('Rating submitted successfully', 'success');
                
                // Update product rating display if available
                if (data.average_rating) {
                    updateProductRatingDisplay(productId, data.average_rating, data.rating_count);
                }
                
            } else {
                // Show error
                showNotification(data.message || 'Failed to submit rating', 'error');
                
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error submitting rating:', error);
            showNotification('An error occurred while submitting your rating', 'error');
            
            // Reset button
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }
    
    // Update product rating display
    function updateProductRatingDisplay(productId, averageRating, ratingCount) {
        const productCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
        
        if (productCard) {
            const starsContainer = productCard.querySelector('.stars');
            const ratingCountElement = productCard.querySelector('.rating-count');
            
            if (starsContainer) {
                // Update stars
                const fullStars = Math.floor(averageRating);
                const hasHalfStar = averageRating % 1 >= 0.5;
                
                starsContainer.innerHTML = '';
                
                for (let i = 1; i <= 5; i++) {
                    const starIcon = document.createElement('i');
                    
                    if (i <= fullStars) {
                        starIcon.className = 'fas fa-star';
                    } else if (i === fullStars + 1 && hasHalfStar) {
                        starIcon.className = 'fas fa-star-half-alt';
                    } else {
                        starIcon.className = 'far fa-star';
                    }
                    
                    starsContainer.appendChild(starIcon);
                }
            }
            
            if (ratingCountElement) {
                ratingCountElement.textContent = `${parseFloat(averageRating).toFixed(1)} (${ratingCount})`;
            }
        }
    }
    
    // Function to show notification
    function showNotification(message, type = 'success') {
        const notification = document.getElementById('notification');
        const notificationMessage = document.getElementById('notification-message');
        
        if (!notification || !notificationMessage) return;
        
        // Set message
        notificationMessage.textContent = message;
        
        // Reset notification classes
        notification.className = 'notification';
        
        // Set notification type
        notification.classList.add(type);
        
        // Set icon based on type
        const icon = notification.querySelector('i');
        if (icon) {
            if (type === 'success') {
                icon.className = 'fas fa-check-circle';
            } else if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle';
            } else if (type === 'warning') {
                icon.className = 'fas fa-exclamation-triangle';
            }
        }
        
        // Show notification
        notification.classList.add('show');
        
        // Hide after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }
    
    // Update wishlist count
    function updateWishlistCount(count) {
        const wishlistCount = document.querySelector('.wishlist-count');
        if (wishlistCount) {
            wishlistCount.textContent = count;
            wishlistCount.style.display = count > 0 ? 'block' : 'none';
        }
    }
    
    // Update cart count
    function updateCartCount(count) {
        const cartCount = document.querySelector('.cart-count');
        if (cartCount) {
            cartCount.textContent = count;
            cartCount.style.display = count > 0 ? 'block' : 'none';
        }
    }
    </script>
</body>
</html>

