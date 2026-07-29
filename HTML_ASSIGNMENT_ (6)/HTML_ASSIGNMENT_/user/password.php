<?php
require '../_base.php';

if ($_user) redirect('/index.php');

$id = trim($_GET['id'] ?? '');
if (!$id) redirect('/login.php');

$pdo->query('DELETE FROM token WHERE expire < NOW()');

$stmt = $pdo->prepare('SELECT * FROM token WHERE id = ? AND type = "reset"');
$stmt->execute([$id]);
$token = $stmt->fetch();

if (!$token) {
    set_flash('Reset link is invalid or has expired.', 'danger');
    redirect('reset.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (!$password)          $errors['password'] = 'Password is required.';
    if (strlen($password) < 6) $errors['password'] = 'Min. 6 characters.';
    if ($password !== $confirm) $errors['confirm'] = 'Passwords do not match.';

    if (!$errors) {
        $pdo->prepare('UPDATE user SET password = ? WHERE id = ?')->execute([sha1($password), $token->user_id]);
        $pdo->prepare('DELETE FROM token WHERE id = ?')->execute([$id]);
        set_flash('Password reset successful! Please login.', 'success');
        redirect('/login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SitNEat — Reset Password</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body style="display:flex; flex-direction:column; min-height:100vh;">

<div class="top-nav">
    <a href="/index.php" class="brand">Sit<span>N</span>Eat</a>
</div>

<div class="page-wrap">
    <div class="auth-card">
        <div class="card-header">
            <div class="logo">Sit<span>N</span>Eat 🍔</div>
            <p>Set a new password</p>
        </div>
        <div class="card-body">
            <h3>Reset Password 🔑</h3>
            <p class="subtitle">Enter and confirm your new password below.</p>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" id="password" placeholder="Min. 6 characters">
                <?php if (!empty($errors['password'])): ?>
                    <span class="error-msg">⚠️ <?= $errors['password'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" id="confirm" placeholder="Repeat new password">
                <?php if (!empty($errors['confirm'])): ?>
                    <span class="error-msg">⚠️ <?= $errors['confirm'] ?></span>
                <?php endif; ?>
            </div>

            <button class="btn-submit" id="newPassBtn">Reset Password</button>
        </div>
        <div class="card-footer">
            <a href="/login.php">← Back to Login</a>
        </div>
    </div>
</div>

<div class="bottom-bar">© 2026 SitNEat — All Rights Reserved.</div>

<form id="resetForm" method="POST" action="/user/token.php?id=<?= htmlspecialchars($id) ?>">
    <input type="hidden" id="f_password" name="password">
    <input type="hidden" id="f_confirm"  name="confirm">
</form>

<script>
$('#newPassBtn').on('click', function () {
    $('#f_password').val($('#password').val());
    $('#f_confirm').val($('#confirm').val());
    $('#resetForm').submit();
});
</script>
</body>
</html>