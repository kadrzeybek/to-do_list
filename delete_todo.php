<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_id'])) {
    $todo_id = intval($_POST['todo_id']);
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        header("Location: login.php");
        exit();
    }

    // ToDo kontrol
    $check_stmt = execute_query($conn, "SELECT todo_id FROM todos WHERE todo_id = ? AND user_id = ?", "ii", $todo_id, $user_id);
    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) > 0) {
        execute_query($conn, "DELETE FROM todos WHERE todo_id = ?", "i", $todo_id);
    }

    header("Location: index.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
