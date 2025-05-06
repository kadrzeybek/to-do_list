<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'database.php';

// Define the execute_query function
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

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && intval($_POST['user_id']) === $_SESSION['user_id']) {
    $user_id = $_SESSION['user_id'];

    execute_query($conn, "DELETE FROM todos WHERE user_id = ?", "i", $user_id);
    execute_query($conn, "DELETE FROM categories WHERE user_id = ?", "i", $user_id);
    execute_query($conn, "DELETE FROM users WHERE user_id = ?", "i", $user_id);

    session_destroy();
    header("Location: login.php");
    exit();
} else {
    echo "Silme başarısız.";
    exit();
}
