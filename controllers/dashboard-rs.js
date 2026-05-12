function initProfileMenuVisibility() {
    const currentPage = window.location.pathname.split('/').pop() || 'dashboard.html';
    const profilMenuItem = document.getElementById('profilMenuItem');
    const pengaturanTitle = document.getElementById('pengaturanTitle');
    
    if (currentPage === 'dashboard.html' || currentPage === '') {
        profilMenuItem.style.display = 'block';
        pengaturanTitle.style.display = 'block';
    } else {
        profilMenuItem.style.display = 'none';
        pengaturanTitle.style.display = 'none';
    }
}

initProfileMenuVisibility();

document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('active');
});