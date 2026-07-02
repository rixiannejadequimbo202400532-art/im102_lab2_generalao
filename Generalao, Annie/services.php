<?php
require_once 'config.php';
requireAdmin(); // This blocks non-admin users!

$action = $_GET['action'] ?? 'list';

// DELETE
if ($action === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM services WHERE service_id = ?");
    $stmt->execute([$_GET['id']]);
    setFlash('success', 'Service deleted successfully!');
    header("Location: services.php");
    exit();
}

// ADD/EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['service_name'];
    $desc = $_POST['description'];
    $price = $_POST['price_per_kg'];
    $hours = $_POST['estimated_hours'];
    $active = isset($_POST['is_active']) ? 1 : 0;
    
    if (isset($_POST['service_id']) && $_POST['service_id']) {
        $stmt = $pdo->prepare("UPDATE services SET service_name=?, description=?, price_per_kg=?, estimated_hours=?, is_active=? WHERE service_id=?");
        $stmt->execute([$name, $desc, $price, $hours, $active, $_POST['service_id']]);
        setFlash('success', 'Service updated successfully!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO services (service_name, description, price_per_kg, estimated_hours, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $hours, $active]);
        setFlash('success', 'Service added successfully!');
    }
    header("Location: services.php");
    exit();
}

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM services WHERE service_name LIKE ? ORDER BY service_name";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%"]);
$services = $stmt->fetchAll();

$editService = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE service_id = ?");
    $stmt->execute([$_GET['id']]);
    $editService = $stmt->fetch();
}

include 'header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <h2><?= $editService ? 'Edit Service' : 'New Service' ?></h2>
    <form method="POST">
        <?php if ($editService): ?>
            <input type="hidden" name="service_id" value="<?= $editService['service_id'] ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Service Name</label>
            <input type="text" name="service_name" value="<?= $editService ? htmlspecialchars($editService['service_name']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= $editService ? htmlspecialchars($editService['description']) : '' ?></textarea>
        </div>
        <div class="form-group">
            <label>Price per kg (₱)</label>
            <input type="number" step="0.01" name="price_per_kg" value="<?= $editService ? $editService['price_per_kg'] : '' ?>" required>
        </div>
        <div class="form-group">
            <label>Estimated Hours</label>
            <input type="number" name="estimated_hours" value="<?= $editService ? $editService['estimated_hours'] : '' ?>" required>
        </div>
        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" <?= ($editService && $editService['is_active']) || !$editService ? 'checked' : '' ?>> Active
            </label>
        </div>
        <button type="submit" class="btn btn-success"><?= $editService ? 'Update' : 'Add' ?> Service</button>
        <a href="services.php" class="btn btn-primary">Cancel</a>
    </form>
</div>
<?php else: ?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Services Management (Admin Only)</h2>
        <a href="services.php?action=add" class="btn btn-success">+ New Service</a>
    </div>
    
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search services..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="services.php" class="btn btn-warning">Clear</a>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Service Name</th>
                <th>Price/kg</th>
                <th>Est. Hours</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $s): ?>
            <tr>
                <td><?= $s['service_id'] ?></td>
                <td><?= htmlspecialchars($s['service_name']) ?></td>
                <td>₱<?= number_format($s['price_per_kg'], 2) ?></td>
                <td><?= $s['estimated_hours'] ?> hrs</td>
                <td><?= $s['is_active'] ? 'Active' : 'Inactive' ?></td>
                <td class="actions">
                    <a href="services.php?action=edit&id=<?= $s['service_id'] ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                    <a href="services.php?action=delete&id=<?= $s['service_id'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Delete this service?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

<?php include 'footer.php'; ?>