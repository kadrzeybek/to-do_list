
<!-- ToDo Güncelleme Modal -->
<form method="POST" action="index.php">
  <div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg rounded-4 border-0">

        <div class="modal-header border-bottom-0">
          <h5 class="modal-title fw-semibold"><i class="bi bi-pencil-square me-2"></i>ToDo Güncelle</h5>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>

        <div class="modal-body px-4">
          <input type="hidden" name="todo_id" id="editTodoId">

          <div class="mb-3">
            <h6>Görev</h6>
            <input type="text" class="form-control shadow-sm" id="editTodoText" name="todo_text" placeholder="ToDo metnini gir..." required>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <h6>Son Tarih</h6>
              <input type="date" class="form-control shadow-sm" id="editTodoDueDate" name="due_date">
            </div>

            <div class="col-md-6">
              <h6>Kategori</h6>
              <select name="category_id" id="editTodoCategory" class="form-select shadow-sm">
                <?php foreach ($categories as $cat): ?>
                  <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer border-top-0 px-4 pt-3">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i> Vazgeç
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i> Kaydet
          </button>
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
        
        

          <div class="row">
            <div class="col-md-12 d-flex justify-content-center">
                <div class=" ">
                    <input type="radio" class="custom-control-input" id="default" name="avatar" value="default.png" <?php if ($user['avatar'] === 'default.png') echo 'checked'; ?>>
                        <img src="img/default.png" alt="#" style="height:100px; width:100px" class="img-fluid">
                </div>
                <div class="d-flex">
                    <input type="radio" class="custom-control-input" id="woman" name="avatar" value="woman.png" <?php if ($user['avatar'] === 'woman.png') echo 'checked'; ?>>
                    <img src="img/woman.png" alt="#" style="height:100px; width:100px" class="img-fluid">
                </div>
                <div class="">
                    <input type="radio" class="custom-control-input" id="man" name="avatar" value="man.png" <?php if ($user['avatar'] === 'man.png') echo 'checked'; ?>>
                    <img src="img/man.png" alt="#" style="height:100px; width:100px" class="img-fluid">
                </div>
            </div>
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
  <div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg rounded-4">

        <div class="modal-header bg-light border-0">
          <h5 class="modal-title text-danger fw-semibold">
            <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>
            Hesabı Silmek Üzeresiniz
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
        </div>

        <div class="modal-body text-center">
          <p class="text-muted fs-6 mb-3">
            Bu işlem <strong>geri alınamaz</strong>. Tüm verileriniz <span class="text-danger fw-bold">kalıcı olarak silinecektir.</span>
          </p>
          <p class="text-muted">Devam etmek istediğinizden emin misiniz?</p>
        </div>

        <div class="modal-footer border-top-0 justify-content-center">
          <input type="hidden" name="user_id" value="<?= $user_id ?>">
          <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i> Vazgeç
          </button>
          <button type="submit" class="btn btn-danger px-4">
            <i class="bi bi-trash-fill me-1"></i> Evet, Hesabımı Sil
          </button>
        </div>

      </div>
    </div>
  </div>
</form>

