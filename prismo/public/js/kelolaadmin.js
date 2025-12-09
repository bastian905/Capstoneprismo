// kelola-admin.js (versi dibersihkan & aman)

// Use real data from server if available, otherwise use mock data
let admins = window.adminsData || [
  { id: 1, name: 'Bagas Agustian', email: 'budi@steamwash.com' },
  { id: 2, name: 'Naufal apabae', email: 'budi@steamwash.com' }
];

// elemen DOM (cek dulu apakah ada)
const adminList = document.getElementById('adminList');
const btnAddAdmin = document.getElementById('btnAddAdmin');
const modalAdd = document.getElementById('modalAdd');
const modalDelete = document.getElementById('modalDelete');

const inputName = document.getElementById('inputName');
const inputEmail = document.getElementById('inputEmail');
const inputPassword = document.getElementById('inputPassword');
const inputRole = document.getElementById('inputRole');
// togglePassword mungkin sudah dihapus dari HTML — hanya tambahkan listener kalau ada
const togglePassword = document.getElementById('togglePassword');

const btnCancelAdd = document.getElementById('btnCancelAdd');
const btnSaveAdmin = document.getElementById('btnSaveAdmin');

const confirmDeleteYes = document.getElementById('confirmDeleteYes');
const confirmDeleteNo = document.getElementById('confirmDeleteNo');

let deleteTargetId = null;

// safe text
function escapeHtml(text) {
  if (text == null) return '';
  return String(text).replace(/[&<>"']/g, function (m) {
    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
  });
}

// render tabel
function renderAdmins() {
  if (!adminList) return;
  adminList.innerHTML = '';
  admins.forEach((a, idx) => {
    const tr = document.createElement('tr');
    tr.dataset.id = a.id;
    tr.innerHTML = `
      <td class="col-no">${idx + 1}</td>
      <td>${escapeHtml(a.name)}</td>
      <td><span class="owner-email">${escapeHtml(a.email)}</span></td>
      <td style="text-align:center">
        <button class="action-btn small" data-action="delete" data-id="${a.id}">Hapus</button>
      </td>
    `;
    adminList.appendChild(tr);
  });
}

// open/close helpers (cek keberadaan dulu)
function openModal(el) { if (el) el.classList.add('show'); }
function closeModal(el) { if (el) el.classList.remove('show'); }

// Inisialisasi: tombol Tambah Admin
if (btnAddAdmin && modalAdd) {
  btnAddAdmin.addEventListener('click', () => {
    // reset form (jika elemen ada)
    if (inputName) inputName.value = '';
    if (inputEmail) inputEmail.value = '';
    if (inputPassword) inputPassword.value = '';
    if (inputRole) {
      inputRole.value = 'Admin'; // selalu Admin dan readonly
      inputRole.setAttribute('readonly', 'true');
    }
    openModal(modalAdd);
  });
}

// toggle password (jika tombol ada)
if (togglePassword && inputPassword) {
  togglePassword.addEventListener('click', () => {
    inputPassword.type = inputPassword.type === 'password' ? 'text' : 'password';
  });
}

// Cancel tambah
if (btnCancelAdd && modalAdd) {
  btnCancelAdd.addEventListener('click', () => {
    closeModal(modalAdd);
  });
}

// Simpan admin baru
if (btnSaveAdmin && modalAdd) {
  btnSaveAdmin.addEventListener('click', () => {
    const name = inputName ? inputName.value.trim() : '';
    const email = inputEmail ? inputEmail.value.trim() : '';

    if (!name || !email) {
      alert('Nama dan email harus diisi.');
      return;
    }

    // id sederhana
    const newId = admins.length ? (Math.max(...admins.map(a => a.id)) + 1) : 1;
    admins.push({ id: newId, name, email });

    renderAdmins();
    closeModal(modalAdd);
  });
}

// Event delegation: tombol Hapus di tabel
if (adminList && modalDelete) {
  adminList.addEventListener('click', (e) => {
    const btn = e.target.closest('button[data-action="delete"]');
    if (!btn) return;
    const id = Number(btn.dataset.id);
    deleteTargetId = id;
    openModal(modalDelete);
  });
}

// Konfirmasi hapus
if (confirmDeleteYes && modalDelete) {
  confirmDeleteYes.addEventListener('click', () => {
    if (deleteTargetId == null) return;
    admins = admins.filter(a => a.id !== deleteTargetId);
    deleteTargetId = null;
    renderAdmins();
    closeModal(modalDelete);
  });
}

// Batal hapus
if (confirmDeleteNo && modalDelete) {
  confirmDeleteNo.addEventListener('click', () => {
    deleteTargetId = null;
    closeModal(modalDelete);
  });
}

// Close modal ketika klik luar
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', (e) => {
    if (e.target === m) closeModal(m);
  });
});

// ESC tutup semua modal
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay.show').forEach(m => closeModal(m));
  }
});

// initial render
renderAdmins();