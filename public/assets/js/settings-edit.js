document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePasswordButtons = document.querySelectorAll('[id^="toggle"]');
    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.id.replace('toggle', '').toLowerCase();
            const passwordField = document.getElementById(targetId === 'password' ? 'password' : 'confirm_password');
            const icon = this.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });
    });
    
    // Validation des mots de passe en temps réel
    function validatePasswords() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const matchDiv = document.getElementById('passwordMatch');
        
        if (password && confirmPassword) {
            if (password === confirmPassword) {
                matchDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>Les mots de passe correspondent</span>';
                matchDiv.className = 'form-text text-success';
            } else {
                matchDiv.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Les mots de passe ne correspondent pas</span>';
                matchDiv.className = 'form-text text-danger';
            }
        } else {
            matchDiv.innerHTML = '';
        }
    }
    
    const passwordField = document.getElementById('password');
    const confirmPasswordField = document.getElementById('confirm_password');
    
    if (passwordField) passwordField.addEventListener('input', validatePasswords);
    if (confirmPasswordField) confirmPasswordField.addEventListener('input', validatePasswords);
    
    // Validation du formulaire
    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password && password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return false;
            }
            
            if (password && password.length < 6) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 6 caractères.');
                return false;
            }
        });
    }
    
    // Validation pour la suppression du compte
    const confirmUsernameField = document.getElementById('confirmUsername');
    if (confirmUsernameField) {
        confirmUsernameField.addEventListener('input', function() {
            const expectedUsername = this.getAttribute('data-expected-username');
            const confirmBtn = document.getElementById('confirmDeleteBtn');
            
            if (this.value === expectedUsername) {
                confirmBtn.disabled = false;
                confirmBtn.classList.remove('disabled');
            } else {
                confirmBtn.disabled = true;
                confirmBtn.classList.add('disabled');
            }
        });
    }
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            const alertInstance = new bootstrap.Alert(alert);
            alertInstance.close();
        });
    }, 5000);
});

function deleteAccount() {
    if (confirm('Êtes-vous absolument sûr ? Cette action ne peut pas être annulée.')) {
        window.location.href = 'delete-account.php';
    }
}