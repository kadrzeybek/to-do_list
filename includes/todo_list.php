<ul  class="list-group todo-list-scroll" style="min-height: 503px; overflow-y: auto;">
    <?php $hasTodo = false; while ($todo = mysqli_fetch_assoc($todos)) {
            $hasTodo = true; ?>
        <li class="list-group-item d-flex align-items-center justify-content-between gap-2  <?php echo ($todo['done'] ?? 0) ? 'completed' : ''; ?>">
            <div class="d-flex align-items-start flex-grow-1 gap-2" style="min-width: 0; overflow-x: auto; white-space: nowrap;">
                <form method="post" class="m-0 p-0" style="display: inline-block; width: auto;">
                    <input type="hidden" name="todo_id" value="<?php echo $todo['todo_id']; ?>">
                    <input type="hidden" name="done" value="<?php echo ($todo['done'] ?? 0) ? '0' : '1'; ?>">
                    <input type="checkbox" class="form-check-input custom-checkbox" onchange="this.form.submit()" <?php echo ($todo['done'] ?? 0) ? 'checked' : ''; ?>>
                </form>
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 12px; height: 12px; border-radius: 50%; background-color: <?php echo $categoryColors[$todo['category_name']] ?? '#ced4da'; ?>;">
                    </div>
                    <div class="todo-text-scroll-wrapper">
                        <div class="todo-text-scroll <?php echo ($todo['done'] ?? 0) ? 'text-decoration-line-through text-muted' : ''; ?>">
                            <?php echo htmlspecialchars($todo['todo_text']); ?>

                            <?php if (!empty($todo['due_date'])): ?>
                                <small class="todo-date text-muted"><?php echo htmlspecialchars(date('d.m.Y', strtotime($todo['due_date']))); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
            <div class="d-flex gap-1">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal" data-id="<?php echo $todo['todo_id']; ?>" data-text="<?php echo htmlspecialchars($todo['todo_text']); ?>" data-category-id="<?php echo $todo['category_id']; ?>" data-due-date="<?php echo $todo['due_date']; ?>">
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
            <img src="img/empty_todo.svg" alt="Boş Liste" style="max-height: 430px;" class=" img-fluid mb-3">
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