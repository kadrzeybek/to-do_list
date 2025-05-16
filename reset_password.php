<?php
session_start();
require_once 'config/database.php';
require_once 'functions/helpers.php';

// Token kontrolü
$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Token belirtilmedi.");
}

// Token doğrulama
$stmt = execute_query($conn, "SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()", "s", $token);
$result = mysqli_stmt_get_result($stmt);

if (!$user = mysqli_fetch_assoc($result)) {
    die("Geçersiz veya süresi dolmuş bağlantı.");
}

// Form gönderildiyse
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Şifre doğrulama
    if (strlen($new_password) < 6) {
        $error = "Şifre en az 6 karakter olmalıdır.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Şifreler uyuşmuyor.";
    } else {
        // Şifreyi hash'le ve veritabanını güncelle
        $hashed = password_hash($new_password, PASSWORD_BCRYPT);

        execute_query($conn, "UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE user_id = ?", "si", $hashed, $user['user_id']);

        // Oturum aç ve yönlendir
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: index.php");
        exit();
    }
}
?>

<!-- Şifre güncelleme formu -->
<form method="POST">
    <h2>Yeni Şifre Belirle</h2>

    <?php if (!empty($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <input type="password" name="password" required placeholder="Yeni Şifre">
    <input type="password" name="confirm_password" required placeholder="Şifreyi Tekrar Girin">
    <button type="submit">Şifreyi Güncelle</button>
</form>
