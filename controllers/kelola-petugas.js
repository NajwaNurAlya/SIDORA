// Controller: Kelola Petugas
// Dipakai di admin/kelola-petugas.html

const modal = document.getElementById('petugasModal');
const tambahBtn = document.getElementById('tambahPetugasBtn');
const closeBtn = document.getElementById('closeModal');
const cancelBtn = document.getElementById('cancelBtn');
const editBtns = document.querySelectorAll('.editBtn');

// Open modal for new petugas
tambahBtn.addEventListener('click', () => {
    document.getElementById('modalTitle').textContent = 'Tambah Petugas';
    document.getElementById('petugasForm').reset();
    modal.classList.add('active');
});

// Open modal for edit (demo)
editBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('modalTitle').textContent = 'Edit Petugas';
        modal.classList.add('active');
    });
});

// Close modal
closeBtn.addEventListener('click', () => modal.classList.remove('active'));
cancelBtn.addEventListener('click', () => modal.classList.remove('active'));

// Close modal when clicking outside
modal.addEventListener('click', (e) => {
    if (e.target === modal) modal.classList.remove('active');
});

// Form submission
document.getElementById('petugasForm').addEventListener('submit', (e) => {
    e.preventDefault();
    alert('Data petugas berhasil disimpan!');
    modal.classList.remove('active');
});

// Sidebar toggle
document.getElementById('sidebarToggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('active');
});
