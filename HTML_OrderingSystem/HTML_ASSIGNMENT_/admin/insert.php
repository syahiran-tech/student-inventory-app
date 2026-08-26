<?php
require '../_base.php';
auth('Admin','Owner');


$errors = [];
$name = $email = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');


    // Validation
    if (!$name)     $errors['name']     = 'Name is required.';
    if (!$email)    $errors['email']    = 'Email is required.';
    if (!$password) $errors['password'] = 'Password is required.';
    if (strlen($password) < 6)
                    $errors['password'] = 'Min. 6 characters.';
    if ($password !== $confirm)
                    $errors['confirm']  = 'Passwords do not match.';


    if (!$errors) {
        // Check duplicate email
        $stmt = $pdo->prepare('SELECT id FROM user WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'This email is already registered.';
        }
    }


    if (!$errors) {
        $pdo->prepare('INSERT INTO user
                       (name, email, password, role, is_verified)
                       VALUES (?, ?, ?, "Admin", 1)')
            ->execute([$name, $email, sha1($password)]);


        set_flash('Admin "' . $name . '" added successfully!', 'success');
        redirect('detail.php');
    }
}


require '../_head.php';
?>


<div class="page-header">
    <h2>➕ Add Admin</h2>
    <a href="detail.php" class="btn btn-secondary">← Back</a>
</div>


<div class="card" style="max-width:560px;">


    <div class="form-group">
        <label>Full Name <span style="color:#c0392b;">*</span></label>
        <input type="text" id="name"
               value="<?= htmlspecialchars($name) ?>"
               placeholder="Enter admin's full name">
        <?php if (!empty($errors['name'])): ?>
            <span class="error">⚠️ <?= $errors['name'] ?></span>
        <?php endif; ?>
    </div>


    <div class="form-group">
        <label>Email Address <span style="color:#c0392b;">*</span></label>
        <input type="email" id="email"
               value="<?= htmlspecialchars($email) ?>"
               placeholder="Enter admin's email">
        <?php if (!empty($errors['email'])): ?>
            <span class="error">⚠️ <?= $errors['email'] ?></span>
        <?php endif; ?>
    </div>


    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
        <div class="form-group">
            <label>Password <span style="color:#c0392b;">*</span></label>
            <input type="password" id="password"
                   placeholder="Min. 6 characters">
            <?php if (!empty($errors['password'])): ?>
                <span class="error">⚠️ <?= $errors['password'] ?></span>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label>Confirm Password <span style="color:#c0392b;">*</span></label>
            <input type="password" id="confirm"
                   placeholder="Repeat password">
            <?php if (!empty($errors['confirm'])): ?>
                <span class="error">⚠️ <?= $errors['confirm'] ?></span>
            <?php endif; ?>
        </div>
    </div>


    <div style="background:#fff8f8; border-left:4px solid #c0392b;
                padding:12px 16px; border-radius:6px;
                margin-bottom:20px; font-size:13px; color:#666;">
        ⚠️ New admin will have <strong>full access</strong>
        to all admin features including category and admin management.
    </div>


    <div style="display:flex; gap:10px;">
            <button class="btn btn-primary" id="adminBtn">
                👑 Add Admin
            </button>  
        <a href="detail.php" class="btn btn-secondary">Cancel</a>
    </div>
</div>


<form id="adminForm" method="POST" action="">
    <input type="hidden" id="f_name"     name="name">
    <input type="hidden" id="f_email"    name="email">
    <input type="hidden" id="f_password" name="password">
    <input type="hidden" id="f_confirm"  name="confirm">
</form>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="/js/app.js"></script>


</div></body></html>



