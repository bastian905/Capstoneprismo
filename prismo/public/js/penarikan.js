// penarikan.js

// klik kembali
function goBack(){ history.back(); }

// Use real data from server - Load from backend
let mockPayouts = [];

// Load withdrawals data from server
if (window.withdrawalsData && Array.isArray(window.withdrawalsData)) {
  mockPayouts = window.withdrawalsData.map(w => ({
    id: w.id,
    name: w.mitra.name || w.mitra_name || '-',  // Nama tempat (business_name)
    owner: w.mitra.owner || w.account_name || '-',  // Pemilik (name)
    email: w.mitra.email || w.mitra_email || '-',
    contact: w.mitra.phone || '-',  // Nomor telepon
    location: w.mitra.address || '-',  // Kota/Kabupaten
    amount: Number(w.amount).toLocaleString('id-ID'),
    qrisSrc: w.qris_image ? `/storage/${w.qris_image}` : '/images/qris-placeholder.png',
    status: w.status,
    bankName: w.bank_name || '-',
    accountNumber: w.account_number || '-',
    accountName: w.account_name || '-'
  }));
}

const tbody = document.getElementById('payoutList');

// render rows dari data
function renderRows(){
  tbody.innerHTML = '';
  
  if (mockPayouts.length === 0) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td colspan="7" style="text-align: center; padding: 2rem;">
        <p style="color: var(--gray-500); font-style: italic;">
          Tidak ada penarikan saldo yang menunggu persetujuan.
        </p>
      </td>
    `;
    tbody.appendChild(tr);
    return;
  }
  
  mockPayouts.forEach((p, idx) => {
    const tr = document.createElement('tr');
    tr.dataset.id = p.id;

    tr.innerHTML = `
      <td class="col-no">${idx+1}</td>
      <td class="col-name">${escapeHtml(p.name)}</td>
      <td class="col-owner">
        <span class="owner-name">${escapeHtml(p.owner)}</span>
        <span class="owner-email">${escapeHtml(p.email)}</span>
      </td>
      <td class="col-contact">${escapeHtml(p.contact)}</td>
      <td class="col-lokasi">${escapeHtml(p.location)}</td>
      <td class="col-jumlah"><span class="amount">Rp<br>${escapeHtml(p.amount)}</span></td>
      <td class="col-status">
        <div class="actions" style="justify-content:flex-end">
          <button class="pill qris" data-action="qris">Details</button>
        </div>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// safe text
function escapeHtml(text){
  if(text == null) return '';
  return String(text).replace(/[&<>"']/g, function(m){ 
    return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]; 
  });
}

// initial render
renderRows();

// modal controls
const qrisModal = document.getElementById('qrisModal');
const qrisImage = document.getElementById('qrisImage');
const qrisClose = document.getElementById('qrisClose');
const qrisApprove = document.getElementById('qrisApprove');

let activeIndex = null; // index dalam mockPayouts yang sedang diproses

// event delegation: tombol Details di tabel
tbody.addEventListener('click', function(e){
  const btn = e.target.closest('button[data-action="qris"]');
  if(!btn) return;
  const row = btn.closest('tr');
  if(!row) return;
  const id = row.dataset.id;
  activeIndex = mockPayouts.findIndex(x => String(x.id) === String(id));
  if(activeIndex === -1) return;

  // isi QR image dengan path yang sesuai dari data
  const src = mockPayouts[activeIndex].qrisSrc;
  qrisImage.src = src;
  qrisImage.alt = `QRIS untuk ${mockPayouts[activeIndex].name}`;

  showModal(qrisModal);
});

// tombol tutup
qrisClose.addEventListener('click', () => {
  hideModal(qrisModal);
  activeIndex = null;
});

// tombol Selesai di dalam modal: update status ke completed via API
qrisApprove.addEventListener('click', async () => {
  if(activeIndex == null) { 
    hideModal(qrisModal); 
    return; 
  }
  
  const withdrawalId = mockPayouts[activeIndex].id;
  
  // Simulasi loading
  const originalText = qrisApprove.textContent;
  qrisApprove.textContent = 'Memproses...';
  qrisApprove.disabled = true;
  
  try {
    // Call API to complete withdrawal
    const response = await fetch(`/api/withdrawals/${withdrawalId}/complete`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
      }
    });
    
    if (!response.ok) {
      const errorData = await response.json();
      throw new Error(errorData.message || 'Gagal menyelesaikan penarikan');
    }
    
    // hapus dari array
    mockPayouts.splice(activeIndex, 1);
    // rerender tabel
    renderRows();
    // close modal
    hideModal(qrisModal);
    activeIndex = null;
    
    // Reset tombol
    qrisApprove.textContent = originalText;
    qrisApprove.disabled = false;
    
    // Tampilkan notifikasi sukses
    showNotification('Penarikan saldo berhasil diselesaikan!');
    
  } catch (error) {
    console.error('Error completing withdrawal:', error);
    alert(error.message || 'Terjadi kesalahan saat menyelesaikan penarikan');
    
    // Reset tombol
    qrisApprove.textContent = originalText;
    qrisApprove.disabled = false;
  }
});

// helper show/hide modal
function showModal(el){
  el.classList.add('show');
  el.setAttribute('aria-hidden','false');
  document.body.style.overflow = 'hidden'; // Mencegah scroll di body
}

function hideModal(el){
  el.classList.remove('show');
  el.setAttribute('aria-hidden','true');
  document.body.style.overflow = ''; // Mengembalikan scroll di body
}

// klik di luar modal untuk tutup
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', function(e){
    if(e.target === m) hideModal(m);
  });
});

// ESC tutup semua modal
document.addEventListener('keydown', function(e){
  if(e.key === 'Escape'){
    document.querySelectorAll('.modal-overlay.show').forEach(m => hideModal(m));
  }
});

// Fungsi untuk menampilkan notifikasi
function showNotification(message) {
  const notification = document.createElement('div');
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: var(--success-500);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: var(--shadow-lg);
    z-index: 2000;
    font-weight: 600;
    animation: slideIn 0.3s ease-out;
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease-in';
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}

// Tambahkan style animasi untuk notifikasi
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
  }
  @keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
  }
`;
document.head.appendChild(style);