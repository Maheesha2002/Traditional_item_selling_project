<?php
session_start();
require_once 'Backend/dbconnect.php';

// Function to get category image URL
function getCategoryImageUrl($category) {
    $image_urls = [
        'Traditional Masks' => 'https://cdn.pixabay.com/photo/2018/03/18/00/59/mask-3235633_1280.jpg',
        'Batik Products' => 'https://img.freepik.com/free-photo/closeuo-colorful-textile-fabric-shop_53876-31319.jpg?t=st=1744026746~exp=1744030346~hmac=45bfe45e0a060709e512ff8fbe488f26c3da58b9272690132802ab9620167170&w=1380',
        'Brass Items' => 'https://img.freepik.com/free-photo/view-vintage-objects-still-life_23-2150348526.jpg?t=st=1744026653~exp=1744030253~hmac=4f57695470052f2a535b189aa83f3518a7b0d34f0be87e57b3ac25636a24a122&w=1380',
        'Cane Products' => 'https://img.freepik.com/free-photo/bamboo-raft_1136-339.jpg?t=st=1744026534~exp=1744030134~hmac=080fec833551f44e4a082f5edd51f341f3838821932d08022203e4595e2394aa&w=1380',
        'Traditional Jewelry' => 'https://img.freepik.com/free-photo/flat-lay-essentials-bead-working-with-scissors-thread_23-2148815807.jpg?t=st=1744026584~exp=1744030184~hmac=9d0e9fc1e247151261bcfa9289f5c19575f932a4de8095784b8a47432a4ac572&w=1380',
        'Ceylon Tea' => 'https://cdn.pixabay.com/photo/2020/06/15/09/23/green-tea-5301025_1280.jpg',
        'Spices' => 'https://img.freepik.com/free-photo/indian-condiments-with-copy-space-view_23-2148723492.jpg?t=st=1744027482~exp=1744031082~hmac=4eaecad06cf53994173449714d3ddcd235ecb58809d244e43fe69470fe77f7ed&w=1380',
        'Ceylon Cinnamon' => 'https://img.freepik.com/free-photo/cinnamon-sticks-background_158595-6298.jpg?t=st=1744027580~exp=1744031180~hmac=78e0056a09c4fb68591048c2d4fb7c0878f1a527a4baefdeae4fd22f6ace7031&w=1380',
        'Coconut Products' => 'https://cdn.pixabay.com/photo/2020/02/28/21/42/vietnam-4888683_1280.jpg',
        'Kithul Products' => 'https://img.freepik.com/free-photo/healthy-jaggery-still-life-arrangement_23-2149161538.jpg?t=st=1744028480~exp=1744032080~hmac=db3bb548b9c4afec0e96e0c732838dd2eacf01a5a3d0e35db0244b787d9fe5a2&w=1380',
        'Ceylon Gems' => 'https://cdn.pixabay.com/photo/2017/03/22/20/29/stones-2166377_1280.jpg',
        'Silver Crafts' => 'https://cdn.pixabay.com/photo/2017/04/30/11/41/diamond-2272699_1280.jpg',
        'Leather Products' => 'https://img.freepik.com/free-photo/leathercraft-hand-sewing-tool-set_1150-6388.jpg?t=st=1744029078~exp=1744032678~hmac=9b99bfd76adfb26528545877dbb314177bd28859fa49b52e44f873d627ce3c58&w=1380',
        'Traditional Pottery' => 'https://img.freepik.com/free-photo/view-ancient-pottery-vessels-earthenware_23-2151538377.jpg?t=st=1744029408~exp=1744033008~hmac=09b1b6291f4a37bd27b1965a8184e7725bd52516dee1744fb73008229553ab8e&w=1380',
        'Other' => 'https://img.freepik.com/free-photo/ornate-handmade-beadwork-necklace-complements-traditional-african-garment-generated-by-ai_188544-13800.jpg?t=st=1744029493~exp=1744033093~hmac=6af1334c1978787ae716dc41b4a4e1279722fcb83bc07900cf44d2b9874ab526&w=1380'
    ];
    
    // Return the URL for the category, or a default image if not found
    return isset($image_urls[$category]) ? $image_urls[$category] : 'https://img.freepik.com/free-photo/lohri-celebration-india_23-2151099189.jpg?t=st=1744029545~exp=1744033145~hmac=c4f04fb8641c50b4ec91a548dcf5ac3b32170c6e9bfe9ec7cfb8dd097e3ac14e&w=1380';
}

