<?php
session_start();

if (isset($_SESSION['customer_id']) && $_SESSION['user_type'] === 'customer') {
    include 'Nevbar/nevbar2.php';
    $customer_id = $_SESSION['customer_id'];
} else {
    header("Location: index.html");
    exit();
}

require_once 'Backend/dbconnect.php';

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    header("Location: index2.php");
    exit();
}

$order_id = $_GET['order_id'];

// Get order details
$order_sql = "SELECT o.*, sa.* FROM orders o
              JOIN shipping_addresses sa ON o.address_id = sa.address_id
              WHERE o.order_id = ? AND o.customer_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("ss", $order_id, $customer_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows === 0) {
    header("Location: index2.php");
    exit();
}

$order = $order_result->fetch_assoc();

// Get order items
$items_sql = "SELECT oi.*, p.product_name, p.main_category, p.price as original_price, 
             p.offer_price, s.shop_name,
             (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_path
             FROM order_items oi
             JOIN products p ON oi.product_id = p.product_id
             JOIN sellers s ON p.seller_id = s.seller_id
             WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("s", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation | Traditional Products</title>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="nevbar/nevbar2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .confirmation-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://img.freepik.com/free-vector/delivery-courier-service-flat-composition-with-cityscape-delivery-boy-riding-bike-with-smartphone-tracking-app-vector-illustration_1284-74349.jpg?t=st=1744897100~exp=1744900700~hmac=585036074d2ef10ddf377ddf29580291ac9ce5730055e3d0a1b887bd23c91182&w=1800');
            background-size: cover;
            background-position: center;
            height: 370px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            margin-bottom: 40px;
        }

        .hero-content h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .confirmation-breadcrumb {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .confirmation-breadcrumb a {
            color: #e67e22;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .confirmation-breadcrumb a:hover {
            color: #d35400;
        }

        .confirmation-breadcrumb i {
            font-size: 0.8rem;
            color: #e67e22;
        }

        .confirmation-breadcrumb span {
            color: #fff;
        }
        
        .confirmation-container {
            max-width: 1200px;
            margin: 0 auto 50px;
            padding: 0 20px;
        }
        
        .confirmation-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .confirmation-header i {
            font-size: 5rem;
            color: #4CAF50;
            margin-bottom: 20px;
            display: block;
        }
        
        .confirmation-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #333;
        }
        
        .confirmation-header p {
            font-size: 1.2rem;
            color: #666;
        }
        
        .order-details {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .order-details h2 {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5rem;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .detail-row .label {
            font-weight: 600;
            color: #555;
        }
        
        .detail-row .value {
            color: #333;
        }
        
        .shipping-address {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .shipping-address h2 {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5rem;
        }
        
        .address-details p {
            margin: 5px 0;
            color: #555;
            font-size: 1.1rem;
        }
        
        .order-items {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .order-items h2 {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5rem;
        }
        
        .item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            margin-right: 20px;
            position: relative;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .item-image .item-quantity {
            position: absolute;
            top: -1px;
            background:rgb(167, 37, 34);
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-details h3 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            color: #333;
        }
        
        .item-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .item-seller {
            color: #e67e22 !important;
            font-weight: 500;
        }
        
        .item-price {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }
        
        .order-summary {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 30px;
        }
        
        .order-summary h2 {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
            color: #333;
            font-size: 1.5rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .summary-row.total {
            font-weight: 600;
            font-size: 1.3rem;
            border-top: 1px solid #eee;
            padding-top: 15px;
            margin-top: 15px;
        }
        
        .actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 40px;
        }
        
        .actions a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .continue-shopping {
            background: #f5f5f5;
            color: #333;
        }
        
        .continue-shopping:hover {
            background: #e0e0e0;
        }
        
        .view-orders {
            background: #e67e22;
            color: white;
        }
        
        .view-orders:hover {
            background: #d35400;
        }
        
        .payment-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .payment-badge.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .payment-badge.paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .status-badge.pending {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-badge.processing {
            background: #e0cffc;
            color: #5a2ca0;
        }
        
        .status-badge.shipped {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-badge.delivered {
            background: #d4edda;
            color: #155724;
        }
        
        .original-price {
            text-decoration: line-through;
            color: #999;
            margin-right: 8px;
            font-size: 0.9em;
        }
        
        .offer-price {
            color: #e67e22;
            font-weight: bold;
        }
        
        .notification {
            position: fixed;
            top: 80px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(120%);
            transition: transform 0.3s ease;
            z-index: 1000;
            box-shadow: 0 3px 6px rgba(0,0,0,0.16);
        }

        .notification.success {
            background: #4CAF50;
            color: white;
        }

        .notification.warning {
            background: #ff9800;
            color: white;
        }

        .notification.error {
            background: #f44336;
            color: white;
        }

        .notification i {
            font-size: 20px;
        }

        .notification.show {
            transform: translateX(0);
        }
        
        @media (max-width: 768px) {
            .confirmation-hero {
                height: 200px;
            }
            
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .confirmation-header i {
                font-size: 4rem;
            }
            
            .confirmation-header h1 {
                font-size: 2rem;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .actions a {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <section class="confirmation-hero">
        <div class="hero-content">
            <h1>Order Confirmation</h1>
            <div class="confirmation-breadcrumb">
                <a href="index2.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="cart.php">Cart</a>
                <i class="fas fa-chevron-right"></i>
                <a href="checkout.php">Checkout</a>
                <i class="fas fa-chevron-right"></i>
                <span>Confirmation</span>
            </div>
        </div>
    </section>

    <div class="confirmation-container">
        <div class="confirmation-header">
            <i class="fas fa-check-circle"></i>
            <h1>Thank You for Your Order!</h1>
            <p>Your order has been received and is now being processed.</p>
        </div>
        
        <div class="order-details">
        <h2><i class="fas fa-info-circle"></i> Order Information</h2>
            <div class="detail-row">
                <span class="label">Order Number:</span>
                <span class="value"><?php echo htmlspecialchars($order_id); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Order Date:</span>
                <span class="value"><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Payment Method:</span>
                <span class="value"><?php echo ucfirst(htmlspecialchars($order['payment_method'])); ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Payment Status:</span>
                <span class="value">
                <span class="payment-badge <?php echo $order['payment_status']; ?>">
                        <?php echo ucfirst(htmlspecialchars($order['payment_status'])); ?>
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span class="label">Order Status:</span>
                <span class="value">
                    <span class="status-badge <?php echo $order['status']; ?>">
                        <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                    </span>
                </span>
            </div>
        </div>
        
        <div class="shipping-address">
            <h2><i class="fas fa-shipping-fast"></i> Shipping Address</h2>
            <div class="address-details">
                <p><strong><?php echo htmlspecialchars($order['full_name']); ?></strong></p>
                <p><?php echo htmlspecialchars($order['address_line1']); ?></p>
                <?php if (!empty($order['address_line2'])): ?>
                    <p><?php echo htmlspecialchars($order['address_line2']); ?></p>
                <?php endif; ?>
                <p><?php echo htmlspecialchars($order['city']) . ', ' . htmlspecialchars($order['province']); ?></p>
                <p><?php echo htmlspecialchars($order['postal_code']); ?></p>
                <p>Phone: <?php echo htmlspecialchars($order['phone']); ?></p>
                <p>Email: <?php echo htmlspecialchars($order['email']); ?></p>
            </div>
        </div>
        
        <div class="order-items">
            <h2><i class="fas fa-box-open"></i> Order Items</h2>
            <?php 
            $items_result->data_seek(0); // Reset result pointer
            while ($item = $items_result->fetch_assoc()): 
            ?>
                <div class="item">
                    <div class="item-image">
                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                        <span class="item-quantity"><?php echo $item['quantity']; ?></span>
                    </div>
                    <div class="item-details">
                        <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                        <p class="item-seller">Seller: <?php echo htmlspecialchars($item['shop_name']); ?></p>
                        <p>Category: <?php echo htmlspecialchars($item['main_category']); ?></p>
                        <p>
                            <?php if (isset($item['offer_price']) && $item['offer_price'] > 0): ?>
                                <span class="original-price">LKR <?php echo number_format($item['original_price'], 2); ?></span>
                                <span class="offer-price">LKR <?php echo number_format($item['offer_price'], 2); ?></span>
                            <?php else: ?>
                                <span>Price: LKR <?php echo number_format($item['price'], 2); ?></span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="item-price">
                        LKR <?php echo number_format($item['item_total'], 2); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        
        <div class="order-summary">
            <h2><i class="fas fa-receipt"></i> Order Summary</h2>
            <div class="summary-row">
                <span>Subtotal</span>
                <span>LKR <?php echo number_format($order['subtotal'], 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span>LKR <?php echo number_format($order['shipping_fee'], 2); ?></span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span>LKR <?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
        </div>
        
        <div class="actions">
            <a href="index2.php" class="continue-shopping">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
            <a href="my-orders.php" class="view-orders">
                <i class="fas fa-list"></i> View My Orders
            </a>
        </div>
    </div>
    
    <!-- Notification for actions -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span id="notification-message"></span>
    </div>
    
    <?php include 'footer/footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show a success notification when the page loads
            showNotification('Order confirmed successfully!', 'success');
            
            // Track order view event (can be used for analytics)
            trackOrderConfirmation('<?php echo $order_id; ?>');
        });
        
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
        
        // Function to track order confirmation (placeholder for analytics)
        function trackOrderConfirmation(orderId) {
            // This could be connected to Google Analytics or other tracking systems
            console.log('Order confirmation viewed:', orderId);
            
            // Example of how you might send this to a tracking endpoint
            /*
            fetch('Backend/analytics/track_order_view.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
                    event: 'order_confirmation_view'
                })
            });
            */
        }
        
        // Print order receipt
        function printReceipt() {
            window.print();
        }
    </script>
</body>
</html>

