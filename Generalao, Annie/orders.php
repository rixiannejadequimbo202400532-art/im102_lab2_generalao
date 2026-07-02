<?php
require_once 'config.php';
requireLogin();

$action = $_GET['action'] ?? 'list';
$message = '';

// DELETE
if ($action === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->execute([$_GET['id']]);
    setFlash('success', 'Order deleted successfully!');
    header("Location: orders.php");
    exit();
}

// ADD/EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerId = $_POST['customer_id'];
    $serviceId = $_POST['service_id'];
    $weight = $_POST['weight_kg'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    
    // Calculate total price
    $stmt = $pdo->prepare("SELECT price_per_kg FROM services WHERE service_id = ?");
    $stmt->execute([$serviceId]);
    $pricePerKg = $stmt->fetchColumn();
    $totalPrice = $weight * $pricePerKg;
    
    if (isset($_POST['order_id']) && $_POST['order_id']) {
        // UPDATE
        $stmt = $pdo->prepare("UPDATE orders SET customer_id=?, service_id=?, weight_kg=?, total_price=?, status=?, notes=? WHERE order_id=?");
        $stmt->execute([$customerId, $serviceId, $weight, $totalPrice, $status, $notes, $_POST['order_id']]);
        setFlash('success', 'Order updated successfully!');
    } else {
        // CREATE
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, service_id, weight_kg, total_price, status, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customerId, $serviceId, $weight, $totalPrice, $status, $notes, $_SESSION['user_id']]);
        setFlash('success', 'Order created successfully!');
    }
    header("Location: orders.php");
    exit();
}

// Get data for dropdowns
$customers = $pdo->query("SELECT * FROM customers ORDER BY full_name")->fetchAll();
$services = $pdo->query("SELECT * FROM services WHERE is_active = 1 ORDER BY service_name")->fetchAll();

// Search functionality
$search = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$sql = "SELECT o.*, c.full_name as customer_name, s.service_name, u.full_name as created_by_name 
        FROM orders o 
        JOIN customers c ON o.customer_id = c.customer_id 
        JOIN services s ON o.service_id = s.service_id 
        LEFT JOIN users u ON o.created_by = u.user_id 
        WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (c.full_name LIKE ? OR s.service_name LIKE ? OR o.notes LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($statusFilter) {
    $sql .= " AND o.status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Get single order for edit
$editOrder = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->execute([$_GET['id']]);
    $editOrder = $stmt->fetch();
}

include 'header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <h2><?= $editOrder ? 'Edit Order' : 'New Order' ?></h2>
    <form method="POST">
        <?php if ($editOrder): ?>
            <input type="hidden" name="order_id" value="<?= $editOrder['order_id'] ?>">
        <?php endif; ?>
        
        <div class="form-group">
            <label>Customer</label>
            <select name="customer_id" required>
                <option value="">Select Customer</option>
                <?php foreach ($customers as $c): ?>
                <option value="<?= $c['customer_id'] ?>" <?= ($editOrder && $editOrder['customer_id'] == $c['customer_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['full_name']) ?> - <?= $c['phone'] ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Service</label>
            <select name="service_id" required>
                <option value="">Select Service</option>
                <?php foreach ($services as $s): ?>
                <option value="<?= $s['service_id'] ?>" <?= ($editOrder && $editOrder['service_id'] == $s['service_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['service_name']) ?> - ₱<?= number_format($s['price_per_kg'], 2) ?>/kg
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Weight (kg)</label>
            <input type="number" step="0.01" name="weight_kg" value="<?= $editOrder ? $editOrder['weight_kg'] : '' ?>" required>
        </div>
        
        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <?php foreach (['pending','processing','ready','completed','cancelled'] as $st): ?>
                <option value="<?= $st ?>" <?= ($editOrder && $editOrder['status'] == $st) ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" rows="3"><?= $editOrder ? htmlspecialchars($editOrder['notes']) : '' ?></textarea>
        </div>
        
        <button type="submit" class="btn btn-success"><?= $editOrder ? 'Update Order' : 'Create Order' ?></button>
        <a href="orders.php" class="btn btn-primary">Cancel</a>
    </form>
</div>

<?php else: ?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Orders Management</h2>
        <a href="orders.php?action=add" class="btn btn-success">+ New Order</a>
    </div>
    
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by customer, service, or notes..." value="<?= htmlspecialchars($search) ?>">
        <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            <option value="">All Status</option>
            <?php foreach (['pending','processing','ready','completed','cancelled'] as $st): ?>
            <option value="<?= $st ?>" <?= $statusFilter == $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="orders.php" class="btn btn-warning">Clear</a>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Weight</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created By</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= $order['order_id'] ?></td>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= htmlspecialchars($order['service_name']) ?></td>
                <td><?= $order['weight_kg'] ?> kg</td>
                <td>₱<?= number_format($order['total_price'], 2) ?></td>
                <td><span class="badge badge-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                <td><?= htmlspecialchars($order['created_by_name']) ?></td>
                <td><?= date('M d, Y H:i', strtotime($order['created_at'])) ?></td>
                <td class="actions">
                    <a href="orders.php?action=edit&id=<?= $order['order_id'] ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                    <a href="orders.php?action=delete&id=<?= $order['order_id'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Are you sure you want to delete this order?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($orders)): ?>
            <tr><td colspan="9" style="text-align: center; color: #999;">No orders found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

<?php include 'footer.php'; ?>