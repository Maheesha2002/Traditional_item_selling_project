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

// Get all orders for the customer
$orders_sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC";
$orders_stmt = $conn->prepare($orders_sql);
$orders_stmt->bind_param("s", $customer_id);
$orders_stmt->execute();
$orders_result = $orders_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Traditional Products</title>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="nevbar/nevbar2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .orders-container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
            margin-top: 100px;
        }
        
        .orders-header {
            margin-bottom: 30px;
        }
        
        .orders-header h1 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
        }
        
        .orders-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .order-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            overflow: hidden;
        }
        
        .order-header {
            background: #f9f9f9;
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .order-id {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
        }
        
        .order-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .order-status {
            display: flex;
            align-items: center;
            gap: 15px;
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
        
        .order-body {
            padding: 20px;
        }
        
        .order-items {
            margin-bottom: 20px;
        }
        
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .order-item:last-child {
            border-bottom: none;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            border-radius: 5px;
            overflow: hidden;
            margin-right: 15px;
        }
        
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .item-details {
            flex: 1;
        }
        
        .item-details h4 {
            margin: 0 0 5px 0;
            font-size: 1rem;
            color: #333;
        }
        
        .item-details p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .item-price {
            font-weight: 600;
            color: #333;
        }
        
        .order-summary {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-row:last-child {
            margin-bottom: 0;
        }
        
        .summary-row.total {
            font-weight: 600;
            font-size: 1.1rem;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .order-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .order-actions a {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .view-details {
            background: #e67e22;
            color: white;
        }
        
        .view-details:hover {
            background: #d35400;
        }
        
        .track-order {
            background: #f5f5f5;
            color: #333;
        }
        
        .track-order:hover {
            background: #e0e0e0;
        }
        
        .empty-orders {
            text-align: center;
            padding: 50px 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .empty-orders i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-orders h2 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 10px;
        }
        
        .empty-orders p {
            color: #666;
            margin-bottom: 20px;
        }
        
        .shop-now-btn {
            display: inline-block;
            background: #e67e22;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .shop-now-btn:hover {
            background: #d35400;
        }
    </style>
</head>
<body>
    <div class="orders-container">
        <div class="orders-header">
            <h1><i class="fas fa-shopping-bag"></i> My Orders</h1>
            <p>Track and manage your orders</p>
        </div>
        
        <?php if ($orders_result->num_rows > 0): ?>
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <?php
                // Get order items
                $items_sql = "SELECT oi.*, p.product_name, 
                             (SELECT pi.image_path FROM product_images pi WHERE pi.product_id = p.product_id LIMIT 1) as image_path
                             FROM order_items oi
                             JOIN products p ON oi.product_id = p.product_id
                             WHERE oi.order_id = ?
                             LIMIT 3"; // Limit to 3 items for display
                $items_stmt = $conn->prepare($items_sql);
                $items_stmt->bind_param("s", $order['order_id']);
                $items_stmt->execute();
                $items_result = $items_stmt->get_result();
                
                // Count total items
                $count_sql = "SELECT COUNT(*) as total FROM order_items WHERE order_id = ?";
                $count_stmt = $conn->prepare($count_sql);
                $count_stmt->bind_param("s", $order['order_id']);
                $count_stmt->execute();
                $count_result = $count_stmt->get_result();
                $count_row = $count_result->fetch_assoc();
                $total_items = $count_row['total'];
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-id">Order #<?php echo htmlspecialchars($order['order_id']); ?></div>
                            <div class="order-date"><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></div>
                        </div>
                        <div class="order-status">
                            <span class="status-badge <?php echo $order['status']; ?>">
                                <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                            </span>
                            <span class="payment-badge <?php echo $order['payment_status']; ?>">
                                Payment: <?php echo ucfirst(htmlspecialchars($order['payment_status'])); ?>
                            </span>
                        </div>
                    </div>
                    <div class="order-body">
                        <div class="order-items">
                            <?php while ($item = $items_result->fetch_assoc()): ?>
                                <div class="order-item">
                                    <div class="item-image">
                                        <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                                    </div>
                                    <div class="item-details">
                                        <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <div class="item-price">
                                        LKR <?php echo number_format($item['item_total'], 2); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                            
                            <?php if ($total_items > 3): ?>
                                <div class="more-items">
                                    <p>+ <?php echo $total_items - 3; ?> more item(s)</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-summary">
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
                        
                        <div class="order-actions">
                            <a href="order-confirmation.php?order_id=<?php echo $order['order_id']; ?>" class="view-details">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            <?php if ($order['status'] != 'delivered' && $order['status'] != 'cancelled'): ?>
                                <a href="track-order.php?order_id=<?php echo $order['order_id']; ?>" class="track-order">
                                    <i class="fas fa-truck"></i> Track Order
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-orders">
                <i class="fas fa-shopping-bag"></i>
                <h2>No Orders Yet</h2>
                <p>You haven't placed any orders yet. Start shopping to see your orders here.</p>
                <a href="index2.php" class="shop-now-btn">Shop Now</a>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include 'footer/footer.php'; ?>
</body>
</html>
