// Controller: Dashboard Rumah Sakit
// Dipakai di rumahsakit/dashboard.html

// Show profile menu only on dashboard page
function initProfileMenuVisibility() {
    const currentPage = window.location.pathname.split('/').pop() || 'dashboard.html';
    const profilMenuItem = document.getElementById('profilMenuItem');
    const pengaturanTitle = document.getElementById('pengaturanTitle');
    
    if (currentPage === 'dashboard.html' || currentPage === '') {
        // Show profil menu and pengaturan title
        profilMenuItem.style.display = 'block';
        pengaturanTitle.style.display = 'block';
    } else {
        // Hide profil menu and pengaturan title on other pages
        profilMenuItem.style.display = 'none';
        pengaturanTitle.style.display = 'none';
    }
}

// Initialize on page load
initProfileMenuVisibility();

// Sidebar toggle
document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('active');
});
