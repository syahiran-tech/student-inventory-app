<?php
require '_base.php';

if ($_user) redirect('index.php');

$errors = [];
$email  = $_COOKIE['remember_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = !empty($_POST['remember']);

    if (!$email)    $errors['email']    = 'Email is required.';
    if (!$password) $errors['password'] = 'Password is required.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $errors['email'] = 'Email not found.';
        } elseif (!$user->is_verified) {
            $errors['email'] = 'Please verify your email first.';
        } elseif ($user->blocked_until && new DateTime() < new DateTime($user->blocked_until)) {
            $unblock = date('h:i A', strtotime($user->blocked_until));
            $errors['email'] = "Account blocked until $unblock.";
        } elseif ($user->password !== sha1($password)) {
            $attempts = $user->login_attempts + 1;
            if ($attempts >= 3) {
                $blocked = date('Y-m-d H:i:s', strtotime('+1 minute'));
                $pdo->prepare('UPDATE user SET login_attempts=?, blocked_until=? WHERE id=?')
                    ->execute([$attempts, $blocked, $user->id]);
                $errors['password'] = 'Account blocked for 1 minute!';
            } else {
                $pdo->prepare('UPDATE user SET login_attempts=? WHERE id=?')
                    ->execute([$attempts, $user->id]);
                $left = 3 - $attempts;
                $errors['password'] = "Wrong password. $left attempt(s) left.";
            }
        } else {
            $pdo->prepare('UPDATE user SET login_attempts=0, blocked_until=NULL WHERE id=?')
                ->execute([$user->id]);
            if ($remember) {
                setcookie('remember_email', $email, time() + (7*24*60*60), '/');
            } else {
                setcookie('remember_email', '', time() - 3600, '/');
            }
            login($user);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SitNEat — Login</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body style="display:flex; flex-direction:column; min-height:100vh;">

<div class="top-nav">
    <a href="/index.php" class="brand">Sit<span>N</span>Eat</a>
    <div>
        <a href="/index.php">Home</a>
        <a href="/menu.php">Menu</a>
    </div>
</div>

<div class="page-wrap">
    <div class="auth-card">
        <div class="card-header">
            <div class="logo">Sit<span>N</span>Eat 🍔</div>
            <p>Your favourite restaurant, online.</p>
        </div>
        <div class="card-body">
            <h3>Welcome back!</h3>
            <p class="subtitle">Sign in to your account to continue.</p>

            <?php $flash = get_flash(); ?>
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label>Email</label>
                <input type="email" id="email" value="<?= htmlspecialchars($email) ?>"
                       placeholder="Enter your email"
                       class="<?= !empty($errors['email']) ? 'input-error' : '' ?>">
                <?php if (!empty($errors['email'])): ?>
                    <span class="error-msg">⚠️ <?= $errors['email'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" id="password" placeholder="Enter your password"
                       class="<?= !empty($errors['password']) ? 'input-error' : '' ?>">
                <?php if (!empty($errors['password'])): ?>
                    <span class="error-msg">⚠️ <?= $errors['password'] ?></span>
                <?php endif; ?>
            </div>

            <div class="row">
                <label>
                    <input type="checkbox" id="remember" <?= isset($_COOKIE['remember_email']) ? 'checked' : '' ?>>
                    Remember Me
                </label>
                <a href="/user/reset.php">Forgot Password?</a>
            </div>

            <button class="btn-login" id="loginBtn">Login</button>
        </div>
        <div class="card-footer">
            Don't have an account? <a href="/user/register.php">Register here</a>
        </div>
    </div>
</div>

<div class="bottom-bar">© 2026 SitNEat — All Rights Reserved.</div>

<form id="loginForm" method="POST" action="">
    <input type="hidden" id="f_email"    name="email">
    <input type="hidden" id="f_password" name="password">
    <input type="hidden" id="f_remember" name="remember">
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

    $('#loginBtn').on('click', function () {
        clearErrors();
        const email    = $('#email').val().trim();
        const password = $('#password').val().trim();
        const remember = $('#remember').is(':checked') ? '1' : '';
        let hasError = false;
        if (!email)    { showError('email', 'Email is required.');    hasError = true; }
        if (!password) { showError('password', 'Password is required.'); hasError = true; }
        if (hasError) return;
        $('#f_email').val(email); $('#f_password').val(password); $('#f_remember').val(remember);
        $('#loginForm').submit();
    });
    $(document).on('keypress', function (e) { if (e.key === 'Enter') $('#loginBtn').trigger('click'); });
});
</script>
</body>
</html>