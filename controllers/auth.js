// Controller: Auth
// Dipakai di login.html dan register-rs.html

// Toggle password visibility - password utama
document.getElementById('togglePassword').addEventListener('click', function () {
    const input = document.getElementById('password');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    document.getElementById('iconEye').style.display    = isHidden ? 'none' : '';
    document.getElementById('iconEyeOff').style.display = isHidden ? ''     : 'none';
});
