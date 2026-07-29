<?php
require '_base.php';
require '_head.php';


// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


$select_item = null;


if (isset($_GET['id'])) {


    $stmt = $pdo->prepare("SELECT * FROM menu_item WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $select_item = $stmt->fetch();
}


if (isset($_POST['add'])) {


    $id    = $_POST['id'];
    $name  = $_POST['name'];
    $price = $_POST['price'];
    $qty   = $_POST['qty'] ?? 1;  


    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] += $qty;
    } else {
        $_SESSION['cart'][$id] = [
            'name' => $name,
            'price' => $price,
            'qty' => $qty
        ];
    }


    header("Location: menu.php");
    exit;
}




// ✅ REMOVE ITEM
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
}


$cart = $_SESSION['cart'];
?>


<div class="page-header">
    <h2>🛒 Your Cart</h2>
</div>


<?php if ($cart): ?>
<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>


        <?php $grand = 0; ?>


        <?php foreach ($cart as $id => $item):
            $total = $item['price'] * $item['qty'];
            $grand += $total;
        ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= $item['qty'] ?></td>
            <td>RM <?= number_format($item['price'], 2) ?></td>
            <td>RM <?= number_format($total, 2) ?></td>
            <td>
                <a href="cart.php?remove=<?= $id ?>"
                   class="btn btn-danger"
                   style="padding:4px 10px; font-size:12px;">
                    ❌
                </a>
            </td>
        </tr>
        <?php endforeach; ?>


        </tbody>
    </table>


    <div style="text-align:right; margin-top:15px;">
        <h3>Total: RM <?= number_format($grand, 2) ?></h3>


        <!-- ✅ CHECKOUT BUTTON ONLY HERE -->
        <a href="checkout.php" class="btn btn-primary">
            Checkout
        </a>
    </div>
</div>


<?php else: ?>
<div class="card" style="text-align:center; padding:40px;">
    <h3>Your cart is empty 🛒</h3>
    <a href="menu.php" class="btn btn-primary" style="margin-top:10px;">
        Browse Menu
    </a>
</div>
<?php endif; ?>






</div></body></html>

