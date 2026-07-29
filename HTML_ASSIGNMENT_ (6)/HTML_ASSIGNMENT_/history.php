<?php
require '_base.php';
require '_head.php';

$isLoggedIn = isset($_SESSION['user']);

if (!$isLoggedIn) {
    echo "<div class='alert alert-warning'>
            ⚠️ Please login first to view order history.
            <a href='login.php' class='btn btn-primary'>Login Now</a>
          </div>";
    exit;
}

$user = $_SESSION['user'];

if (isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];
    $pdo->prepare("DELETE FROM order_items WHERE order_id = ?")->execute([$delete_id]);
    $pdo->prepare("DELETE FROM orders WHERE id = ? AND user_id = ?")->execute([$delete_id, $user->id]);
    header("Location: history.php"); exit;
}

if (isset($_POST['delete_all'])) {
    $pdo->prepare("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE user_id = ?)")->execute([$user->id]);
    $pdo->prepare("DELETE FROM orders WHERE user_id = ?")->execute([$user->id]);
    header("Location: history.php"); exit;
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user->id]);
$orders = $stmt->fetchAll();
?>

<div class="history-wrap">
    <div class="history-header">
        <div>
            <h2>📜 Order History</h2>
            <p class="history-sub">All your past orders in one place</p>
        </div>
        <?php if ($orders): ?>
            <form method="POST" onsubmit="return confirm('Delete ALL orders? This cannot be undone!')">
                <button type="submit" name="delete_all" class="btn-delete-all">🗑️ Delete All Orders</button>
            </form>
        <?php endif; ?>
    </div>

    <?php if (!$orders): ?>
        <div class="empty-history">
            <div class="empty-icon">📦</div>
            <h3>No Orders Yet</h3>
            <p>You haven't placed any orders yet.</p>
            <a href="/menu.php" class="btn-shop">🍔 Start Ordering</a>
        </div>
    <?php else: ?>
        <div class="order-list">
        <?php foreach ($orders as $order):
            $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $stmt->execute([$order->id]);
            $items = $stmt->fetchAll();
        ?>
            <div class="order-card">
                <div class="order-card-header">
                    <div class="order-badge">Order #<?= $order->id ?></div>
                    <div class="order-date">🕐 <?= date('d M Y, h:i A', strtotime($order->creat_at)) ?></div>
                </div>
                <div class="order-user-info">
                    <span>👤 <?= htmlspecialchars($order->name) ?></span>
                    <span>✉️ <?= htmlspecialchars($order->email) ?></span>
                </div>
                <div class="order-divider"></div>
                <div class="order-items">
                    <?php foreach ($items as $item): ?>
                        <div class="order-item-row">
                            <span class="order-item-name"><?= htmlspecialchars($item->name) ?></span>
                            <span class="order-item-qty">×<?= $item->qty ?></span>
                            <span class="order-item-price">RM <?= number_format($item->price * $item->qty, 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="order-card-footer">
                    <div class="order-total">Total: <strong>RM <?= number_format($order->total, 2) ?></strong></div>
                    <form method="POST" onsubmit="return confirm('Delete Order #<?= $order->id ?>?')">
                        <input type="hidden" name="delete_id" value="<?= $order->id ?>">
                        <button type="submit" class="btn-delete-order">🗑️ Delete Order</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '_foot.php'; ?>