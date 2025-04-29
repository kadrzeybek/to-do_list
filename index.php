<?php

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
    // Hata yönetimi
    die("Kullanıcı bilgileri alınamadı.");
}

// --- Yeni ToDo Ekleme ---
if (isset($_POST['add_todo'])) {
    $todo_text = trim($_POST['todo_text']);
    if (!empty($todo_text)) {
        execute_query($conn, "INSERT INTO todos (user_id, todo_text) VALUES (?, ?)", "is", $user_id, $todo_text);
    }
    header("Location: index.php");
    exit();
}

// --- ToDo Güncelleme (Metin) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['todo_id'], $_POST['todo_text']) && !isset($_POST['done'])) {
    $todo_id = intval($_POST['todo_id']);
    $todo_text = trim($_POST['todo_text']);
    if (!empty($todo_text)) {
        execute_query($conn, "UPDATE todos SET todo_text = ?, updated_at = CURRENT_TIMESTAMP WHERE todo_id = ?", "si", $todo_text, $todo_id);
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
$stmt = execute_query($conn, "SELECT * FROM todos WHERE user_id = ? ORDER BY done ASC, updated_at DESC", "i", $user_id);

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
</head>
<body>
    <div class="container py-3">
        <div class="d-flex justify-content-center">
            <div class="mb-1 p-3 position-relative text-center" style="max-width: 700px; width: 100%;"><img id="currentAvatar" src="img/<?php echo htmlspecialchars($user['avatar']); ?>" width="100" height="100" class="rounded-circle ">
                <div class="dropdown position-absolute top-0 end-0 m-3">
                    <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-fill"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink">
                    <li>
            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">
                Profili Düzenle
            </button>
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
        <div class="mx-auto rounded-4 bg-white p-4 shadow-sm" style="max-width: 700px;">
            <form method="post" style="max-width: 700px;">
                <div class="d-flex mb-3">
                    <input type="text" name="todo_text" class="form-control me-2" placeholder="Yapacağınız Şey..." required>
                    <button type="submit" name="add_todo" class="btn text-white fw-bold px-3" style="background-color:#ff8269;">+</button>
                </div>
            </form>
            <ul class="list-group">
                <?php while ($todo = mysqli_fetch_assoc($todos)): ?>
                    <li class="list-group-item d-flex align-items-center justify-content-between gap-2 <?php echo ($todo['done'] ?? 0) ? 'completed' : ''; ?>">
                        <div class="d-flex align-items-start flex-grow-1 overflow-hidden gap-2" style="min-width: 0;">
                            <form method="post" class="m-0 p-0" style="display: inline-block; width: auto;">
                                <input type="hidden" name="todo_id" value="<?php echo $todo['todo_id']; ?>">
                                <input type="hidden" name="done" value="<?php echo ($todo['done'] ?? 0) ? '0' : '1'; ?>">
                                <input type="checkbox" class="form-check-input custom-checkbox" onchange="this.form.submit()" <?php echo ($todo['done'] ?? 0) ? 'checked' : ''; ?>>
                            </form>
                            <span class="text-break text-start <?php echo ($todo['done'] ?? 0) ? 'text-decoration-line-through text-muted' : ''; ?>">
                                <?php echo htmlspecialchars($todo['todo_text']); ?>
                            </span>
                        </div>

                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="<?php echo $todo['todo_id']; ?>"
                                    data-text="<?php echo htmlspecialchars($todo['todo_text']); ?>">
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
                <?php endwhile; ?>
            </ul>
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
                    <input type="text" class="form-control  px-4 py-2" id="editTodoText" name="todo_text" placeholder="Update your task..." required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
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
                            <!-- Avatar Seçimi -->
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

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function updateAvatarPreview(avatar) {
        document.getElementById('currentAvatar').src = 'img/' + avatar;
    }

    document.querySelectorAll('input[name="avatar"]').forEach(radio => {
        radio.addEventListener('change', function () {
            const selectedAvatar = this.value;
            updateAvatarPreview(selectedAvatar);
        });
    });
</script>

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
