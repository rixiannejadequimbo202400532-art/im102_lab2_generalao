<?php
require_once 'config.php';

$result = $conn->query("
    SELECT
        p.product_id,
        p.product_name,
        p.description,
        p.price,
        p.stock,
        c.category_name,
        s.supplier_name,
        p.created_at
    FROM products p
    JOIN categories c
        ON p.category_id = c.category_id
    JOIN suppliers s
        ON p.supplier_id = s.supplier_id
    ORDER BY p.product_id ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inventory System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Inventory Products</h1>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Category</th>
        <th>Supplier</th>
        <th>Created At</th>
    </tr>

    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['product_id']; ?></td>
        <td><?= $row['product_name']; ?></td>
        <td><?= $row['description']; ?></td>
        <td>₱<?= number_format($row['price'], 2); ?></td>
        <td><?= $row['stock']; ?></td>
        <td><?= $row['category_name']; ?></td>
        <td><?= $row['supplier_name']; ?></td>
        <td><?= $row['created_at']; ?></td>
    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>