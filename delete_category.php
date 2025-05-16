<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']);
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        header("Location: login.php");
        exit();
    }

    // 🔒 Kategori kontrol
    $check_owner = execute_query($conn, "SELECT category_id FROM categories WHERE category_id = ? AND user_id = ?", "ii", $category_id, $user_id);
    $owner_result = mysqli_stmt_get_result($check_owner);

    if (mysqli_num_rows($owner_result) === 0) {
        $_SESSION['category_delete_error'] = "Yetkiniz olmayan bir kategoriyi silemezsiniz.";
        header("Location: index.php");
        exit();
    }

    $check_stmt = execute_query($conn, "SELECT COUNT(*) AS total FROM todos WHERE category_id = ?", "i", $category_id);
    $result = mysqli_stmt_get_result($check_stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row['total'] > 0) {
        $_SESSION['category_delete_error'] = "Bu kategoriye ait görevler bulunduğu için silinemez.";
    } else {
        execute_query($conn, "DELETE FROM categories WHERE category_id = ?", "i", $category_id);
    }
}

header("Location: index.php");
exit();