// Get category parameters from URL
$main_category = isset($_GET['main']) ? $_GET['main'] : '';
$sub_category = isset($_GET['sub']) ? $_GET['sub'] : '';

// Validate and sanitize categories
$valid_main_categories = [
    'Traditional Masks', 'Batik Products', 'Brass Items', 'Cane Products', 'Traditional Jewelry',
    'Ceylon Tea', 'Spices', 'Ceylon Cinnamon', 'Coconut Products', 'Kithul Products',
    'Ceylon Gems', 'Silver Crafts', 'Leather Products', 'Traditional Pottery', 'Other'
];

if (!in_array($main_category, $valid_main_categories)) {
    header("Location: index2.php");
    exit();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['customer_id']);
$customer_id = $is_logged_in ? $_SESSION['customer_id'] : '';

// Get wishlist items if user is logged in
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

// Get liked products if user is logged in
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

// Get subcategories for the main category
$subcategory_sql = "SELECT DISTINCT sub_category FROM products WHERE main_category = ? AND status = 'active'";
$subcategory_stmt = $conn->prepare($subcategory_sql);
$subcategory_stmt->bind_param("s", $main_category);
$subcategory_stmt->execute();
$subcategory_result = $subcategory_stmt->get_result();
$subcategories = [];
while ($row = $subcategory_result->fetch_assoc()) {
    $subcategories[] = $row['sub_category'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($main_category); ?> | Traditional Products</title>
    <link rel="stylesheet" href="css/SUB/SUBCATmask.cs">
    <link rel="stylesheet" href="Nevbar/nevbar2.css">
    <link rel="stylesheet" href="css/category.css">
    <style>
        .category-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('<?php echo getCategoryImageUrl($main_category); ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            color: white;
            padding: 100px 5%;
            text-align: center;
            min-height: 450px;
            padding-top: 170px;
        }
    
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="<?php echo $is_logged_in ? 'logged-in' : ''; ?>">
    <?php include 'Nevbar/nevbar2.php'; ?>

    <div class="category-header">
        <h1><?php echo htmlspecialchars($main_category); ?></h1>
        <p>Discover our authentic collection of traditional <?php echo htmlspecialchars(strtolower($main_category)); ?> handcrafted by skilled Sri Lankan artisans.</p>
        
        <div class="category-types">
            <button class="type-btn active" data-type="all">All Items</button>
            <?php foreach ($subcategories as $subcat): ?>
                <button class="type-btn" data-type="<?php echo htmlspecialchars($subcat); ?>"><?php echo htmlspecialchars($subcat); ?></button>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="breadcrumb">
        <a href="index2.php">Home</a>
        <i class="fas fa-chevron-right"></i>
        <span class="current"><?php echo htmlspecialchars($main_category); ?></span>
    </div>
    <div class="filter-controls">
        <div class="search-bar">
            <input type="text" id="productSearch" placeholder="Search <?php echo htmlspecialchars(strtolower($main_category)); ?>...">
            <i class="fas fa-search"></i>
        </div>
        <select id="sortSelect">
            <option value="default">Sort By</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="name-az">Name: A to Z</option>
            <option value="name-za">Name: Z to A</option>
            <option value="rating-high">Rating: High to Low</option>
        </select>
    </div>

    <div class="products-grid">
        <?php
            // Build SQL query based on parameters
            $sql = "SELECT p.*, s.shop_name, pi.image_path,
                    (SELECT COUNT(*) FROM product_likes WHERE product_id = p.product_id) as like_count,
                    (SELECT COUNT(*) FROM product_comments WHERE product_id = p.product_id AND parent_id IS NULL) as comment_count,
                    (SELECT AVG(rating) FROM product_ratings WHERE product_id = p.product_id) as avg_rating,
                    (SELECT COUNT(*) FROM product_ratings WHERE product_id = p.product_id) as rating_count
                    FROM products p
                    JOIN sellers s ON p.seller_id = s.seller_id
                    LEFT JOIN product_images pi ON p.product_id = pi.product_id
                    WHERE p.main_category = ? AND p.status = 'active'";
            
            $params = [$main_category];
            $types = "s";
            
            if (!empty($sub_category)) {
                $sql .= " AND p.sub_category = ?";
                $params[] = $sub_category;
                $types .= "s";
            }
            
            $sql .= " GROUP BY p.product_id ORDER BY p.created_at DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                echo '<div class="no-products"><p>No products found in this category.</p></div>';
            }

            while ($row = $result->fetch_assoc()) {
                $product_id = $row['product_id'];
                $avg_rating = $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
                $rating_count = $row['rating_count'] ? $row['rating_count'] : 0;
                $like_count = $row['like_count'] ? $row['like_count'] : 0;
                $comment_count = $row['comment_count'] ? $row['comment_count'] : 0;
                $is_liked = in_array($product_id, $liked_products);
                ?>
                <div class="product-item" data-type="<?php echo htmlspecialchars($row['sub_category']); ?>"
                    data-name="<?php echo htmlspecialchars($row['product_name']); ?>" 
                    data-price="<?php echo $row['price']; ?>"
                    data-rating="<?php echo $avg_rating; ?>">

                    <div class="product-image">
                        <?php if ($row['offer_price']): ?>
                            <div class="offer-badge">
                                <?php
                                $discount = (($row['price'] - $row['offer_price']) / $row['price']) * 100;
                                echo round($discount) . '% OFF';
                                ?>
                            </div>
                        <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>"
                            alt="<?php echo htmlspecialchars($row['product_name']); ?>">
                        <div class="product-overlay">
                            <button
                                class="add-wishlist <?php echo in_array($row['product_id'], $wishlist_products) ? 'active' : ''; ?>"
                                data-product-id="<?php echo $row['product_id']; ?>">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                    </div>

                    <div class="product-details">
                        <div class="product-type"><?php echo htmlspecialchars($row['sub_category']); ?></div>
                        <a href="productDetails.php?id=<?php echo $row['product_id']; ?>" class="product-name-link">
                            <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                        </a>
                        
                        <!-- Product Rating -->
                        <div class="product-rating" data-product-id="<?php echo $product_id; ?>">
                            <div class="stars">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $avg_rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } elseif ($i - 0.5 <= $avg_rating) {
                                        echo '<i class="fas fa-star-half-alt"></i>';
                                    } else {
                                        echo '<i class="far fa-star"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <span class="rating-count"><?php echo $avg_rating; ?> (<?php echo $rating_count; ?> reviews)</span>
                        </div>
                        
                        <div class="product-meta">
                            <a href="shop_profile.php?seller_id=<?php echo $row['seller_id']; ?>" class="artisan">
                                <i class="fas fa-store"></i>
                                By <?php echo htmlspecialchars($row['shop_name']); ?>
                            </a>
                        </div>

                        <div class="stock-status">
                            <?php if ($row['quantity'] > 0): ?>
                                <span class="in-stock">
                                    <i class="fas fa-check-circle"></i> In Stock
                                </span>
                            <?php else: ?>
                                <span class="out-of-stock">
                                    <i class="fas fa-times-circle"></i> Out of Stock
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="product-price">
                            <?php if ($row['offer_price']): ?>
                                <div class="price-container">
                                    <span class="original-price">
                                        <i class="fas fa-tag"></i>
                                        LKR <?php echo number_format($row['price'], 2); ?>
                                    </span>
                                    <span class="offer-price">
                                        <i class="fas fa-tags"></i>
                                        LKR <?php echo number_format($row['offer_price'], 2); ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <span class="price">
                                    <i class="fas fa-tag"></i>
                                    LKR <?php echo number_format($row['price'], 2); ?>
                                </span>
                            <?php endif; ?>
                            <button class="add-cart" 
                                onclick="addToCart(this)"
                                <?php echo $row['quantity'] <= 0 ? 'disabled' : ''; ?>
                                data-product='<?php echo json_encode([                                    "product_id" => $row["product_id"],
                                    "quantity" => 1,
                                    "price" => $row["price"],
                                    "offer_price" => $row["offer_price"]
                                ]); ?>'>
                                <i class="fas fa-shopping-cart"></i>
                                <?php echo $row['quantity'] > 0 ? 'Add to Cart' : 'Out of Stock'; ?>
                            </button>
                        </div>
                        
                        <!-- Product Social Actions -->
                        <div class="product-social">
                            <button class="like-btn <?php echo $is_liked ? 'active' : ''; ?>" 
                                    data-product-id="<?php echo $product_id; ?>">
                                <i class="<?php echo $is_liked ? 'fas' : 'far'; ?> fa-thumbs-up"></i>
                                <span class="like-count"><?php echo $like_count; ?></span> Likes
                            </button>
                            
                            <button class="comment-btn" data-product-id="<?php echo $product_id; ?>">
                                <i class="far fa-comment"></i>
                                <span class="comment-count"><?php echo $comment_count; ?></span> Comments
                            </button>
                            
                            <button class="rate-btn" data-product-id="<?php echo $product_id; ?>" 
                                    data-product-name="<?php echo htmlspecialchars($row['product_name']); ?>">
                                <i class="far fa-star"></i> Rate
                            </button>
                        </div>
                        
                        <!-- Comments Section (Hidden by default) -->
                        <div class="comments-section" id="comments-section-<?php echo $product_id; ?>">

                            <?php if ($is_logged_in): ?>
                                <div class="comment-form">
                                    <input type="text" id="comment-input-<?php echo $product_id; ?>" class="comment-input" placeholder="Write a comment...">
                                    <button class="comment-submit" data-product-id="<?php echo $product_id; ?>">Post</button>
                                </div>

                            <?php else: ?>
                                <div class="login-prompt">
                                    <p>Please <a href="login.php">login</a> to comment on this product.</p>
                                </div>
                            <?php endif; ?>
                            
                            <div class="comments-list" id="comments-list-<?php echo $product_id; ?>">
                                <!-- Comments will be loaded here via AJAX -->
                                <div class="loading-comments">Loading comments...</div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        ?>
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

    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message"></span>
    </div>

    <?php include 'footer/footer.php'; ?>
    

    <script src="js/category.js"></script>
