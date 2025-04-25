<?php

require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_id'])) {
    $todo_id = intval($_POST['todo_id']);

    $sql = "DELETE FROM todos WHERE todo_id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $todo_id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: index.php");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
