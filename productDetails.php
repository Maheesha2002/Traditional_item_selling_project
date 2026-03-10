<?php
session_start();
require_once 'Backend/dbconnect.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index2.php");
    exit();
}

$product_id = $_GET['id'];
$customer_id = $_SESSION['customer_id'] ?? null;

// Get product details
$product_sql = "SELECT p.*, s.shop_name, s.seller_id 
                FROM products p
                JOIN sellers s ON p.seller_id = s.seller_id
                WHERE p.product_id = ? AND p.status = 'active'";
$product_stmt = $conn->prepare($product_sql);
$product_stmt->bind_param("s", $product_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

if ($product_result->num_rows === 0) {
    header("Location: index2.php");
    exit();
}

$product = $product_result->fetch_assoc();

// Get product images
$images_sql = "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC";
$images_stmt = $conn->prepare($images_sql);
$images_stmt->bind_param("s", $product_id);
$images_stmt->execute();
$images_result = $images_stmt->get_result();

$images = [];
while ($image = $images_result->fetch_assoc()) {
    $images[] = $image;
}

// Get primary image
$primary_image = !empty($images) ? $images[0]['image_path'] : 'assets/images/placeholder.jpg';

// Check if product is in wishlist
$in_wishlist = false;
if ($customer_id) {
    $wishlist_sql = "SELECT * FROM wishlists WHERE customer_id = ? AND product_id = ?";
    $wishlist_stmt = $conn->prepare($wishlist_sql);
    $wishlist_stmt->bind_param("ss", $customer_id, $product_id);
    $wishlist_stmt->execute();
    $in_wishlist = $wishlist_stmt->get_result()->num_rows > 0;
}

// Get product ratings
$ratings_sql = "SELECT r.*, c.full_name 
                FROM product_ratings r
                JOIN customers c ON r.customer_id = c.customer_id
                WHERE r.product_id = ?
                ORDER BY r.created_at DESC";
$ratings_stmt = $conn->prepare($ratings_sql);
$ratings_stmt->bind_param("s", $product_id);
$ratings_stmt->execute();
$ratings_result = $ratings_stmt->get_result();

$ratings = [];
$total_rating = 0;
$rating_count = 0;

while ($rating = $ratings_result->fetch_assoc()) {
    $ratings[] = $rating;
    $total_rating += $rating['rating'];
    $rating_count++;
}

$average_rating = $rating_count > 0 ? round($total_rating / $rating_count, 1) : 0;

// Check if user has already rated
$user_rated = false;
$user_rating = null;
if ($customer_id) {
    $user_rating_sql = "SELECT * FROM product_ratings WHERE customer_id = ? AND product_id = ?";
    $user_rating_stmt = $conn->prepare($user_rating_sql);
    $user_rating_stmt->bind_param("ss", $customer_id, $product_id);
    $user_rating_stmt->execute();
    $user_rating_result = $user_rating_stmt->get_result();
    
    if ($user_rating_result->num_rows > 0) {
        $user_rated = true;
        $user_rating = $user_rating_result->fetch_assoc();
    }
}

// Get related products
$related_sql = "SELECT p.*, 
                (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_path
                FROM products p
                WHERE p.main_category = ? AND p.product_id != ? AND p.status = 'active'
                ORDER BY RAND()
                LIMIT 4";
$related_stmt = $conn->prepare($related_sql);
$related_stmt->bind_param("ss", $product['main_category'], $product_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();

// Get product likes count
$likes_sql = "SELECT COUNT(*) as like_count FROM product_likes WHERE product_id = ?";
$likes_stmt = $conn->prepare($likes_sql);
$likes_stmt->bind_param("s", $product_id);
$likes_stmt->execute();
$likes_result = $likes_stmt->get_result()->fetch_assoc();
$like_count = $likes_result['like_count'];

// Check if user has liked the product
$user_liked = false;
if ($customer_id) {
    $user_like_sql = "SELECT * FROM product_likes WHERE customer_id = ? AND product_id = ?";
    $user_like_stmt = $conn->prepare($user_like_sql);
    $user_like_stmt->bind_param("ss", $customer_id, $product_id);
    $user_like_stmt->execute();
    $user_liked = $user_like_stmt->get_result()->num_rows > 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> | Traditional Products</title>
    <link rel="stylesheet" href="Nevbar/nevbar2.cs">
    <link rel="stylesheet" href="css/ProductDetails.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <?php include 'Nevbar/nevbar2.php'; ?>
</head>
<body class="product-details-page <?php echo isset($_SESSION['customer_id']) ? 'logged-in' : 'not-logged-in'; ?>">

    <div class="product-breadcrumb">
        <a href="index2.php">Home</a>
        <i class="fas fa-chevron-right"></i>
        <a href="category.php?main=<?php echo urlencode($product['main_category']); ?>"><?php echo htmlspecialchars($product['main_category']); ?></a>
        <i class="fas fa-chevron-right"></i>
        <span class="current"><?php echo htmlspecialchars($product['product_name']); ?></span>
    </div>

    <div class="product-container">
        <div class="product-grid">
            <!-- Product Gallery -->
            <div class="product-gallery">
                <div class="main-image">
                    <img src="<?php echo htmlspecialchars($primary_image); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" id="mainImage">
                </div>
                
                <div class="thumbnail-container">
                    <?php foreach ($images as $index => $image): ?>
                        <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" data-image="<?php echo htmlspecialchars($image['image_path']); ?>">
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Thumbnail">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="product-info">
                <div>
                    <span class="product-category"><?php echo htmlspecialchars($product['sub_category'] ?? $product['main_category']); ?></span>
                    <h1 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                    
                    <div class="product-rating">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= $average_rating): ?>
                                    <i class="fas fa-star"></i>
                                <?php elseif ($i - 0.5 <= $average_rating): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <span class="rating-count"><?php echo $rating_count; ?> reviews</span>
                        <span class="like-count"><i class="far fa-thumbs-up"></i> <?php echo $like_count; ?> likes</span>
                    </div>
                </div>
                
                <div class="product-price">
                    <?php if (!empty($product['offer_price']) && $product['offer_price'] > 0): ?>
                        <span class="current-price">LKR <?php echo number_format($product['offer_price'], 2); ?></span>
                        <span class="original-price">LKR <?php echo number_format($product['price'], 2); ?></span>
                        <?php 
                            $discount_percentage = round((($product['price'] - $product['offer_price']) / $product['price']) * 100);
                        ?>
                        <span class="discount-badge"><?php echo $discount_percentage; ?>% OFF</span>
                    <?php else: ?>
                        <span class="current-price">LKR <?php echo number_format($product['price'], 2); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="product-description">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </div>
                
                <div class="product-meta">
                    <div class="meta-item">
                        <i class="fas fa-store"></i>
                        <span>Seller: <a href="shop_profile.php?seller_id=<?php echo $product['seller_id']; ?>"><?php echo htmlspecialchars($product['shop_name']); ?></a></span>
                    </div>
                    
                    <?php if (!empty($product['weight'])): ?>
                    <div class="meta-item">
                        <i class="fas fa-weight-hanging"></i>
                        <span>Weight: <?php echo $product['weight']; ?> kg</span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="meta-item">
                        <i class="fas fa-box"></i>
                        <div class="stock-status">
                            <?php if ($product['quantity'] > 0): ?>
                                <span class="in-stock"><i class="fas fa-check-circle"></i> In Stock (<?php echo $product['quantity']; ?> available)</span>
                            <?php else: ?>
                                <span class="out-of-stock"><i class="fas fa-times-circle"></i> Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="product-actions">
                    <div class="quantity-selector">
                        <button class="quantity-btn" id="decreaseQuantity">-</button>
                        <input type="number" class="quantity-input" id="quantity" value="1" min="1" max="<?php echo $product['quantity']; ?>">
                        <button class="quantity-btn" id="increaseQuantity">+</button>
                    </div>
                    
                    <button class="add-to-cart" id="addToCartBtn" 
                        <?php echo $product['quantity'] <= 0 ? 'disabled' : ''; ?>
                        data-product-id="<?php echo $product_id; ?>"
                        data-max-quantity="<?php echo $product['quantity']; ?>">
                        <i class="fas fa-shopping-cart"></i>
                        Add to Cart
                    </button>

                    
                    <button class="wishlist-btn <?php echo $in_wishlist ? 'active' : ''; ?>" id="wishlistBtn" data-product-id="<?php echo $product_id; ?>">
                        <i class="<?php echo $in_wishlist ? 'fas' : 'far'; ?> fa-heart"></i>
                    </button>
                    
                    <button class="like-btn <?php echo $user_liked ? 'active' : ''; ?>" id="likeBtn" data-product-id="<?php echo $product_id; ?>">
                        <i class="<?php echo $user_liked ? 'fas' : 'far'; ?> fa-thumbs-up"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Tabs Section -->
        <div class="product-tabs">
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="description">Description</button>
                <button class="tab-btn" data-tab="reviews">Reviews (<?php echo $rating_count; ?>)</button>
            </div>
            
            <div class="tab-content active" id="description">
                <div class="description-content">
                    <?php if (!empty($product['description'])): ?>
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    <?php else: ?>
                        <p>No detailed description available for this product.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="tab-content" id="reviews">
                <div class="reviews-content">
                    <div class="review-summary">
                        <div class="average-rating">
                            <div class="rating-number"><?php echo number_format($average_rating, 1); ?></div>
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $average_rating): ?>
                                        <i class="fas fa-star"></i>
                                    <?php elseif ($i - 0.5 <= $average_rating): ?>
                                        <i class="fas fa-star-half-alt"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <div class="rating-count-text"><?php echo $rating_count; ?> reviews</div>
                        </div>
                        
                        <div class="rating-breakdown">
                            <?php
                            // Calculate rating breakdown
                            $rating_breakdown = [0, 0, 0, 0, 0];
                            foreach ($ratings as $rating) {
                                $rating_breakdown[$rating['rating'] - 1]++;
                            }
                            
                            for ($i = 5; $i >= 1; $i--):
                                $percent = $rating_count > 0 ? ($rating_breakdown[$i - 1] / $rating_count) * 100 : 0;
                            ?>
                            <div class="rating-bar">
                                <div class="rating-label"><?php echo $i; ?> <i class="fas fa-star" style="font-size: 0.8rem;"></i></div>
                                <div class="progress-bar">
                                    <div class="progress" style="width: <?php echo $percent; ?>%"></div>
                                </div>
                                <div class="rating-percent"><?php echo round($percent); ?>%</div>
                            </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <div class="review-form-container">
                            <h3 class="review-form-title">
                                <?php echo $user_rated ? 'Update Your Review' : 'Write a Review'; ?>
                            </h3>
                            
                            <form id="reviewForm">
                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                
                                <div class="star-rating">
                                
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" name="rating" id="star<?php echo $i; ?>" value="<?php echo $i; ?>" <?php echo ($user_rating && $user_rating['rating'] == $i) ? 'checked' : ''; ?>>
                                        <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                    <?php endfor; ?>
                                    <span>Select Rating</span>
                                </div>
                                
                                <textarea class="review-textarea" name="review" placeholder="Share your thoughts about this product..."><?php echo $user_rating ? htmlspecialchars($user_rating['review']) : ''; ?></textarea>
                                
                                <button type="submit" class="review-submit">
                                    <?php echo $user_rated ? 'Update Review' : 'Submit Review'; ?>
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                    
                    <div class="reviews-list">
                        <?php if (count($ratings) > 0): ?>
                            <?php foreach ($ratings as $review): ?>
                                <div class="review-item">
                                    <div class="review-header">
                                        <div class="reviewer-info">
                                            <div class="reviewer-avatar">
                                                <?php echo strtoupper(substr($review['full_name'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="reviewer-name"><?php echo htmlspecialchars($review['full_name']); ?></div>
                                                <div class="review-date"><?php echo date('F j, Y', strtotime($review['created_at'])); ?></div>
                                            </div>
                                        </div>
                                        <div class="review-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="<?php echo $i <= $review['rating'] ? 'fas' : 'far'; ?> fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <div class="review-text">
                                        <?php echo nl2br(htmlspecialchars($review['review'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="no-reviews">
                                <p>No reviews yet. Be the first to review this product!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <div class="related-products">
            <h2 class="section-title">Related Products</h2>
            
            <div class="products-grid">
                <?php while ($related = $related_result->fetch_assoc()): ?>
                    <a href="productDetails.php?id=<?php echo $related['product_id']; ?>" class="product-card">
                        <div class="product-card-image">
                            <img src="<?php echo htmlspecialchars($related['image_path']); ?>" alt="<?php echo htmlspecialchars($related['product_name']); ?>">
                        </div>
                        <div class="product-card-content">
                            <h3 class="product-card-title"><?php echo htmlspecialchars($related['product_name']); ?></h3>
                            <div class="product-card-price">
                                <?php if (!empty($related['offer_price']) && $related['offer_price'] > 0): ?>
                                    <span class="card-current-price">LKR <?php echo number_format($related['offer_price'], 2); ?></span>
                                    <span class="card-original-price">LKR <?php echo number_format($related['price'], 2); ?></span>
                                <?php else: ?>
                                    <span class="card-current-price">LKR <?php echo number_format($related['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    
    <!-- Notification -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message"></span>
    </div>

    <?php include 'footer/footer.php'; ?>
    <script src="js/productDetails.js"></script>
    <script>
    </script>
</body>
</html>

