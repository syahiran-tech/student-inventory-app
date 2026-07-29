<?php
require '../_base.php';


if ($_user) redirect('/index.php');


$errors = [];
$email  = '';
$sent   = false;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!$email) { $errors['email'] = 'Email is required.'; }


    if (!$errors) {
        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();


        if (!$user) {
            $errors['email'] = 'No account found with this email.';
        } else {
            $pdo->prepare('DELETE FROM token WHERE user_id = ? AND type = "reset"')->execute([$user->id]);


            $token_id = sha1(uniqid() . rand());
            $expire   = date('Y-m-d H:i:s', strtotime('+5 minutes'));


            $pdo->prepare('INSERT INTO token (id, user_id, type, expire) VALUES (?, ?, "reset", ?)')
                ->execute([$token_id, $user->id, $expire]);


            $check = $pdo->prepare('SELECT id FROM token WHERE id = ?');
            $check->execute([$token_id]);


            if ($check->fetch()) {
                $link = 'http://localhost:8000/user/token.php?id=' . $token_id;
                $body = '<div style="font-family:Segoe UI,sans-serif;max-width:500px;margin:auto;">'
                      . '<div style="background:#c0392b;padding:24px;text-align:center;border-radius:8px 8px 0 0;">'
                      . '<h1 style="color:white;margin:0;">SitNEat 🍔</h1></div>'
                      . '<div style="background:white;padding:30px;border:1px solid #eee;">'
                      . '<h2>Password Reset Request</h2>'
                      . '<p style="color:#555;line-height:1.6;">Hi ' . htmlspecialchars($user->name) . ',<br>'
                      . 'Click the button below to reset your password.<br><strong>This link expires in 5 minutes.</strong></p>'
                      . '<div style="text-align:center;margin:30px 0;">'
                      . '<a href="' . $link . '" style="background:#c0392b;color:white;padding:14px 32px;border-radius:6px;text-decoration:none;font-weight:700;font-size:15px;">🔑 Reset My Password</a>'
                      . '</div><p style="color:#999;font-size:12px;">If you did not request this, ignore this email.</p>'
                      . '</div></div>';
                try {
                    $mail = get_mail();
                    $mail->addAddress($email, $user->name);
                    $mail->Subject = 'SitNEat — Reset Your Password';
                    $mail->isHTML(true);
                    $mail->Body = $body;
                    $mail->send();
                } catch (Exception $e) { }
            }
            $sent = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SitNEat — Forgot Password</title>
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body style="display:flex; flex-direction:column; min-height:100vh;">


<div class="top-nav">
    <a href="/index.php" class="brand">Sit<span>N</span>Eat</a>
    <div>
        <a href="/login.php">Login</a>
        <a href="/user/register.php">Register</a>
    </div>
</div>


<div class="page-wrap">
    <div class="auth-card">
        <div class="card-header">
            <div class="logo">Sit<span>N</span>Eat 🍔</div>
            <p>Password Recovery</p>
        </div>
        <div class="card-body">
            <?php if ($sent): ?>
                <div class="success-box">
                    <div class="icon">📧</div>
                    <h3>Check Your Email!</h3>
                    <p>A password reset link has been sent to<br>
                       <strong><?= htmlspecialchars($email) ?></strong><br><br>
                       The link expires in <strong>5 minutes</strong>.<br>
                       Check your spam folder if not received.</p>
                </div>
            <?php else: ?>
                <h3>Forgot Password?</h3>
                <p class="subtitle">Enter your email and we'll send you a link to reset your password.</p>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="email" value="<?= htmlspecialchars($email) ?>"
                           placeholder="Enter your registered email">
                    <?php if (!empty($errors['email'])): ?>
                        <span class="error-msg">⚠️ <?= $errors['email'] ?></span>
                    <?php endif; ?>
                </div>
                <button class="btn-submit" id="resetBtn">Send Reset Link</button>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            Remember your password? <a href="/login.php">Back to Login</a>
        </div>
    </div>
</div>


<div class="bottom-bar">© 2026 SitNEat — All Rights Reserved.</div>


<form id="resetForm" method="POST" action="">
    <input type="hidden" id="f_email" name="email">
</form>
<script>
$('#resetBtn').on('click', function () {
    $('#f_email').val($('#email').val());
    $('#resetForm').submit();
});
</script>
</body>
</html>