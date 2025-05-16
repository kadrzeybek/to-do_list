
<!-- ToDo Güncelleme Modal -->
<form method="POST" action="index.php">
    <div id="editModal" class="modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">✏️ToDo Güncelleme</h5>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="todo_id" id="editTodoId">
                    <input type="text" class="form-control px-4 py-2 mb-3" id="editTodoText" name="todo_text" placeholder="To-do metnini gir..." required>
                    <div class="d-flex align-items-center gap-4">
                        <div class="w-50">
                            <p>Son Tarih</p>
                            <input type="date" class="form-control w-40" id="editTodoDueDate" name="due_date">
                        </div>
                        <div class="w-50">
                            <p>Kategori Seç</p>
                            <select name="category_id" id="editTodoCategory" class="form-select">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['category_id']; ?>"><?php echo htmlspecialchars($cat['category_name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Vazgeç</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
</form>


<!-- Profil Güncelleme Modal -->
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
                        
                        <div class="row mt-4">
                            <div class="col-12 mb-2">
                                <h6>Kullanıcı Bilgileri</h6>
                                <input type="text" name="username" class="form-control" placeholder="Kullanıcı adı" value="<?php echo htmlspecialchars($username); ?>" required>
                            </div>
                            <div class="col-12 mb-2">
                                <input type="email" name="email" class="form-control" placeholder="E-mail" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                <?php if (isset($_SESSION['profile_error'])): ?>
                                    <div id="email-error-msg" class="text-danger mt-1" style="font-size: 0.875rem;">
                                        <?php echo $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-12 mb-2">
                                <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Yeni şifre">
                            </div>

                            <div class="col-12 mb-2">
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Yeni şifreyi tekrar gir">
                            </div>

                            <?php if (isset($_SESSION['password_error'])): ?>
                                <div class="text-danger mt-1" style="font-size: 0.875rem;">
                                    <?php echo $_SESSION['password_error']; unset($_SESSION['password_error']); ?>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger me-auto" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Hesabımı Sil</button>
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
