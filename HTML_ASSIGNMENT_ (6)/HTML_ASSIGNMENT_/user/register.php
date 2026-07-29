<?php
require '../_base.php';

if ($_user) redirect('/index.php');

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (!$name)                 $errors['name']     = 'Full name is required.';
    if (!$email)                $errors['email']    = 'Email is required.';
    if (!$password)             $errors['password'] = 'Password is required.';
    if (strlen($password) < 6)  $errors['password'] = 'Min. 6 characters.';
    if ($password !== $confirm) $errors['confirm']  = 'Passwords do not match.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM user WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) { $errors['email'] = 'This email is already registered.'; }
    }

    if (!$errors) {
        $pdo->prepare('INSERT INTO user (name, email, password, role, is_verified) VALUES (?, ?, ?, "Member", 0)')
            ->execute([$name, $email, sha1($password)]);

        $user_id  = $pdo->lastInsertId();
        $token_id = sha1(uniqid() . rand());
        $expire   = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $pdo->prepare('INSERT INTO token (id, user_id, type, expire) VALUES (?, ?, "verify", ?)')
            ->execute([$token_id, $user_id, $expire]);

        $link = 'http://localhost:8000/user/verify.php?id=' . $token_id;
        $body = '<div style="font-family:Segoe UI,sans-serif;max-width:500px;margin:auto;">'
              . '<div style="background:#c0392b;padding:24px;text-align:center;border-radius:8px 8px 0 0;">'
              . '<h1 style="color:white;margin:0;">SitNEat 🍔</h1></div>'
              . '<div style="background:white;padding:30px;border:1px solid #eee;">'
              . '<h2>Hi ' . htmlspecialchars($name) . '! 👋</h2>'
              . '<p style="color:#555;line-height:1.6;">Thanks for registering at SitNEat!<br>Please click the button below to verify your email.</p>'
              . '<div style="text-align:center;margin:30px 0;">'
              . '<a href="' . $link . '" style="background:#c0392b;color:white;padding:14px 32px;border-radius:6px;text-decoration:none;font-weight:700;font-size:15px;">✅ Verify My Email</a>'
              . '</div><p style="color:#999;font-size:12px;">Link expires in 24 hours.</p></div></div>';

        try {
            $mail = get_mail();
            $mail->addAddress($email, $name);
            $mail->Subject = 'SitNEat — Verify Your Email';
            $mail->isHTML(true);
            $mail->Body = $body;
            $mail->send();
            set_flash('Registered! Please check your email to verify your account.', 'success');
        } catch (Exception $e) {
            set_flash('Account created but email could not be sent. Please contact support.', 'warning');
        }
        redirect('/login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SitNEat — Register</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body style="display:flex; flex-direction:column; min-height:100vh;">

<div class="top-nav">
    <a href="/index.php" class="brand">Sit<span>N</span>Eat</a>
    <div>
        <a href="/index.php">Home</a>
        <a href="/menu.php">Menu</a>
        <a href="/login.php">Login</a>
    </div>
</div>

<div class="page-wrap">
    <div class="auth-card auth-card-wide">
        <div class="card-header">
            <div class="logo">Sit<span>N</span>Eat 🍔</div>
            <p>Create your account today</p>
        </div>
        <div class="card-body">
            <h3>Create Account</h3>
            <p class="subtitle">Fill in the details below to get started.</p>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" id="name" value="<?= htmlspecialchars($name) ?>"
                       placeholder="Enter your full name"
                       class="<?= !empty($errors['name']) ? 'input-error' : '' ?>">
                <?php if (!empty($errors['name'])): ?><span class="error-msg">⚠️ <?= $errors['name'] ?></span><?php endif; ?>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="email" value="<?= htmlspecialchars($email) ?>"
                       placeholder="Enter your email"
                       class="<?= !empty($errors['email']) ? 'input-error' : '' ?>">
                <?php if (!empty($errors['email'])): ?><span class="error-msg">⚠️ <?= $errors['email'] ?></span><?php endif; ?>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" id="password" placeholder="Min. 6 characters"
                           class="<?= !empty($errors['password']) ? 'input-error' : '' ?>">
                    <?php if (!empty($errors['password'])): ?><span class="error-msg">⚠️ <?= $errors['password'] ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" id="confirm" placeholder="Repeat password"
                           class="<?= !empty($errors['confirm']) ? 'input-error' : '' ?>">
                    <?php if (!empty($errors['confirm'])): ?><span class="error-msg">⚠️ <?= $errors['confirm'] ?></span><?php endif; ?>
                </div>
            </div>

            <button class="btn-register" id="registerBtn">Create Account</button>
        </div>
        <div class="card-footer">
            Already have an account? <a href="/login.php">Login here</a>
        </div>
    </div>
</div>

<div class="bottom-bar">© 2026 SitNEat — All Rights Reserved.</div>

<form id="regForm" method="POST" action="">
    <input type="hidden" id="f_name"     name="name">
    <input type="hidden" id="f_email"    name="email">
    <input type="hidden" id="f_password" name="password">
    <input type="hidden" id="f_confirm"  name="confirm">
</form>

<script>
$(document).ready(function () {
    function showError(inputId, message) {
        $('#' + inputId).addClass('input-error');
        if ($('#' + inputId).next('.error-msg.js-error').length === 0) {
            $('#' + inputId).after('<span class="error-msg js-error">⚠️ ' + message + '</span>');
        }
    }
    function clearErrors() { $('.js-error').remove(); $('input').removeClass('input-error'); }

    $('#registerBtn').on('click', function () {
        clearErrors();
        const name = $('#name').val().trim(), email = $('#email').val().trim();
        const password = $('#password').val().trim(), confirm = $('#confirm').val().trim();
        let hasError = false;
        if (!name)     { showError('name', 'Full name is required.'); hasError = true; }
        if (!email)    { showError('email', 'Email is required.'); hasError = true; }
        if (!password) { showError('password', 'Password is required.'); hasError = true; }
        else if (password.length < 6) { showError('password', 'Min. 6 characters.'); hasError = true; }
        if (!confirm)  { showError('confirm', 'Please confirm your password.'); hasError = true; }
        else if (password !== confirm) { showError('confirm', 'Passwords do not match.'); hasError = true; }
        if (hasError) return;
        $('#f_name').val(name); $('#f_email').val(email);
        $('#f_password').val(password); $('#f_confirm').val(confirm);
        $('#regForm').submit();
    });
    $(document).on('keypress', function (e) { if (e.key === 'Enter') $('#registerBtn').trigger('click'); });
});
</script>
</body>
</html>