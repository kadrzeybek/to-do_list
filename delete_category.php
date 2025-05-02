<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']);

    // Bu kategoriye ait görev var mı kontrol
    $check_stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM todos WHERE category_id = ?");
    mysqli_stmt_bind_param($check_stmt, "i", $category_id);
    mysqli_stmt_execute($check_stmt);
    $result = mysqli_stmt_get_result($check_stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($check_stmt);

    if ($row['total'] > 0) {
        $_SESSION['category_delete_error'] = "Bu kategoriye ait görevler bulunduğu için silinemez.";
    } else {
        $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE category_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $category_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: index.php");
exit();
