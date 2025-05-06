<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'database.php';

// --- Veritabanı Veri Alma Yardımcı Fonksiyonu ---
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

    if (!empty($todo_text) && $category_id > 0) {
        execute_query($conn, "UPDATE todos SET todo_text = ?, category_id = ?, updated_at = CURRENT_TIMESTAMP WHERE todo_id = ?", "sii", $todo_text, $category_id, $todo_id);
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

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link href="./css/style.css" rel="stylesheet">
<style>
    
</style>
</head>
<body>
    <div class="container py-3 px-4">
    <div class="row justify-content-center align-items-stretch">
    <div class="col-lg-3 mb-3 mb-lg-0">
                <div class="d-flex justify-content-center">
                    <div class="mb-1 position-relative text-center" style=" width: 100%;"><img id="currentAvatar" src="img/<?php echo htmlspecialchars($user['avatar']); ?>" width="100" height="100" class="rounded-circle ">
                        <div class="dropdown position-absolute top-0 end-0 m-3">
                            <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-fill"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                                <li>
                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">Profili Düzenle</button>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="logout.php">Çıkış Yap</a>
                                </li>
                            </ul>
                        </div>
                        <h1 class="fw-bold text-dark m-0">To-Do List</h1>
                        <p class="text-muted mt-2">Hoşgeldin <?php echo htmlspecialchars($username); ?></p>
                    </div>
                </div>
                <div class="d-flex flex-column gap-3">
                    <div class="card p-3 shadow-sm ">
                        <h5 class="fw-bold mb-3">Kategori Ekle</h5>
                        <form method="POST" action="index.php">
                            <div class="d-flex mb-3 gap-1">
                                <input type="text" name="category_name" class="form-control" placeholder="Kategori Adı" required>
                                <input type="color" name="category_color" class="form-control form-control-color" title="Kategori Rengi Seç" required>
                            </div>
                            <button type="submit" name="add_category" class="btn btn-warning w-100">Kategori Ekle</button>

                        </form>
                    </div>

                    <div class="card p-3  mb-md-0 shadow-sm h-100 mb-3">
                        <h5 class="fw-bold mb-3">Kategoriler</h5>
                        <ul class="list-group h-100" style="max-height: 193px; overflow-y: auto;">
                            <li class="list-group-item  
                                <?php if (!$filter_category_id) echo 'active_1'; ?>">
                                    <a href="index.php" class="text-decoration-none text-dark d-block">Tümünü Göster</a>
                            </li>
                            <?php if (empty($categories)): ?>
                                <li class="list-group-item text-center">
                                    <img src="./img/dog.svg" class="img-fluid mb-2" style="max-height: 100px;" alt="Kategori yok">
                                    <div>Henüz kategori yok.</div>
                                </li>
                            <?php endif; ?>
                            <?php foreach ($categories as $cat): ?>
                                <li class="list-group-item d-flex align-items-center justify-content-between <?php if ($filter_category_id == $cat['category_id']) echo 'active_1'; ?>">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 12px; height: 12px; border-radius: 50%; background-color: <?php echo htmlspecialchars($cat['color']); ?>;"></div>
                                        <a href="?category=<?php echo $cat['category_id']; ?>" class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($cat['category_name']); ?>
                                        </a>
                                    </div>
                                    
                                    <form method="POST" action="delete_category.php" class="m-0 p-0">
                                        <input type="hidden" name="category_id" value="<?php echo $cat['category_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 2px 6px;">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                            
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="card p-4 pb-1 shadow-sm " style="border-bottom: none; border-top: none;" >
                    <?php
                        $category_stmt = execute_query($conn, "SELECT * FROM categories WHERE user_id = ?", "i", $user_id);
                        $categories = mysqli_stmt_get_result($category_stmt);
                        
                    ?>
                    <form method="post" class="w-100">
                        <div class="d-flex w-100 mb-2 align-items-center todo_form">
                            <input type="text" name="todo_text" class="form-control flex-grow-1 me-2" placeholder="Yapacağınız Şey..." required>
                            <select name="category_id" class="form-select me-2 category_select">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="add_todo" class="btn text-white fw-bold px-3" style="background-color:#ff8269;">+</button>
                        </div>
                    </form>
                    <ul  class="list-group todo-list-scroll" style="min-height: 503px; overflow-y: auto;">
                        <?php
                            $hasTodo = false;
                            while ($todo = mysqli_fetch_assoc($todos)) {
                                $hasTodo = true;
                        ?>
                            <li class="list-group-item d-flex align-items-center justify-content-between gap-2  <?php echo ($todo['done'] ?? 0) ? 'completed' : ''; ?>">
                                <div class="d-flex align-items-start flex-grow-1 gap-2" style="min-width: 0; overflow-x: auto; white-space: nowrap;">
                                    <form method="post" class="m-0 p-0" style="display: inline-block; width: auto;">
                                        <input type="hidden" name="todo_id" value="<?php echo $todo['todo_id']; ?>">
                                        <input type="hidden" name="done" value="<?php echo ($todo['done'] ?? 0) ? '0' : '1'; ?>">
                                        <input type="checkbox" class="form-check-input custom-checkbox" onchange="this.form.submit()" <?php echo ($todo['done'] ?? 0) ? 'checked' : ''; ?>>
                                    </form>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 12px; height: 12px; border-radius: 50%; background-color: <?php echo $categoryColors[$todo['category_name']] ?? '#ced4da'; ?>;"></div>
                                        <div class="todo-text-scroll <?php echo ($todo['done'] ?? 0) ? 'text-decoration-line-through text-muted' : ''; ?>">
                                            <?php echo htmlspecialchars($todo['todo_text']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal"
                                        data-id="<?php echo $todo['todo_id']; ?>"
                                        data-text="<?php echo htmlspecialchars($todo['todo_text']); ?>"
                                        data-category-id="<?php echo $todo['category_id']; ?>">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>

                                    <form method="post" action="delete_todo.php" class="m-0 p-0">
                                        <input type="hidden" name="todo_id" value="<?php echo $todo['todo_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        <?php } ?>

                        <?php if (!$hasTodo): ?>
                            <li class="list-group-item text-center mb-3 d-flex flex-column align-items-center justify-content-center pt-5">
                                <img src="img/empty_todo.svg" alt="Boş Liste" style="max-height: 443px;" class=" img-fluid mb-3">
                                <p class="text-muted">Yapılacaklar listen şu an boş. Hemen bir şeyler eklemeye ne dersin? 🚀</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <?php if ($hasTodo): ?>
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                                    <a class="page-link" href="<?php if ($page > 1) echo '?page=' . ($page - 1); else echo '#'; ?>">Önceki</a>
                                </li>
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                                    <a class="page-link" href="<?php if ($page < $total_pages) echo '?page=' . ($page + 1); else echo '#'; ?>">Sonraki</a>
                                </li>
                            </ul>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>







<form method="POST" action="index.php">
    <div id="editModal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✏️ Edit ToDo</h5>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="todo_id" id="editTodoId">

                    <!-- Text -->
                    <input type="text" class="form-control px-4 py-2 mb-3" id="editTodoText" name="todo_text" placeholder="Update your task..." required>

                    <!-- Category -->
                    <select name="category_id" id="editTodoCategory" class="form-select">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                        <?php endforeach; ?>
                    </select>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
</form>



<form method="POST" action="update_profile.php" enctype="multipart/form-data">
<?php if (isset($error)): ?>
  <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>
    <div id="profileModal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">👤 Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>

                <div class="modal-body">
                    <!-- Avatar -->
                    <div class="text-center">
                            <p class="text-center">Avatar Seç</p>
                        </div>
                        <div class="row justify-content-center mb-3 py-4">
                            <div class="col-4 col-md-3 position-relative">
                                <input type="radio" class="custom-control-input" id="default" name="avatar" value="default.png" <?php if ($user['avatar'] === 'default.png') echo 'checked'; ?>>
                                <label class="custom-control-label ms-4" for="default">
                                    <img src="img/default.png" alt="#" style="height:100px; width:100px" class="img-fluid">
                                </label>
                            </div>
                            <div class="col-4 col-md-3 position-relative">
                                <input type="radio" class="custom-control-input" id="man" name="avatar" value="man.png" <?php if ($user['avatar'] === 'man.png') echo 'checked'; ?>>
                                <label class="custom-control-label ms-4" for="man">
                                    <img src="img/man.png" alt="#" style="height:100px; width:100px" class="img-fluid">
                                </label>
                            </div>
                            <div class="col-4 col-md-3 position-relative">
                                <input type="radio" class="custom-control-input" id="woman" name="avatar" value="woman.png" <?php if ($user['avatar'] === 'woman.png') echo 'checked'; ?>>
                                <label class="custom-control-label ms-4" for="woman">
                                    <img src="img/woman.png" alt="#" style="height:100px; width:100px" class="img-fluid">
                                </label>
                            </div>
                        </div>
                        
                        <div class="row">
                    
                        <div class="col-12 mb-3">
                            <p>Kullanıcı Adı</p>
                            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        <div class="col-12 mb-3">
                            <p>E-Posta</p>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            <?php if (isset($_SESSION['profile_error'])): ?>
                                <div id="email-error-msg" class="text-danger mt-1" style="font-size: 0.875rem;">
                                    <?php echo $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Hesabımı Sil</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Hesap Silme Onay Modali -->
<form method="POST" action="delete_account.php">
    <div id="deleteAccountModal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Hesabınızı Silmek İstediğinize Emin misiniz?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <div class="modal-body">
                    <p>Bu işlem geri alınamaz. Tüm verileriniz silinecektir.</p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-danger">Evet, Sil</button>
                </div>
            </div>
        </div>
    </div>
</form>


<?php if (isset($_SESSION['open_profile_modal']) && $_SESSION['open_profile_modal']): ?>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const profileModal = new bootstrap.Modal(document.getElementById('profileModal'));
            profileModal.show();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const profileModalElement = document.getElementById('profileModal');

            profileModalElement.addEventListener('hidden.bs.modal', () => {
                const errorDiv = document.getElementById('email-error-msg');
                if (errorDiv) {
                    errorDiv.remove();
                }
            });
        });
    </script>

<?php unset($_SESSION['open_profile_modal']); ?>
<?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" 
    integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" 
    crossorigin="anonymous"></script>
    <script src="./js/app.js"></script>
</body>
</html>
