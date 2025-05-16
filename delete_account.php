<?php


session_start();

require_once  __DIR__.'/config/database.php';
require_once  __DIR__.'/functions/helpers.php';

// login sayfasına yönlendirme
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

//  kullanıcı doğrulaması
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $post_user_id = intval($_POST['user_id']);
    $session_user_id = $_SESSION['user_id'];


    if ($post_user_id === $session_user_id) {
        // Kullanıcıya ait verileri sil
        execute_query($conn, "DELETE FROM todos WHERE user_id = ?", "i", $session_user_id);
        execute_query($conn, "DELETE FROM categories WHERE user_id = ?", "i", $session_user_id);
        execute_query($conn, "DELETE FROM users WHERE user_id = ?", "i", $session_user_id);
        session_destroy();
        session_start();
        $_SESSION['deleted'] = "Hesabınız başarıyla silindi.";
        header("Location: login.php");
        exit();
    } else {
        echo "Kullanıcı doğrulaması başarısız.";
        exit();
    }
} else {
    echo "Silme işlemi başarısız.";
    exit();
}
