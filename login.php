<?php
session_start();
require_once 'database.php';

// Yardımcı Fonksiyon
function execute_query($conn, $sql, $types = '', ...$params) {
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        if (!empty($types)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        mysqli_stmt_execute($stmt);
        return $stmt;
    }
    return false;
}

// --- Giriş İşlemi ---
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = execute_query($conn, "SELECT * FROM users WHERE email = ?", "s", $email);
    if ($stmt) {
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['user_id'] = $row['user_id'];
                header("Location: index.php");
                exit();
            } else {
                echo "<script>alert('Yanlış Şifre!')</script>";
            }
        } else {
            echo "<script>alert('Bu e-posta adresi kayıtlı değil!')</script>";
        }
    } else {
        echo "<script>alert('Sorgu Hatası: " . mysqli_error($conn) . "')</script>";
    }
}

// --- Kayıt İşlemi ---
if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Kullanıcı zaten kayıtlı mı kontrol
    $stmt = execute_query($conn, "SELECT * FROM users WHERE email = ?", "s", $email);
    if ($stmt) {
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            echo "<script>alert('Bu e-posta adresi zaten kayıtlı!')</script>";
        } else {
            // Yeni kullanıcı kaydı
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = execute_query($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)", "sss", $username, $email, $hashedPassword);
            if ($stmt) {
                $_SESSION['show_login'] = true;
                echo "<script>document.addEventListener('DOMContentLoaded', function () {
                        document.querySelector('.toggle').click();
                    });</script>";
            } else {
                echo "<script>alert('Kayıt Başarısız: " . mysqli_error($conn) . "')</script>";
            }
        }
    }
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
    <link rel="stylesheet" href="./css/style.css" />
</head>
<body>
  <main>
    <div class="box">
      <div class="inner-box">
        <div class="forms-wrap">
          <form action="" method="post" autocomplete="off" class="sign-in-form">
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
              <input type="submit" name="login" value="Giriş Yap" class="sign-btn" />
            </div>
          </form>
          <form action="login.php" method="post" autocomplete="off" class="sign-up-form">
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

  <script src="./js/app.js"></script>
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
