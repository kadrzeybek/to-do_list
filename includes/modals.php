
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
  <div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content shadow-lg border-0 rounded-4">

        <div class="modal-header border-bottom-0">
          <h5 class="modal-title"><i class="bi bi-person-circle me-2"></i>Profil Bilgileri</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>

        <div class="modal-body">
          <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
          <?php endif; ?>

          <div class="text-center mb-4">
            <p class="mb-1 fw-semibold">Avatar Seç</p>
            <div class="d-flex justify-content-center gap-4">
              <?php
                $avatars = ['default.png', 'man.png', 'woman.png'];
                foreach ($avatars as $avt):
              ?>
              <label class="position-relative">
                <input type="radio" name="avatar" value="<?= $avt ?>" class="visually-hidden"
                  <?= ($user['avatar'] === $avt) ? 'checked' : '' ?>>
                <img src="img/<?= $avt ?>" class="rounded-circle border border-2 <?= ($user['avatar'] === $avt) ? 'border-primary' : 'border-secondary' ?>" style="height: 80px; width: 80px; object-fit: cover; cursor: pointer;">
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="row">
            <div class="col-12 my-2">
              <h6 >Kullanıcı Adı</h6>
              <input type="text" name="username" class="form-control shadow-sm" value="<?= htmlspecialchars($username) ?>" required>
            </div>

            <div class="col-12 my-2">
              <h6>E-posta</h6>
              <input type="email" name="email" class="form-control shadow-sm" value="<?= htmlspecialchars($user['email']) ?>" required>
              <?php if (isset($_SESSION['profile_error'])): ?>
                <div class="text-danger mt-1 small"><?= $_SESSION['profile_error']; unset($_SESSION['profile_error']); ?></div>
              <?php endif; ?>
            </div>
            <div class="col-md-12">
                <h6>Şifre Değiştirme Formu</h6>
            </div>
            <div class="col-md-6">
              <input type="password" name="new_password" id="new_password" class="form-control shadow-sm" placeholder="Yeni Şifre">
            </div>

            <div class="col-md-6">
              
              <input type="password" name="confirm_password" id="confirm_password" class="form-control shadow-sm" placeholder="Şifre Tekrar">
            </div>

            <?php if (isset($_SESSION['password_error'])): ?>
              <div class="col-12 text-danger small"><?= $_SESSION['password_error']; unset($_SESSION['password_error']); ?></div>
            <?php endif; ?>
          </div>
        </div>

        <div class="modal-footer border-top-0 pt-3">
          <button type="button" class="btn btn-outline-danger me-auto" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            <i class="bi bi-trash me-1"></i> Hesabımı Sil
          </button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Kaydet
          </button>
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
