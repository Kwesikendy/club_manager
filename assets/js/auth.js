// Client-side form validation
function validateRegisterForm() {
    const password = document.getElementById('password').value;
    if (password.length < 8) {
        alert('Password must be at least 8 characters');
        return false;
    }
    return true;
}