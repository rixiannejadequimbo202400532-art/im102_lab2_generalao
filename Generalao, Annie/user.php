<?php
require_once 'config.php';
requireAdmin();

$action = $_GET['action'] ?? 'list';

// DELETE (prevent deleting self)
if ($action === 'delete' && isset($_GET['id'])) {
    if ($_GET['id'] == $_SESSION['user_id']) {
        setFlash('danger', 'Cannot delete your own account!');
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$_GET['id']]);
        setFlash('success', 'User deleted successfully!');
    }
    header("Location: users.php");
    exit();
}

// ADD/EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    
    if (isset($_POST['user_id']) && $_POST['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, username=?, role=?, phone=? WHERE user_id=?");
        $stmt->execute([$fullName, $email, $username, $role, $phone, $_POST['user_id']]);
        setFlash('success', 'User updated successfully!');
    } else {
        // New user - default password
        $defaultPass = password_hash('password123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (full_name, email, username, password_hash, role, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $username, $defaultPass, $role, $phone]);
        setFlash('success', 'User added! Default password: password123');
    }
    header("Location: users.php");
    exit();
}

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM users WHERE full_name LIKE ? OR username LIKE ? OR email LIKE ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(["%$search%", "%$search%", "%$search%"]);
$users = $stmt->fetchAll();

$editUser = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_GET['id']]);
    $editUser = $stmt->fetch();
}

include 'header.php';
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="card">
    <h2><?= $editUser ? 'Edit User' : 'New User' ?></h2>
    <form method="POST">
        <?php if ($editUser): ?>
            <input type="hidden" name="user_id" value="<?= $editUser['user_id'] ?>">
        <?php endif; ?>
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" value="<?= $editUser ? htmlspecialchars($editUser['full_name']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= $editUser ? htmlspecialchars($editUser['email']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?= $editUser ? htmlspecialchars($editUser['username']) : '' ?>" required>
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" required>
                <option value="staff" <?= ($editUser && $editUser['role'] == 'staff') ? 'selected' : '' ?>>Staff</option>
                <option value="admin" <?= ($editUser && $editUser['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= $editUser ? htmlspecialchars($editUser['phone']) : '' ?>">
        </div>
        <button type="submit" class="btn btn-success"><?= $editUser ? 'Update' : 'Add' ?> User</button>
        <a href="users.php" class="btn btn-primary">Cancel</a>
    </form>
</div>
<?php else: ?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>User Management (Admin Only)</h2>
        <a href="users.php?action=add" class="btn btn-success">+ New User</a>
    </div>
    
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search users..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="users.php" class="btn btn-warning">Clear</a>
    </form>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Phone</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['user_id'] ?></td>
                <td><?= htmlspecialchars($u['full_name']) ?></td>
                <td><?= htmlspecialchars($u['username']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><span class="badge badge-<?= $u['role'] == 'admin' ? 'danger' : 'primary' ?>"><?= ucfirst($u['role']) ?></span></td>
                <td><?= htmlspecialchars($u['phone']) ?></td>
                <td><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                <td class="actions">
                    <a href="users.php?action=edit&id=<?= $u['user_id'] ?>" class="btn btn-warning" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                    <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                    <a href="users.php?action=delete&id=<?= $u['user_id'] ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;" onclick="return confirm('Delete this user?')">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php endif; ?>

<?php include 'footer.php'; ?>