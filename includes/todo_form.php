<form method="post" class="w-100 mb-3">
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