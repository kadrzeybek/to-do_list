<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

// --- Giriş İşlemi ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = "Geçerli bir e-posta adresi giriniz.";
        header("Location: login.php");
        exit();
    }

    $stmt = execute_query($conn, "SELECT * FROM users WHERE email = ?", "s", $email);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id'] = $row['user_id'];
            header("Location: index.php");
            exit();
        }
    }

    $_SESSION['login_error'] = "E-posta veya şifre yanlış.";
    header("Location: login.php");
    exit();
}

// --- Kayıt İşlemi ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = "Geçerli bir e-posta adresi giriniz.";
        header("Location: login.php");
        exit();
    }

    // E-posta daha önce kayıtlı mı?
    $stmt = execute_query($conn, "SELECT user_id FROM users WHERE email = ?", "s", $email);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['register_error'] = "Bu e-posta adresi zaten kullanılıyor.";
        header("Location: login.php");
        exit();
    }

    // Yeni kullanıcı kaydı
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = execute_query($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)", "sss", $username, $email, $hashedPassword);

    if ($stmt) {
        $_SESSION['show_login'] = true;
    } else {
        $_SESSION['register_error'] = "Kayıt sırasında hata oluştu.";
    }

    header("Location: login.php");
    exit();
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>To-Do List</title>
    <link rel="stylesheet" href="./assets/css/style.css" />
</head>
<body>
  <main>
    <div class="box">
      <div class="inner-box">
        <div class="forms-wrap">
          <form action="" method="post" autocomplete="off" class="sign-in-form login_form">
            <div class="logo">
              <img src="./img/logo.png"/>
              <h4>ToDoList</h4>
            </div>
            <div class="heading">
              <h2>Tekrar Hoş Geldin</h2>
              <h6>Henüz Hesabın Yok mu?</h6>
              <a href="#" class="toggle">Hesap Oluştur</a>
            </div>
            <div class="actual-form">
              <div class="input-wrap">
                <input type="text" name="email" minlength="4" class="input-field" autocomplete="on" required/>
                <label>Email</label>
              </div>
              <div class="input-wrap">
                <input type="password" name="password" minlength="4" class="input-field" autocomplete="on" required/>
                <label>Şifre</label>
              </div>
              <div class="forget">
                <a href="forgot_password.php" class="forgot-link" style="font-size: 0.9rem; color: #333;">Şifreni mi unuttun?</a>
              </div>
              <?php if (isset($_SESSION['login_error'])): ?>
                <p style="color: red; font-size: 0.9rem; margin-top: -3.8rem; margin-bottom: 2rem;">
                  <?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
                </p>
              <?php endif; ?>
              
              <input type="submit" name="login" value="Giriş Yap" class="sign-btn" />
            </div>
            
          </form>
          <form action="login.php" method="post" autocomplete="off" class="sign-up-form login_form">
            <div class="logo">
              <img src="./img/logo.png"/>
              <h4>toDoList</h4>
            </div>
            <div class="heading">
              <h2>Hadi Başlayalım</h2>
              <h6>Zaten Hesabın Var mı?</h6>
              <a href="#" class="toggle">Giriş Yap</a>
            </div>
            <div class="actual-form">
              <div class="input-wrap">
                <input type="text" name="username" minlength="4" class="input-field" autocomplete="on" required/>
                <label>Kullanıcı Adı</label>
              </div>
              <div class="input-wrap">
                <input type="email" name="email" class="input-field" autocomplete="on" required/>
                <label>Mail</label>
              </div>
              <div class="input-wrap">
                <input type="password" name="password" minlength="4" class="input-field" autocomplete="on" required/>
                <label>Şifre</label>
              </div>
              <?php if (isset($_SESSION['register_error'])): ?>
                <p style="color: red; font-size: 0.9rem; margin-top: -1.5rem; margin-bottom: 1rem;">
                  <?php echo $_SESSION['register_error']; unset($_SESSION['register_error']); ?>
                </p>
              <?php endif; ?>
              <input type="submit" name="submit" value="Hesap Oluştur" class="sign-btn" />
              
            </div>
            
          </form>
          
          </div>
          <div class="carousel">
            <div class="images-wrapper">
              <img src="./img/image1.png" class="image img-1 show" alt="" />
              <img src="./img/image2.png" class="image img-2" alt="" />
              <img src="./img/image3.png" class="image img-3" alt="" />
            </div>
            <div class="text-slider">
              <div class="text-wrap">
                <div class="text-group">
                <h2>Kendi Yapılacaklar Listeni Oluştur</h2>
                <h2>İstediğin gibi Düzenle</h2>
                <h2>Zaman Kaybetme</h2>
              </div>
            </div>
            <div class="bullets">
              <span class="active" data-value="1"></span>
              <span data-value="2"></span>
              <span data-value="3"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="./assets/js/app.js"></script>
  <?php if (isset($_SESSION['show_login']) && $_SESSION['show_login']): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelector('.toggle').click();
    });
  </script>
  <?php unset($_SESSION['show_login']); ?>
<?php endif; ?>

</body>
</html>
