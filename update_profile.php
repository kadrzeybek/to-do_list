<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php'; 

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $avatar = $_POST['avatar'] ?? 'default.png';

    // Avatar doğrulama
    $allowed_avatars = ['man.png', 'woman.png', 'default.png'];
    if (!in_array($avatar, $allowed_avatars)) {
        $avatar = 'default.png';
    }

    // E-posta doğrulama
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['open_profile_modal'] = true;
        $_SESSION['profile_error'] = "Lütfen geçerli bir e-posta adresi girin.";
        header("Location: index.php#profileModal");
        exit();
    }

    // --- Şifre kontrolü ---
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if (!empty($new_password) || !empty($confirm_password)) {
        if (strlen($new_password) < 6) {
            $_SESSION['password_error'] = "Şifre en az 6 karakter olmalıdır.";
            $_SESSION['open_profile_modal'] = true;
            header("Location: index.php#profileModal");
            exit();
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['password_error'] = "Şifreler uyuşmuyor.";
            $_SESSION['open_profile_modal'] = true;
            header("Location: index.php#profileModal");
            exit();
        }

        $hashed = password_hash($new_password, PASSWORD_BCRYPT);
        execute_query($conn, "UPDATE users SET password = ? WHERE user_id = ?", "si", $hashed, $user_id);
    }

    // Profil güncellemesi (şifre harici)
    $stmt = execute_query($conn, "UPDATE users SET username = ?, email = ?, avatar = ? WHERE user_id = ?", "sssi", $username, $email, $avatar, $user_id);

    if ($stmt) {
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['avatar'] = $avatar;
        header("Location: index.php?success=1");
        exit();
    } else {
        $_SESSION['open_profile_modal'] = true;
        $_SESSION['profile_error'] = "Profil güncelleme başarısız.";
        header("Location: index.php#profileModal");
        exit();
    }
}
