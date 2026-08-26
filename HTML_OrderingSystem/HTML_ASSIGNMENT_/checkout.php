<?php
require '_base.php';
require '_head.php';


$cart = $_SESSION['cart'] ?? [];
$isLoggedIn = isset($_SESSION['user']);


if (!$cart) {
    echo "<div style='text-align:center; padding:60px;'>
            <div style='font-size:48px;'>🛒</div>
            <h3 style='color:#999; margin-top:12px;'>Your cart is empty</h3>
            <a href='/menu.php' style='display:inline-block; margin-top:16px;
               padding:10px 24px; background:#c0392b; color:#fff;
               border-radius:8px; text-decoration:none;'>Browse Menu</a>
          </div>";
    include '_foot.php';
    exit;
}


$total = 0;
foreach ($cart as $item) { $total += $item['price'] * $item['qty']; }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$isLoggedIn) { header('Location: /login.php'); exit; }
    $user = $_SESSION['user'];
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, email, total) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user->id, $user->name, $user->email, $total]);
    $order_id = $pdo->lastInsertId();
    foreach ($cart as $item) {
    $pdo->prepare("INSERT INTO order_items (order_id, menu_id, name, price, qty) VALUES (?, ?, ?, ?, ?)")
    ->execute([$order_id, $item['menu_id'], $item['name'], $item['price'], $item['qty']]);
    }
    unset($_SESSION['cart']);
    header('Location: /thankyou.php?order_id=' . $order_id);
    exit;
}
?>


<?php if (!$isLoggedIn): ?>
    <div class="alert alert-warning" style="max-width:700px; margin:20px auto;">
        ⚠️ Please <a href="/login.php"><strong>login</strong></a> before checking out.
    </div>
<?php endif; ?>


<div class="checkout-wrap">
    <h2 class="checkout-title">💳 Checkout</h2>
    <div class="checkout-layout">
        <div class="checkout-items">
            <div class="co-card">
                <h3>🛒 Your Items</h3>
                <?php foreach ($cart as $item):
                    $subtotal = $item['price'] * $item['qty'];
                ?>
                    <div class="co-item-row">
                        <div class="co-item-info">
                            <span class="co-item-name"><?= htmlspecialchars($item['name']) ?></span>
                            <small class="co-item-sub">RM <?= number_format($item['price'], 2) ?> × <?= $item['qty'] ?></small>
                        </div>
                        <span class="co-item-total">RM <?= number_format($subtotal, 2) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="checkout-summary">
            <div class="co-card">
                <h3>Order Summary</h3>
                <div class="summary-line">
                    <span>Subtotal</span>
                    <span>RM <?= number_format($total, 2) ?></span>
                </div>
                <hr style="border:none; border-top:1px solid #f0f0f0; margin:12px 0;">
                <div class="summary-line total-line">
                    <strong>Total</strong>
                    <strong class="total-amount">RM <?= number_format($total, 2) ?></strong>
                </div>
                <?php if ($isLoggedIn): ?>
                    <div class="user-info-box">
                        <div>👤 <?= htmlspecialchars($_user->name) ?></div>
                        <div>✉️ <?= htmlspecialchars($_user->email) ?></div>
                    </div>
                <?php endif; ?>
                <form method="POST">
                    <button class="btn-confirm" type="submit"
                            <?= !$isLoggedIn ? 'disabled title="Login to checkout"' : '' ?>>
                        ✅ Confirm Order
                    </button>
                </form>
                <a href="/cart.php" class="btn-back-cart">← Back to Cart</a>
            </div>
        </div>
    </div>
</div>


<?php include '_foot.php'; ?>



