<?php
session_start();
require_once 'config/database.php';
require_once 'functions/helpers.php';
require_once 'mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    $stmt = execute_query($conn, "SELECT * FROM users WHERE email = ?", "s", $email);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        $tempPassword = bin2hex(random_bytes(4)); // 8 karakterlik geçici şifre
        $hashed = password_hash($tempPassword, PASSWORD_BCRYPT);

        execute_query($conn, "UPDATE users SET password = ? WHERE email = ?", "ss", $hashed, $email);
        /*
        // Mail gönder
        $mailBody = "Merhaba {$user['username']},<br><br>Geçici şifreniz: <b>$tempPassword</b><br><br>Lütfen giriş yaptıktan sonra profilinizden şifrenizi güncelleyiniz.";

        
        //$mailResult = sendResetMail($email, $mailBody, "Geçici Şifreniz");
        
        */
        $_SESSION['success'] = ($mailResult === true)
            ? "Eğer sistemde bu e-posta varsa, geçici şifre gönderildi."
            : "Mail gönderilemedi: $mailResult";
    } else {
        $_SESSION['success'] = "Eğer sistemde bu e-posta varsa, geçici şifre gönderildi.";
    }

    header("Location: forgot_password.php");
    exit();
}
?>



<form method="POST">
    <h2>Şifremi Unuttum</h2>
    <p>Kayıtlı e-posta adresinizi girin, size bir sıfırlama bağlantısı gönderelim.</p>
    <input type="email" name="email" required placeholder="E-Posta">
    <button type="submit">Gönder</button>
</form>

<?php if (isset($_SESSION['success'])): ?>
  <p style="color: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
<?php endif; ?>
