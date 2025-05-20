<?php
date_default_timezone_set("Europe/Istanbul");
require_once 'config/database.php';

$token = $_GET['token'] ?? '';
$error_message = null;
$success_message = null;
$show_form = false;

if (!$token) {
    $error_message = "Token gelmedi veya eksik.";
} else {
    $query = $conn->prepare("SELECT * FROM users WHERE reset_token = ?");
    $query->bind_param("s", $token);
    $query->execute();
    $result = $query->get_result();

    if ($user = $result->fetch_assoc()) {
        $now = date("Y-m-d H:i:s");
        $expires = $user['reset_expires'];

        if ($expires >= $now) {
            $show_form = true;

            // FORM GÖNDERİLDİYSE
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $password = $_POST['password'] ?? '';
                $confirm = $_POST['password_confirm'] ?? '';

                if ($password !== $confirm) {
                    $error_message = "Şifreler uyuşmuyor. Lütfen tekrar deneyin.";
                } elseif (strlen($password) < 6) {
                    $error_message = "Şifre en az 6 karakter olmalı.";
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ?");
                    $update->bind_param("ss", $hashedPassword, $token);
                    $update->execute();

                    $success_message = "✅ Şifreniz başarıyla güncellendi. Giriş sayfasına yönlendiriliyorsunuz...";
                    $show_form = false;

                    header("refresh:3;url=login.php");
                }
            }

        } else {
            $error_message = "❌ Bu bağlantının süresi dolmuş.";
        }
    } else {
        $error_message = "❌ Geçersiz bağlantı. Kullanıcı bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Şifre Sıfırla</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css" />
</head>
<body>

<div class="reset-container mt-5">
  <h2 class="text-center mb-4">Yeni Şifre Belirle</h2>

  <?php if ($error_message): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($error_message) ?></div>
  <?php endif; ?>

  <?php if ($success_message): ?>
    <div class="alert alert-success text-center"><?= htmlspecialchars($success_message) ?></div>
  <?php endif; ?>

  <?php if ($show_form): ?>
    <form action="" method="POST">
      <div class="mb-3">
        <input type="password" class="form-control" name="password" placeholder="Yeni Şifre" required>
      </div>
      <div class="mb-3">
        <input type="password" class="form-control" name="password_confirm" placeholder="Yeni Şifre (Tekrar)" required>
      </div>
      <button type="submit" class="btn btn-reset w-100">Şifreyi Güncelle</button>
    </form>
  <?php endif; ?>
</div>

</body>
</html>
