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
    <script src="./assets/js/app.js"></script>
</body>
</html>
