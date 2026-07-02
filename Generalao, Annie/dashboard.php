<?php
require_once 'config.php';
requireLogin();

// Get statistics
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$totalCustomers = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM orders WHERE status = 'completed'")->fetchColumn();

// Get recent orders
$recentOrders = $pdo->query("
    SELECT o.*, c.full_name as customer_name, s.service_name 
    FROM orders o 
    JOIN customers c ON o.customer_id = c.customer_id 
    JOIN services s ON o.service_id = s.service_id 
    ORDER BY o.created_at DESC LIMIT 5
")->fetchAll();

include 'header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <h3><?= $totalOrders ?></h3>
        <p>Total Orders</p>
    </div>
    <div class="stat-card">
        <h3><?= $pendingOrders ?></h3>
        <p>Pending Orders</p>
    </div>
    <div class="stat-card">
        <h3><?= $totalCustomers ?></h3>
        <p>Total Customers</p>
    </div>
    <div class="stat-card">
        <h3>₱<?= number_format($totalRevenue, 2) ?></h3>
        <p>Total Revenue</p>
    </div>
</div>

<div class="card">
    <h2>Recent Orders</h2>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Weight</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentOrders as $order): ?>
            <tr>
                <td>#<?= $order['order_id'] ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= htmlspecialchars($order['service_name']) ?></td>
                <td><?= $order['weight_kg'] ?> kg</td>
                <td>₱<?= number_format($order['total_price'], 2) ?></td>
                <td><span class="badge badge-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div style="margin-top: 15px;">
        <a href="orders.php" class="btn btn-primary">View All Orders</a>
    </div>
</div>

<?php include 'footer.php'; ?>