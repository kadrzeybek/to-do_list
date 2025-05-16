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