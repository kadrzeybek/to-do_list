<?php
date_default_timezone_set("Europe/Istanbul");
require_once 'config/database.php';
require_once 'mailer.php';

$success_message = null;
$error_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Geçerli bir e-posta adresi girin.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            $token = bin2hex(random_bytes(32));
            $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
            $update->bind_param("sss", $token, $expires, $email);
            $update->execute();

            $link = "http://localhost/toDoList/reset_password.php?token=$token";
            sendResetMail($email, $link);

            $success_message = "📩 Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.";
        } else {
            $error_message = "Bu e-posta adresiyle kayıtlı kullanıcı bulunamadı.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Şifre Sıfırlama Talebi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css" />
</head>
<body>

<div class="form-wrapper mt-5">
  <h2 class="text-center mb-4 text-h2">Şifremi Unuttum</h2>

  <?php if ($success_message): ?>
    <div class="alert alert-success text-center"><?= htmlspecialchars($success_message) ?></div>
  <?php elseif ($error_message): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($error_message) ?></div>
  <?php endif; ?>

  <?php if (!$success_message): ?>
    <form action="" method="POST">
      <div class="mb-3">
        <input type="email" class="reset-form form-control" id="email" name="email" placeholder="Kayıtlı E-posta" required>
      </div>
      <button type="submit" class="btn btn-reset w-100">Sıfırlama Linki Gönder</button>
    </form>
  <?php endif; ?>

  <p class="mt-3 text-center text-muted">
    Şifre sıfırlama bağlantısı 10 dakika boyunca geçerli olacaktır.
  </p>
</div>

</body>
</html>
