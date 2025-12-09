// simple interactions for the approval form

// modal elements
const modalSuccess = document.getElementById('modalSuccess');
const modalReject = document.getElementById('modalReject');
const modalViewer = document.getElementById('modalViewer');
const okSuccess = document.getElementById('okSuccess');
const btnApprove = document.getElementById('btnApprove');
const btnReject = document.getElementById('btnReject');
const confirmReject = document.getElementById('confirmReject');
const cancelReject = document.getElementById('cancelReject');
const successText = document.getElementById('successText');
const viewerClose = document.getElementById('viewerClose');
const viewerTitle = document.getElementById('viewerTitle');
const pdfViewer = document.getElementById('pdfViewer');
const imageViewer = document.getElementById('imageViewer');
const pdfFrame = document.getElementById('pdfFrame');
const imagePreview = document.getElementById('imagePreview');

// Current file being viewed
let currentFile = null;

// Sample file data (in real app, this would come from backend)
const fileData = {
  'fasilitas_1.jpg': 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&h=600&fit=crop',
  'fasilitas_2.jpg': 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
  'fasilitas_3.jpg': 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop',
  'dokumen_siup.pdf': 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
  'ktp_penanggung_jawab.jpg': 'https://images.unsplash.com/photo-1552058544-f2b08422138a?w=600&h=400&fit=crop',
  'qris_code.png': 'https://images.unsplash.com/photo-1556655673-33d2d3f9c7c6?w=400&h=400&fit=crop'
};

// Initialize event listeners
function init() {
  // Approval flow
  if (btnApprove) {
    btnApprove.addEventListener('click', handleApprove);
  }

  if (btnReject) {
    btnReject.addEventListener('click', () => showModal(modalReject));
  }

  if (confirmReject) {
    confirmReject.addEventListener('click', handleReject);
  }

  if (cancelReject) {
    cancelReject.addEventListener('click', () => {
      closeModal(modalReject);
      // Reset textarea and error
      document.getElementById('rejectReason').value = '';
      document.getElementById('rejectReasonError').style.display = 'none';
    });
  }
  
  // Reset error when typing
  const rejectReasonTextarea = document.getElementById('rejectReason');
  if (rejectReasonTextarea) {
    rejectReasonTextarea.addEventListener('input', () => {
      if (rejectReasonTextarea.value.trim().length >= 10) {
        document.getElementById('rejectReasonError').style.display = 'none';
      }
    });
  }

  if (okSuccess) {
    okSuccess.addEventListener('click', () => {
      closeModal(modalSuccess);
      // Redirect to kelolamitra list
      window.location.href = '/admin/kelolamitra/kelolamitra';
    });
  }

  if (viewerClose) {
    viewerClose.addEventListener('click', () => closeModal(modalViewer));
  }

  // File viewer handlers
  setupFileViewers();
  
  // Modal close handlers
  setupModalCloseHandlers();
}

// Setup file viewer buttons
function setupFileViewers() {
  // Document viewers
  document.querySelectorAll('.icon-eye[data-type][data-src]').forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      const type = button.getAttribute('data-type');
      const src = button.getAttribute('data-src');
      const title = button.closest('.doc-item') ? 
        button.closest('.doc-item').querySelector('.doc-title').textContent :
        button.closest('li').querySelector('.file-left').textContent;
      
      openViewer(type, src, title);
    });
  });
}

// Open file viewer
function openViewer(type, filename, title) {
  currentFile = { type, filename, title };
  viewerTitle.textContent = title || 'Preview Dokumen';
  
  // Hide all viewers first
  pdfViewer.style.display = 'none';
  imageViewer.style.display = 'none';
  
  // Show appropriate viewer
  if (type === 'pdf') {
    const pdfUrl = fileData[filename] || `https://docs.google.com/gview?url=${encodeURIComponent(filename)}&embedded=true`;
    pdfFrame.src = pdfUrl;
    pdfViewer.style.display = 'flex';
  } else if (type === 'image') {
    const imageUrl = fileData[filename] || filename;
    imagePreview.src = imageUrl;
    imagePreview.alt = title || 'Preview Gambar';
    imageViewer.style.display = 'flex';
  }
  
  showModal(modalViewer);
}

// Download current file
function downloadFile() {
  if (!currentFile) return;
  
  const url = fileData[currentFile.filename] || currentFile.filename;
  const link = document.createElement('a');
  link.href = url;
  link.download = currentFile.filename;
  link.target = '_blank';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// Handle approve action
function handleApprove(e) {
  e.preventDefault();
  const mitraId = btnApprove.getAttribute('data-id');
  
  // Disable button immediately
  btnApprove.disabled = true;
  btnApprove.textContent = 'Memproses...';
  
  // Send AJAX request
  fetch(`/admin/kelolamitra/${mitraId}/approve`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      successText.textContent = data.message || 'Mitra telah disetujui.';
      showModal(modalSuccess);
      disableButtons();
    } else {
      alert('Terjadi kesalahan saat menyetujui mitra');
      btnApprove.disabled = false;
      btnApprove.textContent = 'Setujui';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Terjadi kesalahan saat menyetujui mitra');
    btnApprove.disabled = false;
    btnApprove.textContent = 'Setujui';
  });
}

// Handle reject action
function handleReject() {
  const mitraId = btnReject.getAttribute('data-id');
  const rejectReason = document.getElementById('rejectReason').value.trim();
  const rejectReasonError = document.getElementById('rejectReasonError');
  
  // Validate reason
  if (rejectReason.length < 10) {
    rejectReasonError.style.display = 'block';
    return;
  }
  
  rejectReasonError.style.display = 'none';
  
  // Disable button immediately
  confirmReject.disabled = true;
  confirmReject.textContent = 'Memproses...';
  
  // Send AJAX request
  fetch(`/admin/kelolamitra/${mitraId}/reject`, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    },
    body: JSON.stringify({
      reject_reason: rejectReason
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      successText.textContent = data.message || 'Mitra telah ditolak dan email notifikasi telah dikirim.';
      showModal(modalSuccess);
      disableButtons();
      closeModal(modalReject);
    } else {
      alert('Terjadi kesalahan saat menolak mitra');
      confirmReject.disabled = false;
      confirmReject.textContent = 'Ya, Tolak';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Terjadi kesalahan saat menolak mitra');
    confirmReject.disabled = false;
    confirmReject.textContent = 'Ya, Tolak';
  });
}

// Disable action buttons after decision
function disableButtons() {
  btnApprove.disabled = true;
  btnReject.disabled = true;
  btnApprove.style.opacity = '0.6';
  btnReject.style.opacity = '0.6';
  btnApprove.style.cursor = 'not-allowed';
  btnReject.style.cursor = 'not-allowed';
}

// Modal close handlers
function setupModalCloseHandlers() {
  // Close modal when clicking overlay
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeModal(overlay);
    });
  });

  // Close with ESC key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeModal(modalSuccess);
      closeModal(modalReject);
      closeModal(modalViewer);
    }
  });
}

// Helper functions
function showModal(el) {
  if (!el) return;
  el.classList.add('show');
  document.body.style.overflow = 'hidden';
}

function closeModal(el) {
  if (!el) return;
  el.classList.remove('show');
  document.body.style.overflow = '';
  
  // Clear PDF frame when closing viewer to stop loading
  if (el === modalViewer) {
    pdfFrame.src = '';
  }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', init);