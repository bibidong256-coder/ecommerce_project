<?php
session_start();
require "config/db.php";

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Product ID missing.");
}

$id = $_GET['id'];

// ── How many shop pages you have — just change this number to add more ──
define('TOTAL_PAGES', 10);

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $_POST['name'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $page  = intval($_POST['page']);

    // Validate page range
    if ($page < 1 || $page > TOTAL_PAGES) $page = 1;

    $image_path = $product['image'];

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . "shoes images" . DIRECTORY_SEPARATOR;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext       = strtolower(pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION));
        $file_name = uniqid('product_', true) . '.' . $ext;
        $dest      = $uploadDir . $file_name;

        if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $dest)) {
            $image_path = $file_name;
        }
    }

    try {
        $sql = "UPDATE products SET name=?, brand=?, price=?, image=?, page=? WHERE id=?";
        $update_stmt = $conn->prepare($sql);
        $update_stmt->execute([$name, $brand, $price, $image_path, $page, $id]);

        header("Location: admin_products.php?success=1");
        exit;
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}

// Page icons — pages beyond this list get 📄 automatically
$pageIcons = [1=>'🏪', 2=>'📦', 3=>'🛍️', 4=>'🎯', 5=>'⭐', 6=>'🔥', 7=>'💎', 8=>'🚀', 9=>'🎁', 10=>'👑'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7f6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 16px;
        }

        .form-card {
            background: white;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,.10);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #088178;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 28px;
        }

        .form-group { margin-bottom: 20px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 13px;
            color: #555;
        }

        .form-card input[type="text"],
        .form-card input[type="number"],
        .form-card input[type="file"] {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            color: #333;
            transition: border-color .25s, box-shadow .25s;
        }

        .form-card input:focus {
            outline: none;
            border-color: #088178;
            box-shadow: 0 0 0 3px rgba(8,129,120,.10);
        }

        .current-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1.5px solid #ddd;
            margin-bottom: 10px;
            display: block;
        }

        /* ── PAGE GRID ─────────────────────────────────── */
        .page-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }

        .page-option {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 10px 6px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            position: relative;
        }

        .page-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .page-option .icon  { font-size: 20px; display: block; margin-bottom: 4px; }
        .page-option .lbl   { font-size: 12px; font-weight: 700; color: #555; display: block; }

        .page-option:has(input:checked) {
            border-color: #088178;
            background: #e8f5f4;
        }

        .page-option:has(input:checked) .lbl { color: #088178; }
        .page-option:hover { border-color: #aaa; background: #fafafa; }

        /* ── CURRENTLY ON badge ─────────────────────────── */
        .current-page-note {
            font-size: 12px;
            color: #088178;
            background: #e8f5f4;
            border: 1px solid #b2dfdb;
            border-radius: 20px;
            padding: 4px 12px;
            display: inline-block;
            margin-bottom: 10px;
            font-weight: 600;
        }

        /* buttons */
        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #088178, #04534e);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity .2s, transform .1s;
            margin-top: 6px;
        }

        .btn-submit:hover  { opacity: .88; }
        .btn-submit:active { transform: scale(.98); }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: #088178;
            text-decoration: none;
        }

        .btn-cancel:hover { text-decoration: underline; }

        .note {
            font-size: 12px;
            color: #aaa;
            margin-top: 6px;
        }
    </style>
</head>
<body>

<div class="form-card">
    <h2>✏️ Edit Product #<?= $id ?></h2>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name"
                   value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Brand</label>
            <input type="text" name="brand"
                   value="<?= htmlspecialchars($product['brand']) ?>" required>
        </div>

        <div class="form-group">
            <label>Price (Shs)</label>
            <input type="number" name="price"
                   value="<?= $product['price'] ?>" required>
        </div>

        <!-- ── PAGE SELECTOR — auto-generates all pages ── -->
        <div class="form-group">
            <label>
                Shop Page
                <small style="color:#aaa; font-weight:400;">(move product to a different page)</small>
            </label>

            <?php
                $currentPage = isset($product['page']) && $product['page'] > 0 ? (int)$product['page'] : 1;
                $currentIcon = $pageIcons[$currentPage] ?? '📄';
            ?>
            <div class="current-page-note">
                <?= $currentIcon ?> Currently on Page <?= $currentPage ?>
            </div>

            <div class="page-grid">
                <?php for ($i = 1; $i <= TOTAL_PAGES; $i++):
                    $icon = $pageIcons[$i] ?? '📄';
                ?>
                <label class="page-option">
                    <input type="radio" name="page" value="<?= $i ?>"
                           <?= $currentPage == $i ? 'checked' : '' ?>>
                    <span class="icon"><?= $icon ?></span>
                    <span class="lbl">Page <?= $i ?></span>
                </label>
                <?php endfor; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Current Image</label>
            <?php if ($product['image']): ?>
                <img src="shoes images/<?= htmlspecialchars($product['image']) ?>"
                     class="current-img" alt="Current product image">
            <?php else: ?>
                <p class="note">No image uploaded</p>
            <?php endif; ?>

            <label style="margin-top:10px;">
                Upload New Image
                <span class="note">(leave blank to keep current)</span>
            </label>
            <input type="file" name="image_file" accept="image/*">
        </div>

        <button type="submit" class="btn-submit">Update Product</button>
        <a href="admin_products.php" class="btn-cancel">← Cancel & Go Back</a>
    </form>
</div>

</body>
</html>