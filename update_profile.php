<?php
session_start();
require 'database.php';

$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $avatar = $_POST['avatar'] ?? 'default.png';


    $allowed_avatars = ['man.png', 'woman.png', 'default.png'];
    if (!in_array($avatar, $allowed_avatars)) {
        $avatar = 'default.png';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['open_profile_modal'] = true;
        $_SESSION['profile_error'] = "Lütfen geçerli bir e-posta adresi girin.";
        header("Location: index.php#profileModal");
        exit();
    }
    
    $sql = "UPDATE users SET username = ?, email = ?, avatar = ? WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $avatar, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['avatar'] = $avatar;

        header("Location: index.php?success=1");
        exit();
    } else {
        echo "Veritabanı güncelleme başarısız.";
    }
}
