<?php
require_once 'config.php';
requireLogin();

$action = $_GET['action'] ?? 'list';

// DELETE
if ($action === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM customers WHERE customer_id = ?");
    $stmt->execute([$_GET['id']]);
    setFlash('success', 'Customer deleted successfully!');
    header("Location: customers.php");
    exit();
}

// ADD/EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    
    if (isset($_POST['customer_id']) && $_POST['customer_id']) {
        $stmt = $pdo->prepare("UPDATE customers SET full_name=?, phone=?, email=?, address=? WHERE customer_id=?");
        $stmt->execute([$fullName, $phone, $email, $address, $_POST['customer_id']]);
        setFlash('success', 'Customer updated successfully!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO customers (full_name, phone, email, address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$fullName, $phone, $email, $address]);
        setFlash('success', 'Customer added successfully!');
    }
    header("Location: customers.php");
    exit();
}

// Search
$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM customers WHERE 1=1";
$params = [];
if ($search) {
    $sql .= " AND (full_name LIKE ? OR phone LIKE ? OR email LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}
$sql .= " ORDER BY full_name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$customers = $stmt->fetchAll();

// Edit
$editCustomer = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE customer_id = ?");
    $stmt->execute([$_GET['id']]);
    $editCustomer = $stmt->fetch();
}

include 'header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <h2><?= $editCustomer ? 'Edit Customer' : 'New Customer' ?></h2>
    <form method="POST">
        <?php if ($editCustomer): ?>
            <input type="hidden" name="customer_id" value="<?= $editCustomer['customer_id'] ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" value="<?= $editCustomer ? htmlspecialchars($editCustomer['full_name']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= $editCustomer ? htmlspecialchars($editCustomer['phone']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= $editCustomer ? htmlspecialchars($editCustomer['email']) : '' ?>">
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3"><?= $editCustomer ? htmlspecialchars($editCustomer['address']) : '' ?></textarea>
        </div>
        <button type="submit" class="btn btn-success"><?= $editCustomer ? 'Update' : 'Add' ?> Customer</button>
        <a href="customers.php" class="btn btn-primary">Cancel</a>
    </form>
</div>
<?php else: ?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Customers</h2>
        <a href="customers.php?action=add" class="btn btn-success">+ New Customer</a>
    </div>
    
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by name, phone, or email..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="customers.php" class="btn btn-warning">Clear</a>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($customers as $c): ?>
            <tr>
                <td><?= $c['customer_id'] ?></td>
                <td><?= htmlspecialchars($c['full_name']) ?></td>
                <td><?= htmlspecialchars($c['phone']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= htmlspecialchars($c['address']) ?></td>
                <td class="actions">
                    <a href="customers.php?action=edit&id=<?= $c['customer_id'] ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                    <a href="customers.php?action=delete&id=<?= $c['customer_id'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Delete this customer?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

<?php include 'footer.php'; ?>