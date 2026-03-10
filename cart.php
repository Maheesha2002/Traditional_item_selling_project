<?php
session_start();

if (isset($_SESSION['customer_id']) && $_SESSION['user_type'] === 'customer') {
    include 'Nevbar/nevbar2.php';
} else {
    header("Location: index.html");
    exit();
}

require_once 'Backend/dbconnect.php';
$customer_id = $_SESSION['customer_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart | Traditional Products</title>
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/cart2.css">
    <link rel="stylesheet" href="nevbar/nevbar2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .price-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9em;
        }

        .offer-price {
            color: #e67e22;
            font-weight: bold;
        }

        .regular-price {
            font-weight: bold;
            color: #333;
        }

        .item-total {
            display: none;
            /* Hide the total price line */
        }

        .discount-badge {
            background-color: #e74c3c;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8em;
            margin-left: 5px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <section class="cart-hero">
        <div class="hero-content">
            <h1>Your Shopping Cart</h1>
            <div class="cart-breadcrumb">
                <a href="index2.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Shopping Cart</span>
            </div>
        </div>
    </section>

    <div class="cart-container">
        <h1><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h1>

        <div class="cart-grid">
            <div class="cart-items">
                <?php
                $cart_sql = "SELECT c.*, p.product_name, p.price, p.offer_price, p.main_category, s.shop_name, 
                            (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_path
                            FROM cart c
                            JOIN products p ON c.product_id = p.product_id
                            JOIN sellers s ON p.seller_id = s.seller_id
                            WHERE c.customer_id = ?";

                $cart_stmt = $conn->prepare($cart_sql);
                $cart_stmt->bind_param("s", $customer_id);
                $cart_stmt->execute();
                $cart_result = $cart_stmt->get_result();

                $subtotal = 0;
                $shipping = 350.00;

                if ($cart_result->num_rows > 0) {
                    while ($item = $cart_result->fetch_assoc()) {
                        $has_offer = isset($item['offer_price']) && $item['offer_price'] > 0;
                        $display_price = $has_offer ? $item['offer_price'] : $item['price'];
                        $item_total = $display_price * $item['quantity'];
                        $subtotal += $item_total;

                        // Calculate discount percentage if there's an offer
                        $discount_percentage = 0;
                        if ($has_offer) {
                            $discount_percentage = round((($item['price'] - $item['offer_price']) / $item['price']) * 100);
                        }
                        ?>
                        <div class="cart-item" data-id="<?php echo $item['cart_id']; ?>">
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>"
                                alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="item-image">

                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                <p class="item-category">
                                    <?php echo htmlspecialchars($item['main_category']); ?> |
                                    By <?php echo htmlspecialchars($item['shop_name']); ?>
                                </p>
                                <div class="price-info">
                                    <?php if ($has_offer): ?>
                                        <div>
                                            <span class="original-price">
                                                <i class="fas fa-tag"></i> LKR <?php echo number_format($item['price'], 2); ?>
                                            </span>
                                            <span class="discount-badge">-<?php echo $discount_percentage; ?>%</span>
                                        </div>
                                        <div class="offer-price">
                                            <i class="fas fa-tags"></i> LKR <?php echo number_format($item['offer_price'], 2); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="regular-price">
                                            <i class="fas fa-tag"></i> LKR <?php echo number_format($item['price'], 2); ?>
                                        </div>
                                    <?php endif; ?>
                                    <p class="item-total">
                                        Total: LKR <?php echo number_format($item_total, 2); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="quantity-controls">
                                <button class="quantity-btn decrease"
                                    onclick="updateQuantity(<?php echo $item['cart_id']; ?>, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="quantity"><?php echo $item['quantity']; ?></span>
                                <button class="quantity-btn increase"
                                    onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>

                            <button class="remove-item" onclick="removeItem(<?php echo $item['cart_id']; ?>)">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-cart">
                            <i class="fas fa-shopping-cart"></i>
                            <h2>Your cart is empty</h2>
                            <p>Looks like you haven\'t added any products to your cart yet.</p>
                            <a href="index2.php" class="continue-shopping">Continue Shopping</a>
                          </div>';
                }
                ?>
            </div>

            <div class="cart-summary">
                <h2>Order Summary</h2>
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span class="subtotal">LKR <?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping</span>
                    <span class="shipping">LKR <?php echo number_format($shipping, 2); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span class="total">LKR <?php echo number_format($subtotal + $shipping, 2); ?></span>
                </div>
                <?php if ($cart_result->num_rows > 0): ?>
                    <button class="checkout-btn" onclick="window.location.href='checkout.php'">
                        Proceed to Checkout
                    </button>
                <?php else: ?>
                    <button class="checkout-btn disabled" disabled>Proceed to Checkout</button>
                <?php endif; ?>
                <a href="index2.php" class="continue-shopping-link">
                    <i class="fas fa-arrow-left"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message"></span>
    </div>

    <?php include 'footer/footer.php'; ?>
    <script src="js/cart.js"></script>
</body>

</html>