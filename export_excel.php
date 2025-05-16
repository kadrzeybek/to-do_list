<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/helpers.php';

// ✅ Kullanıcı doğrulaması
if (!isset($_SESSION['user_id']) || !isset($_GET['category_id'])) {
    die("Yetkisiz erişim.");
}

$user_id = intval($_SESSION['user_id']);
$category_id = intval($_GET['category_id']);

// ✅ Dosya adı ve çıktı ayarları
$filename = "category_{$category_id}_todos.csv";

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Expires: 0");

// ✅ UTF-8 BOM ekle (Excel'de Türkçe karakter sorunu yaşamamak için)
echo "\xEF\xBB\xBF";

// ✅ CSV başlıkları
echo "ToDo Metni;Durum;Güncellenme Tarihi;Bitiş Tarihi\n";

// ✅ Kullanıcıya ait ve kategoriye ait verileri çek
$stmt = execute_query(
    $conn,
    "SELECT todo_text, done, updated_at, due_date 
    FROM todos 
    WHERE user_id = ? AND category_id = ?",
    "ii",
    $user_id,
    $category_id
);

if ($stmt) {
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $text     = str_replace(["\r", "\n", ";"], " ", $row['todo_text']); // CSV bozulmasın
        $status   = $row['done'] ? 'Tamamlandı' : 'Yapılmadı';
        $updated  = $row['updated_at'] ?? '-';
        $due_date = $row['due_date'] ?? '-';
        
        echo "{$text};{$status};{$updated};{$due_date}\n";
    }
}

mysqli_close($conn);
exit;
