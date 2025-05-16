<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
    <link href="./assets/css/style.css" rel="stylesheet">
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
