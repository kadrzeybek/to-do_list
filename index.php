<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config/database.php';
require_once 'functions/helpers.php';


// --- Kullanıcı Kontrolü ve Bilgileri Çek ---
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Kullanıcı bilgilerini çek
$stmt = execute_query($conn, "SELECT user_id, avatar, email FROM users WHERE username = ?", "s", $username);
if ($stmt) {
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    $_SESSION['user_id'] = $user['user_id'];
    $user_id = $user['user_id'];
} else {
    die("Kullanıcı bilgileri alınamadı.");
}
// --- Yeni Kategori Ekle ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    $category_color = $_POST['category_color'];

    if (!empty($category_name) && !empty($category_color)) {
        execute_query($conn, "INSERT INTO categories (category_name, color, user_id) VALUES (?, ?, ?)", "ssi", $category_name, $category_color, $user_id);

    }

    header("Location: index.php");
    exit();
}


$categoryColors = [];
$categories = [];

$category_stmt = execute_query($conn, "SELECT category_id, category_name, color FROM categories WHERE user_id = ?", "i", $user_id);

if ($category_stmt) {
    $result = mysqli_stmt_get_result($category_stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row; 
        $categoryColors[$row['category_name']] = $row['color'];
    }
}


//paging
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1);
$offset = ($page - 1) * $limit;
$count_stmt = execute_query($conn, "SELECT COUNT(*) AS total FROM todos WHERE user_id = ?", "i", $user_id);
$count_result = mysqli_stmt_get_result($count_stmt);
$row = mysqli_fetch_assoc($count_result);
$total_todos = $row['total'];

$total_pages = ceil($total_todos / $limit);

// --- Yeni ToDo Ekleme ---
if (isset($_POST['add_todo'])) {
    $todo_text = trim($_POST['todo_text']);
$category_id = intval($_POST['category_id']) ?? null;

if (!empty($todo_text)) {
    execute_query($conn, "INSERT INTO todos (user_id, todo_text, category_id) VALUES (?, ?, ?)", "isi", $user_id, $todo_text, $category_id);
}

    header("Location: index.php");
    exit();
}

// --- ToDo Güncelleme (Metin) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_id'], $_POST['todo_text'], $_POST['category_id']) && !isset($_POST['done'])) {
    $todo_id = intval($_POST['todo_id']);
    $todo_text = trim($_POST['todo_text']);
    $category_id = intval($_POST['category_id']);
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;


    if (!empty($todo_text) && $category_id > 0) {
        execute_query($conn, "UPDATE todos SET todo_text = ?, category_id = ?, due_date = ?, updated_at = CURRENT_TIMESTAMP WHERE todo_id = ?", "sisi", $todo_text, $category_id, $due_date, $todo_id);

    }

    header("Location: index.php");
    exit();
}



// --- ToDo Güncelleme (Yapıldı) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_id'], $_POST['done'])) {
    $todo_id = intval($_POST['todo_id']);
    $done = ($_POST['done'] === '1') ? 1 : 0;
    execute_query($conn, "UPDATE todos SET done = ?, updated_at = CURRENT_TIMESTAMP WHERE todo_id = ?", "ii", $done, $todo_id);
    header("Location: index.php");
    exit();
}


// --- Kullanıcıya Ait Tüm ToDo'ları Çek ---
$filter_category_id = isset($_GET['category']) ? intval($_GET['category']) : null;

if ($filter_category_id) {
    $stmt = execute_query(
        $conn,
        "SELECT todos.*, categories.category_name 
        FROM todos 
        LEFT JOIN categories ON todos.category_id = categories.category_id
        WHERE todos.user_id = ? AND todos.category_id = ?
        ORDER BY todos.done ASC, todos.updated_at DESC
        LIMIT ? OFFSET ?",
        "iiii",
        $user_id, $filter_category_id, $limit, $offset
    );
    
    
} else {
    $stmt = execute_query(
        $conn,
        "SELECT todos.*, categories.category_name 
        FROM todos 
        LEFT JOIN categories ON todos.category_id = categories.category_id
        WHERE todos.user_id = ?
        ORDER BY todos.done ASC, todos.updated_at DESC
        LIMIT ? OFFSET ?",
        "iii",
        $user_id, $limit, $offset
    );
}



if ($stmt) {
    $todos = mysqli_stmt_get_result($stmt);
} else {
    die("To-Do listesi alınamadı.");
}
?>

<?php include 'includes/header.php' ?>
    <div class="d-flex flex-column gap-3">
        <?php 
            include 'includes/category_form.php';
            include 'includes/category_list.php';
        ?>
        <div class="col-lg-7">
            <div class="card p-4 pb-1 shadow-sm " style="border-bottom: none; border-top: none;" >
                <?php
                    include 'includes/todo_form.php';
                    include 'includes/todo_list.php'
                ?>
            </div>
        </div>
    </div>


<?php include 'includes/modals.php';
include 'includes/footer.php'
?>

