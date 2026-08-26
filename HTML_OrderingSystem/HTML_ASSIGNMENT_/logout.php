<?php
require '_base.php';
// Clear remember me cookie on logout
setcookie('remember_email', '', time() - 3600, '/');
logout();
?>