<!-- Add this right before including the category.js file -->
 <style>
    /* Reply styles */
.replies {
    margin-left: 20px;
    border-left: 2px solid #eee;
    padding-left: 15px;
    margin-top: 10px;
}

.reply-item {
    margin-bottom: 15px;
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 10px;
}

.reply-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.reply-user {
    display: flex;
    align-items: center;
}

.reply-user-img {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    margin-right: 8px;
}

.reply-user-info {
    display: flex;
    flex-direction: column;
}

.reply-user-name {
    font-weight: 600;
    font-size: 0.9rem;
}

.reply-date {
    font-size: 0.8rem;
    color: #777;
}

.reply-content p {
    margin: 0;
    font-size: 0.95rem;
}

.reply-actions {
    display: flex;
    gap: 5px;
}

.reply-edit-btn, .reply-delete-btn {
    background: none;
    border: none;
    color: #777;
    cursor: pointer;
    padding: 2px;
}

.reply-edit-btn:hover, .reply-delete-btn:hover {
    color: #e67e22;
}

.replies-toggle {
    cursor: pointer;
    color: #e67e
}
 </style>
<script>
    // Pass login status from PHP to JavaScript
    const userLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const customerId = <?php echo $is_logged_in ? "'" . $customer_id . "'" : 'null'; ?>;
    
</script>
</body>

</html>

           

        