<?php
// Hataları göster
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'database.php';

// Eğer execute_query yoksa buraya da eklenmeli:
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

if (!isset($_SESSION['user_id']) || !isset($_GET['category_id'])) {
    die("Yetkisiz erişim.");
}

$user_id = $_SESSION['user_id'];
$category_id = intval($_GET['category_id']);

// ✅ CSV çıktısı için içerik türü
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=category_todos.csv");
header("Pragma: no-cache");
header("Expires: 0");

// ✅ Türkçe karakter desteği için BOM
echo "\xEF\xBB\xBF";

// ✅ Başlıklar
echo "ToDo Metni;Durum;Güncellenme Tarihi\n";

// ✅ Verileri yaz
$stmt = execute_query(
    $conn,
    "SELECT todo_text, done, updated_at 
     FROM todos 
     WHERE user_id = ? AND category_id = ?",
    "ii",
    $user_id,
    $category_id
);

if ($stmt) {
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $text = str_replace(["\r", "\n", ";"], " ", $row['todo_text']); // Satır içi taşmaları engelle
        $done = $row['done'] ? 'Tamamlandı' : 'Yapılmadı';
        $updated = $row['updated_at'];
        echo "$text;$done;$updated\n";
    }
}

mysqli_close($conn);
exit;
?>
