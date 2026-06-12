<?php
session_start();
require "config/db.php";

$error   = "";
$success = "";

// ── How many shop pages you have — just change this number to add more ──
define('TOTAL_PAGES', 10);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST['name']);
    $brand = trim($_POST['brand']);
    $price = trim($_POST['price']);
    $page  = intval($_POST['page']);

    // Validate page range
    if ($page < 1 || $page > TOTAL_PAGES) $page = 1;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $allowed   = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize   = 5 * 1024 * 1024;
        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . "shoes images" . DIRECTORY_SEPARATOR;

        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $fileTmp  = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];
        $fileMime = mime_content_type($fileTmp);
        $origName = basename($_FILES['image']['name']);
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($fileMime, $allowed)) {
            $error = "❌ Only JPG, PNG, WEBP, or GIF images are allowed.";
        } elseif ($fileSize > $maxSize) {
            $error = "❌ Image must be under 5 MB.";
        } else {
            $fileName = uniqid('product_', true) . '.' . $ext;
            $dest     = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmp, $dest)) {
                $stmt = $conn->prepare(
                    "INSERT INTO products (name, brand, price, image, page) VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$name, $brand, $price, $fileName, $page]);
                $success = "✅ Product added successfully to Page $page!";
                $name = $brand = $price = "";
                $page = 1;
            } else {
                $error = "❌ Upload failed. Check that the 'shoes images' folder exists and is writable.";
            }
        }

    } else {
        $error = "❌ Please choose an image to upload.";
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
    <title>Add Product | Kisken Trends Duuka</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7f6;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 16px;
        }

        .form-container {
            background: #fff;
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

        .msg {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }
        .msg.error   { background: #fdecea; color: #c0392b; border: 1px solid #f5c6c2; }
        .msg.success { background: #e8f5f4; color: #088178; border: 1px solid #b2dfdb; }

        .form-group { margin-bottom: 18px; }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color .25s, box-shadow .25s;
            color: #333;
            background: #fff;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            outline: none;
            border-color: #088178;
            box-shadow: 0 0 0 3px rgba(8,129,120,.10);
        }

        /* ── PAGE GRID ─────────────────────────────────── */
        .page-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* 5 per row */
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

        .page-option .icon   { font-size: 20px; display: block; margin-bottom: 4px; }
        .page-option .label  { font-size: 12px; font-weight: 700; color: #555; }

        .page-option:has(input:checked) {
            border-color: #088178;
            background: #e8f5f4;
        }
        .page-option:has(input:checked) .label { color: #088178; }
        .page-option:hover { border-color: #aaa; background: #fafafa; }

        /* ── UPLOAD AREA ────────────────────────────────── */
        .upload-area {
            border: 2px dashed #088178;
            border-radius: 10px;
            padding: 22px;
            text-align: center;
            cursor: pointer;
            transition: background .2s;
            position: relative;
        }

        .upload-area:hover { background: #f0faf9; }

        .upload-area input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .upload-area i { font-size: 32px; display: block; margin-bottom: 8px; }
        .upload-area p { font-size: 13px; color: #888; margin: 0; }
        .upload-area p strong { color: #088178; }

        #preview { display: none; margin-top: 12px; text-align: center; }
        #preview img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 8px;
            border: 1.5px solid #e0e0e0;
        }
        #preview span { display: block; font-size: 12px; color: #aaa; margin-top: 5px; }

        button[type="submit"] {
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

        button[type="submit"]:hover  { opacity: .88; }
        button[type="submit"]:active { transform: scale(.98); }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: #088178;
            text-decoration: none;
        }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>👟 Add New Product</h2>

    <?php if ($error):   ?><div class="msg error"><?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="msg success"><?= $success ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label for="name">Product Name</label>
            <input type="text" id="name" name="name"
                   placeholder="e.g. Air Force 1"
                   value="<?= htmlspecialchars($name ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="brand">Brand</label>
            <input type="text" id="brand" name="brand"
                   placeholder="e.g. Nike, Adidas, Puma"
                   value="<?= htmlspecialchars($brand ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="price">Price (Shs)</label>
            <input type="number" id="price" name="price"
                   step="1" min="0"
                   placeholder="e.g. 120000"
                   value="<?= htmlspecialchars($price ?? '') ?>" required>
        </div>

        <!-- ── PAGE SELECTOR — auto-generates all pages ── -->
        <div class="form-group">
            <label>Shop Page <small style="color:#aaa;font-weight:400;">(select which page this product appears on)</small></label>
            <div class="page-grid">
                <?php for ($i = 1; $i <= TOTAL_PAGES; $i++):
                    $icon    = $pageIcons[$i] ?? '📄';
                    $checked = (!isset($page) || $page == 1) ? ($i == 1) : ($page == $i);
                ?>
                <label class="page-option">
                    <input type="radio" name="page" value="<?= $i ?>"
                           <?= $checked ? 'checked' : '' ?>>
                    <span class="icon"><?= $icon ?></span>
                    <span class="label">Page <?= $i ?></span>
                </label>
                <?php endfor; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Product Image</label>
            <div class="upload-area" id="uploadArea">
                <input type="file" id="imageInput" name="image"
                       accept="image/jpeg,image/png,image/webp,image/gif"
                       required>
                <i>📷</i>
                <p><strong>Click to upload</strong> or drag & drop</p>
                <p>JPG, PNG, WEBP · Max 5 MB</p>
            </div>
            <div id="preview">
                <img id="previewImg" src="" alt="Preview">
                <span id="previewName"></span>
            </div>
        </div>

        <button type="submit">Add Product</button>
    </form>

    <a href="admin_products.php" class="back-link">← Back to Products</a>
</div>

<script>
    document.getElementById('imageInput').addEventListener('change', function () {
        const file    = this.files[0];
        const preview = document.getElementById('preview');
        const img     = document.getElementById('previewImg');
        const name    = document.getElementById('previewName');

        if (file) {
            const reader  = new FileReader();
            reader.onload = e => {
                img.src          = e.target.result;
                name.textContent = file.name;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });
</script>

</body>
</html>