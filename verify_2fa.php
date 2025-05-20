<?php

session_start();
require_once 'config/database.php';
require_once 'functions/helpers.php';

if (!isset($_SESSION['2fa_user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['2fa_user_id'];
$error = '';
$expired = false;
$remaining_seconds = 0;


$stmt = execute_query($conn, "SELECT * FROM users WHERE user_id = ?", "i", $user_id);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);


if ($user && isset($user['otp_expires'])) {
    $expires_at = strtotime($user['otp_expires']);
    $now_unix = time();
    $remaining_seconds = max(0, $expires_at - $now_unix);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');

    $now = date("Y-m-d H:i:s");
    if ($user) {
        if ($user['otp_code'] === $otp && $user['otp_expires'] >= $now) {
            unset($_SESSION['2fa_user_id']);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } elseif ($user['otp_expires'] < $now) {
            $expired = true;
            $error = "Kodun süresi doldu. Lütfen tekrar giriş yap.";
        } else {
            $error = "Kod yanlış.";
        }
    } else {
        $error = "Kullanıcı bulunamadı.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>2FA Doğrulama</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css" />
</head>
<body class="d-flex justify-content-center align-items-center vh-100">

  <div class="card shadow-lg border-0 p-4" style="max-width: 420px; width: 100%;">
    <div class="card-body text-center">
      <h4 class="card-title mb-3">Giriş Doğrulaması</h4>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <?php if (!$expired): ?>
        <form method="POST">
          <div class="mb-3">
            <input type="text" name="otp" maxlength="6" class="form-control text-center fs-5" placeholder="6 Haneli Kod" required autofocus>
          </div>
          <button type="submit" class="btn btn-reset w-100">Giriş Yap</button>
        </form>

        <p class="mt-3 text-muted">
            Kodun süresi: <span id="timer" class="fw-bold text-danger"><?= $remaining_seconds ?></span> saniye
        </p>

      <?php else: ?>
        <p class="text-danger mt-3 fw-semibold">Kod süresi doldu. <a href="login.php">Yeniden giriş yap</a></p>
      <?php endif; ?>
    </div>
  </div>

<script>
    let timer = parseInt(document.getElementById('timer').textContent);
    const countdown = document.getElementById('timer');
    const interval = setInterval(() => {
        timer--;
        if (countdown && timer >= 0) {
        countdown.textContent = timer;
        }

        if (timer <= 0) {
        clearInterval(interval);
        if (countdown) {
            countdown.textContent = "Süre doldu";
        }
        }
    }, 1000);
</script>



</body>
</html>