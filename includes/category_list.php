<div class="card p-3  mb-md-0 shadow-sm h-100 mb-3">
    <h5 class="fw-bold mb-3">Kategoriler</h5>
        <ul class="list-group h-100" style="max-height: 193px; overflow-y: auto;">
            <li class="list-group-item  
                <?php if (!$filter_category_id) echo 'active_1'; ?>">
                    <a href="index.php" class="text-decoration-none text-dark d-block">Tümünü Göster</a>
            </li>
            <?php if (isset($_SESSION['category_delete_error'])): ?>
                <div class="alert alert-danger mt-2">
                    <?php echo $_SESSION['category_delete_error']; unset($_SESSION['category_delete_error']); ?>
                </div>
            <?php endif; ?>
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
                                    <a href="export_excel.php?category_id=<?php echo $cat['category_id']; ?>" class="btn btn-sm btn-outline-success me-1" style="padding: 2px 6px;" title="Excel İndir">
                                        <i class="bi bi-file-earmark-excel-fill"></i>
                                    </a>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" style="padding: 2px 6px;">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            <?php endforeach; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>