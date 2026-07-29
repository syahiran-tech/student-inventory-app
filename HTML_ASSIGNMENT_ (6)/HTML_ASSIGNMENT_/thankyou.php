<?php
require '_base.php';
auth();
require '_head.php';

$order_id = $_GET['order_id'] ?? null;
$order = null;
$items = [];

if ($order_id) {
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
    $stmt->execute([$order_id, $_user->id]);
    $order = $stmt->fetch();

    if ($order) {
        $stmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll();
    }
}
?>

<div class="ty-wrap">
    <div class="ty-card">
        <div class="ty-icon">✅</div>
        <h1>Order Confirmed!</h1>
        <p class="ty-sub">Thank you, <strong><?= htmlspecialchars($_user->name) ?></strong>!
            Your order has been placed successfully.</p>

        <?php if ($order): ?>
            <div class="ty-order-id">Order #<?= $order->id ?></div>
            <div class="ty-items">
                <?php foreach ($items as $item): ?>
                    <div class="ty-item-row">
                        <span><?= htmlspecialchars($item->name) ?> <small>×<?= $item->qty ?></small></span>
                        <span>RM <?= number_format($item->price * $item->qty, 2) ?></span>
                    </div>
                <?php endforeach; ?>
                <div class="ty-total-row">
                    <strong>Total</strong>
                    <strong>RM <?= number_format($order->total, 2) ?></strong>
                </div>
            </div>
        <?php endif; ?>

        <div class="ty-actions">
            <a href="/menu.php" class="btn-menu">🍔 Order Again</a>
            <a href="/history.php" class="btn-history">📋 View History</a>
        </div>
    </div>
</div>

<?php include '_foot.php'; ?>