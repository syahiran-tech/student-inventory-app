<?php
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();




// ============================================================
// DATABASE CONNECTION
// ============================================================
$pdo = new PDO(
    'mysql:dbname=wis_assignment;host=localhost;port=3306','root','',
    [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
);




// ============================================================
// GLOBAL USER (restore from session)
// ============================================================
$_user = $_SESSION['user'] ?? null;




// ============================================================
// AUTH - protect pages
// ============================================================
function auth(string ...$roles): void {
    global $_user;
    if ($_user) {
        // Owner can access everything always
        if ($_user->role === 'Owner') return;
        // If no specific roles required, just login is enough
        if (!$roles) return;
        // Check if user role matches
        if (in_array($_user->role, $roles)) return;
    }
    redirect('/login.php');
}




// ============================================================
// LOGIN / LOGOUT
// ============================================================
function login(object $user, string $url = 'index.php'): void {
    $_SESSION['user'] = $user;
    redirect($url);
}




function logout(string $url = 'login.php'): void {
    session_destroy();
    redirect($url);
}




// ============================================================
// REDIRECT
// ============================================================
function redirect(string $url): void {
    header("Location: $url");
    exit();
}




// ============================================================
// FLASH MESSAGES (show one-time success/error messages)
// ============================================================
function set_flash(string $msg, string $type = 'success'): void {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}




function get_flash(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}




// ============================================================
// GET BASE URL (for email links)
// ============================================================
function base(string $path = ''): string {
    $protocol = isset($_SERVER['HTTPS']) &&
                $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . '://' . $host . '/' . $path;
}
// ============================================================
// PHPMAILER — Send Email
// ============================================================
function get_mail(): object {
    require_once __DIR__ . '/lib/PHPMailer/Exception.php';
    require_once __DIR__ . '/lib/PHPMailer/PHPMailer.php';
    require_once __DIR__ . '/lib/PHPMailer/SMTP.php';




    $mail = new PHPMailer\PHPMailer\PHPMailer(true);




    // SMTP Configuration (Gmail)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'AACS3173@gmail.com';  // Demo Gmail
    $mail->Password   = 'xxna ftdu plga hzxl'; // App password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';




    // Sender
    $mail->setFrom('AACS3173@gmail.com', 'SitNEat Restaurant');




    return $mail;
}




// ============================================================
// HTML HELPERS
// ============================================================
function html_text(string $name, string $value = '',
                   string $placeholder = ''): string {
    $v = htmlspecialchars($value);
    $p = htmlspecialchars($placeholder);
    return "<input type='text' id='$name' name='$name'
                   value='$v' placeholder='$p'>";
}




function html_email(string $name, string $value = '',
                    string $placeholder = ''): string {
    $v = htmlspecialchars($value);
    $p = htmlspecialchars($placeholder);
    return "<input type='email' id='$name' name='$name'
                   value='$v' placeholder='$p'>";
}




function html_password(string $name,
                       string $placeholder = 'Enter password'): string {
    $p = htmlspecialchars($placeholder);
    return "<input type='password' id='$name'
                   name='$name' placeholder='$p'>";
}




function html_textarea(string $name, string $value = '',
                       int $rows = 3,
                       string $placeholder = ''): string {
    $v = htmlspecialchars($value);
    $p = htmlspecialchars($placeholder);
    return "<textarea id='$name' name='$name' rows='$rows'
                      placeholder='$p'
                      style='width:100%;padding:10px 12px;
                             border:1.5px solid #ddd;border-radius:6px;
                             font-size:14px;font-family:inherit;
                             box-sizing:border-box;resize:vertical;'>
                $v</textarea>";
}




function html_select(string $name, array $options,
                     string $selected = ''): string {
    $html = "<select id='$name' name='$name'>";
    foreach ($options as $val => $label) {
        $sel  = ($val == $selected) ? 'selected' : '';
        $html .= "<option value='" . htmlspecialchars($val) . "' $sel>"
               . htmlspecialchars($label) . "</option>";
    }
    $html .= "</select>";
    return $html;
}




function html_error(array $errors, string $field): string {
    if (!empty($errors[$field])) {
        return "<span class='error'>⚠️ "
               . htmlspecialchars($errors[$field])
               . "</span>";
    }
    return '';
}




// Is unique?
function is_unique($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}




// Is exists?
function is_exists($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}




function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}




// Is POST request?
function is_post() {
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}




// Obtain GET parameter
function get($key, $value = null) {
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}




// Obtain POST parameter
function post($key, $value = null) {
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}




// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null) {
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}
// Set or get temporary session variable
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}
// Obtain uploaded file --> cast to object
function get_file($key) {
    $f = $_FILES[$key] ?? null;
   
    if ($f && $f['error'] == 0) {
        return (object)$f;
    }




    return null;
}




// Crop, resize and save photo
function save_photo($f, $folder, $width = 200, $height = 200) {
    $photo = uniqid() . '.jpg';
   
    require_once 'lib/SimpleImage.php';
    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($width, $height)
        ->toFile("$folder/$photo", 'image/jpeg');




    return $photo;
}




// Is money?
function is_money($value) {
    return preg_match('/^\-?\d+(\.\d{1,2})?$/', $value);
}




$_err = [];




// Generate <span class='err'>
function err($key) {
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class='err'>$_err[$key]</span>";
    }
    else {
        echo '<span></span>';
    }
}




?>



