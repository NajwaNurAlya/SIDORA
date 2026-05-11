// Controller: Sidebar Toggle
// Dipakai di semua halaman dashboard

document.getElementById('sidebarToggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

// Close sidebar saat menu diklik (mobile)
const menuLinks = document.querySelectorAll('.sidebar-menu-link');
menuLinks.forEach(link => {
    link.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            document.getElementById('sidebar').classList.remove('active');
        }
    });
});
