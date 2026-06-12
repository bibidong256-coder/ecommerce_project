<?php
/**
 * api/update_cart.php
 *
 * Called by AJAX from cart.php and product_details.html.
 * Always returns JSON — never redirects.
 *
 * Actions:
 *   add       – add item to cart (or increment qty if already exists)
 *   increase  – add 1 to qty
 *   decrease  – subtract 1 from qty (removes item if qty reaches 0)
 *   remove    – delete item from cart entirely
 *   size      – save chosen size for item
 *
 * GET params:
 *   id      (int)    – product ID
 *   action  (string) – one of the actions above
 *   size    (string) – required only for action=size
 *
 * JSON responses:
 *   { "status": "success", "qty": 1 }                    ← add
 *   { "success": true,  "qty": 3,   "removed": false }   ← increase / decrease
 *   { "success": true,  "removed": true }                 ← item qty hit 0 or remove
 *   { "success": true,  "size": "42" }                    ← size saved
 *   { "success": false, "error": "..." }                  ← bad request
 */

session_start();
header('Content-Type: application/json');

// ── Read & validate inputs ────────────────────────────────────────────────────
$id     = filter_input(INPUT_GET, 'id',     FILTER_VALIDATE_INT);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
$size   = filter_input(INPUT_GET, 'size',   FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

// ✅ 'add' is now included here
$validActions = ['add', 'increase', 'decrease', 'remove', 'size'];

if (!$id || !in_array($action, $validActions, true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

// ── Ensure cart exists ────────────────────────────────────────────────────────
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = &$_SESSION['cart'];

// ── Normalise entry: plain int → array ───────────────────────────────────────
if (isset($cart[$id]) && !is_array($cart[$id])) {
    $cart[$id] = ['qty' => (int)$cart[$id], 'size' => ''];
}

// ── Handle each action ────────────────────────────────────────────────────────
switch ($action) {

    // ── ADD ───────────────────────────────────────────────────────────────────
    case 'add':
        if (!isset($cart[$id])) {
            $cart[$id] = ['qty' => 1, 'size' => ''];
        } else {
            $cart[$id]['qty']++;
        }
        echo json_encode([
            'status' => 'success',
            'qty'    => $cart[$id]['qty'],
        ]);
        break;

    // ── INCREASE ──────────────────────────────────────────────────────────────
    case 'increase':
        if (!isset($cart[$id])) {
            $cart[$id] = ['qty' => 1, 'size' => ''];
        } else {
            $cart[$id]['qty']++;
        }
        echo json_encode([
            'success' => true,
            'qty'     => $cart[$id]['qty'],
            'removed' => false,
        ]);
        break;

    // ── DECREASE ──────────────────────────────────────────────────────────────
    case 'decrease':
        if (!isset($cart[$id])) {
            echo json_encode(['success' => false, 'error' => 'Item not in cart']);
            break;
        }

        $cart[$id]['qty']--;

        if ($cart[$id]['qty'] <= 0) {
            unset($cart[$id]);
            echo json_encode(['success' => true, 'removed' => true]);
        } else {
            echo json_encode([
                'success' => true,
                'qty'     => $cart[$id]['qty'],
                'removed' => false,
            ]);
        }
        break;

    // ── REMOVE ────────────────────────────────────────────────────────────────
    case 'remove':
        unset($cart[$id]);
        echo json_encode(['success' => true, 'removed' => true]);
        break;

    // ── SIZE ──────────────────────────────────────────────────────────────────
    case 'size':
        if (!isset($cart[$id])) {
            $cart[$id] = ['qty' => 1, 'size' => $size];
        } else {
            $cart[$id]['size'] = $size;
        }
        echo json_encode(['success' => true, 'size' => $size]);
        break;
}