<?php $flash = get_flash(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SitNEat 🍔</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>

<nav class="navbar">
    <a href="/index.php" class="brand">
        Sit<span>N</span>Eat 🍔
    </a>
    <div>
        <?php if ($_user): ?>
            <a href="/index.php">Home</a>
            <a href="/menu.php">Menu</a>
            <a href="/cart.php">Cart</a>

            <?php if (in_array($_user->role, ['Owner', 'Admin'])): ?>
                <a href="/admin/order_detail.php">History</a>
                <a href="/product/index.php">Product</a>
                <a href="/category/index.php">Categories</a>
                <a href="/admin/detail.php">Admins</a>
                <a href="/user/members.php">Members</a>
            <?php else: ?>
                <a href="/history.php">History</a>
            <?php endif; ?>

            <?php if ($_user->role === 'Owner'): ?>
                <a href="/owner/index.php"
                   style="color:#f9ca24; font-weight:700;">
                    👑 Owner
                </a>
            <?php endif; ?>

            <a href="/user/profile.php">
                👤 <?= htmlspecialchars($_user->name) ?>
            </a>
            <a href="/logout.php"
               style="background:rgba(255,255,255,0.15);
                      border-radius:4px; padding:6px 12px;">
                Logout
            </a>
        <?php else: ?>
            <a href="/menu.php">Menu</a>
            <a href="/login.php">Login</a>
            <a href="/user/register.php">Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">

<?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?>">
        <?= htmlspecialchars($flash['msg']) ?>
    </div>
<?php endif; ?>