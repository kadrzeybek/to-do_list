<?php
session_start();
require_once 'database.php';

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
