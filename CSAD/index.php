<?php
// ============================
// REQUIRE DATABASE CONNECTION
// ============================
require_once "connection.php";

// ============================
// HANDLE FORM SUBMISSIONS
// ============================
$message = "";
$message_type = "";

// --- Add Category ---
if (isset($_POST['add_category'])) {
    $name = trim($_POST['category_name']);
    if ($name !== "") {
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $message = "✅ Category added successfully!";
            $message_type = "success";
        } else {
            $message = "❌ Error: " . $stmt->error;
            $message_type = "error";
        }
        $stmt->close();
    } else {
        $message = "⚠️ Category name cannot be empty.";
        $message_type = "error";
    }
}

// --- Add Supplier ---
if (isset($_POST['add_supplier'])) {
    $sname   = trim($_POST['supplier_name']);
    $contact = trim($_POST['contact_person']);
    $phone   = trim($_POST['phone_number']);
    if ($sname !== "") {
        $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, contact_person, phone_number) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $sname, $contact, $phone);
        if ($stmt->execute()) {
            $message = "✅ Supplier added successfully!";
            $message_type = "success";
        } else {
            $message = "❌ Error: " . $stmt->error;
            $message_type = "error";
        }
        $stmt->close();
    } else {
        $message = "⚠️ Supplier name cannot be empty.";
        $message_type = "error";
    }
}

// --- Add Product ---
if (isset($_POST['add_product'])) {
    $sku    = trim($_POST['sku']);
    $pname  = trim($_POST['product_name']);
    $cat_id = intval($_POST['category_id']);
    $sup_id = intval($_POST['supplier_id']);
    $price  = floatval($_POST['unit_price']);
    $stock  = intval($_POST['stock_quantity']);

    if ($sku !== "" && $pname !== "") {
        $stmt = $conn->prepare("INSERT INTO products (sku, product_name, category_id, supplier_id, unit_price, stock_quantity) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiidi", $sku, $pname, $cat_id, $sup_id, $price, $stock);
        if ($stmt->execute()) {
            $message = "✅ Product added successfully!";
            $message_type = "success";
        } else {
            $message = "❌ Error: " . $stmt->error;
            $message_type = "error";
        }
        $stmt->close();
    } else {
        $message = "⚠️ SKU and Product name are required.";
        $message_type = "error";
    }
}

// ============================
// FETCH DATA FOR DISPLAY
// ============================
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name");
$suppliers  = $conn->query("SELECT * FROM suppliers ORDER BY supplier_name");
$products   = $conn->query("
    SELECT p.product_id, p.sku, p.product_name, 
           c.category_name, s.supplier_name,
           p.unit_price, p.stock_quantity
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
    ORDER BY p.product_id DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inventory System</title>
<style>
    * { box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
    body { background: #eef2f7; margin: 0; padding: 20px; color: #333; }
    h1 { color: #2c3e50; text-align: center; margin-bottom: 30px; }
    h2 { color: #34495e; border-bottom: 2px solid #3498db; padding-bottom: 6px; margin-top: 0; }
    .container { max-width: 1100px; margin: 0 auto; }
    .card { background: #fff; padding: 20px; margin-bottom: 25px; border-radius: 10px; box-shadow: 0 3px 8px rgba(0,0,0,0.08); }
    .message { padding: 12px 18px; border-radius: 6px; margin-bottom: 20px; font-weight: 500; }
    .success { background: #d4edda; color: #155724; border-left: 5px solid #28a745; }
    .error   { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
    form { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px; }
    input, select, button { padding: 9px 12px; border-radius: 6px; border: 1px solid #ccc; font-size: 14px; }
    input, select { flex: 1 1 180px; }
    input:focus, select:focus { outline: none; border-color: #3498db; box-shadow: 0 0 4px rgba(52,152,219,0.4); }
    button { background: #3498db; color: #fff; border: none; cursor: pointer; font-weight: 600; transition: 0.2s; }
    button:hover { background: #2980b9; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
    th, td { padding: 10px 12px; border: 1px solid #e1e4e8; text-align: left; }
    th { background: #3498db; color: #fff; font-weight: 600; }
    tr:nth-child(even) { background: #f8fafc; }
    tr:hover { background: #eaf4fb; }
    .empty { text-align: center; color: #888; font-style: italic; padding: 15px; }
</style>
</head>
<body>
<div class="container">
    <h1>📦 Inventory Management System</h1>

    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- ================= CATEGORIES ================= -->
    <div class="card">
        <h2>🗂️ Categories</h2>
        <form method="POST">
            <input type="text" name="category_name" placeholder="Enter category name" required>
            <button type="submit" name="add_category">+ Add Category</button>
        </form>
        <table>
            <thead>
                <tr><th style="width:80px;">ID</th><th>Category Name</th></tr>
            </thead>
            <tbody>
                <?php if ($categories && $categories->num_rows > 0): ?>
                    <?php while ($row = $categories->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['category_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="2" class="empty">No categories yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ================= SUPPLIERS ================= -->
    <div class="card">
        <h2>🚚 Suppliers</h2>
        <form method="POST">
            <input type="text" name="supplier_name" placeholder="Supplier name" required>
            <input type="text" name="contact_person" placeholder="Contact person">
            <input type="text" name="phone_number" placeholder="Phone number">
            <button type="submit" name="add_supplier">+ Add Supplier</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th style="width:80px;">ID</th>
                    <th>Supplier Name</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($suppliers && $suppliers->num_rows > 0): ?>
                    <?php while ($row = $suppliers->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['supplier_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_person'] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($row['phone_number'] ?? '—'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="empty">No suppliers yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ================= PRODUCTS ================= -->
    <div class="card">
        <h2>🛒 Products</h2>
        <form method="POST">
            <input type="text" name="sku" placeholder="SKU" required>
            <input type="text" name="product_name" placeholder="Product name" required>
            <select name="category_id">
                <option value="0">-- Select Category --</option>
                <?php
                $categories->data_seek(0);
                while ($c = $categories->fetch_assoc()):
                ?>
                    <option value="<?php echo $c['category_id']; ?>">
                        <?php echo htmlspecialchars($c['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <select name="supplier_id">
                <option value="0">-- Select Supplier --</option>
                <?php
                $suppliers->data_seek(0);
                while ($s = $suppliers->fetch_assoc()):
                ?>
                    <option value="<?php echo $s['supplier_id']; ?>">
                        <?php echo htmlspecialchars($s['supplier_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <input type="number" step="0.01" name="unit_price" placeholder="Unit price" required>
            <input type="number" name="stock_quantity" placeholder="Stock qty" required>
            <button type="submit" name="add_product">+ Add Product</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>SKU</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th>Price</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($products && $products->num_rows > 0): ?>
                    <?php while ($row = $products->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['product_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['sku']); ?></td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['category_name'] ?? '—'); ?></td>
                            <td><?php echo htmlspecialchars($row['supplier_name'] ?? '—'); ?></td>
                            <td>₱<?php echo number_format($row['unit_price'], 2); ?></td>
                            <td><?php echo $row['stock_quantity']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="empty">No products yet. Add one above.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>