const inputs = document.querySelectorAll(".input-field");
const toggle_btn = document.querySelectorAll(".toggle");
const reset_btn = document.querySelectorAll(".reset");
const main = document.querySelector("main");
const bullets = document.querySelectorAll(".bullets span");
const images = document.querySelectorAll(".image");

inputs.forEach((inp) => {
  inp.addEventListener("focus", () => {
    inp.classList.add("active");
  });
  inp.addEventListener("blur", () => {
    if (inp.value != "") return;
    inp.classList.remove("active");
  });
});

toggle_btn.forEach((btn) => {
  btn.addEventListener("click", () => {
    main.classList.toggle("sign-up-mode");
  });
});
reset_btn.forEach((btn) => {
  btn.addEventListener("click", () => {
    main.classList.toggle("forget-mode");
  });
});

function moveSlider() {
  let index = this.dataset.value;

  let currentImage = document.querySelector(`.img-${index}`);
  images.forEach((img) => img.classList.remove("show"));
  currentImage.classList.add("show");

  const textSlider = document.querySelector(".text-group");
  textSlider.style.transform = `translateY(${-(index - 1) * 2.2}rem)`;

  bullets.forEach((bull) => bull.classList.remove("active"));
  this.classList.add("active");
}

bullets.forEach((bullet) => {
  bullet.addEventListener("click", moveSlider);
});

document.addEventListener('DOMContentLoaded', function () {
  const editModal = document.getElementById('editModal');
  if (!editModal) return;

  editModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const todoId = button.getAttribute('data-id');
      const todoText = button.getAttribute('data-text');
      const categoryId = button.getAttribute('data-category-id');
      const modalTodoDueDate = editModal.querySelector('#editTodoDueDate');
      
      
      const modalTodoId = document.getElementById('editTodoId');
      const modalTodoText = document.getElementById('editTodoText');
      const modalCategory = document.getElementById('editTodoCategory');

      modalTodoId.value = todoId;
      modalTodoText.value = todoText;
      modalCategory.value = categoryId;
      modalTodoDueDate.value = button.getAttribute('data-due-date');
  });
});