document.addEventListener('DOMContentLoaded', function () {
    // Initialize file uploads
    initializeFileUploads();

    // Initialize form submission
    initializeFormSubmission();

    // Initialize dynamic required indicator
    initializeDynamicRequiredIndicator();

    // Load existing data (simulasi)
    loadExistingData();
});

function initializeFileUploads() {
    // Facility photos upload (max 5 images, JPG/PNG)
    const facilityUpload = document.getElementById('facilityUpload');
    const facilityPreview = document.getElementById('facilityPreview');

    if (facilityUpload) {
        facilityUpload.addEventListener('click', function () {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png';
            input.multiple = true;

            input.addEventListener('change', function (e) {
                handleFileUpload(e.target.files, facilityPreview, 5, ['image/jpeg', 'image/png'], facilityUpload);
            });

            input.click();
        });
    }
}

function handleFileUpload(files, previewContainer, maxFiles, allowedTypes, uploadButton) {
    if (!previewContainer) return;

    const currentFiles = previewContainer.querySelectorAll('.preview-item').length;
    const remainingSlots = maxFiles - currentFiles;

    if (files.length > remainingSlots) {
        showCustomAlert('error', [`Maksimal ${maxFiles} file yang diizinkan. Anda dapat mengupload ${remainingSlots} file lagi.`]);
        return;
    }

    for (let i = 0; i < Math.min(files.length, remainingSlots); i++) {
        const file = files[i];

        // Check file type
        if (!allowedTypes.includes(file.type)) {
            const allowedExtensions = allowedTypes.map(type => {
                if (type === 'image/jpeg') return 'JPG';
                if (type === 'image/png') return 'PNG';
                if (type === 'application/pdf') return 'PDF';
                return type;
            }).join(', ');

            showCustomAlert('error', [`File ${file.name} tidak diizinkan. Hanya file ${allowedExtensions} yang diterima.`]);
            continue;
        }

        // Check file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showCustomAlert('error', [`File ${file.name} terlalu besar. Maksimal 5MB per file.`]);
            continue;
        }

        const previewItem = document.createElement('div');
        previewItem.className = 'preview-item';

        // Format file size
        const fileSize = formatFileSize(file.size);

        // Determine icon based on file type for preview - GUNAKAN PATH IKON ANDA
        let fileIcon = '<img src="/images/pdf.png" alt="PDF" style="width: 24px; height: 24px; object-fit: contain;">';
        if (file.type.startsWith('image/')) {
            fileIcon = '<img src="/images/gambar.png" alt="Image" style="width: 24px; height: 24px; object-fit: contain;">';
        }

        previewItem.innerHTML = `
            <div class="preview-info">
                <div class="preview-icon">${fileIcon}</div>
                <div class="preview-details">
                    <div class="preview-filename">${file.name}</div>
                    <div class="preview-filesize">${fileSize}</div>
                </div>
            </div>
            <button type="button" class="remove-btn">×</button>
        `;

        // Add event listener to remove button
        const removeBtn = previewItem.querySelector('.remove-btn');
        removeBtn.addEventListener('click', function () {
            previewItem.remove();

            // Update bintang saat file dihapus
            updateFileUploadIndicator(previewContainer);

            // Show upload button again when file is removed
            if (uploadButton) {
                const currentCount = previewContainer.querySelectorAll('.preview-item').length;
                if (currentCount < maxFiles) {
                    uploadButton.style.display = 'block';
                }
            }
        });

        previewContainer.appendChild(previewItem);

        // Update bintang saat file ditambahkan
        updateFileUploadIndicator(previewContainer);

        // Hide upload button logic
        if (uploadButton) {
            const currentCount = previewContainer.querySelectorAll('.preview-item').length;
            if (currentCount >= maxFiles) {
                uploadButton.style.display = 'none';
            } else {
                uploadButton.style.display = 'block';
            }
        }
    }
}

// Fungsi untuk update indikator bintang pada upload file
function updateFileUploadIndicator(previewContainer) {
    const formGroup = previewContainer.closest('.form-group');
    if (!formGroup) return;

    const hasFiles = previewContainer.querySelectorAll('.preview-item').length > 0;
    
    if (hasFiles) {
        formGroup.classList.add('filled');
    } else {
        formGroup.classList.remove('filled');
    }
}

// Helper function to format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function initializeFormSubmission() {
    const form = document.getElementById('editProfileForm');
    const saveBtn = document.querySelector('.btn-save');

    if (form && saveBtn) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validasi semua field yang wajib diisi
            let isValid = true;
            const missingFields = [];

            // List semua field yang wajib diisi
            const requiredFields = [
                { id: 'businessName', name: 'Nama Bisnis' },
                { id: 'address', name: 'Alamat Lengkap' },
                { id: 'city', name: 'Kota/Kabupaten' },
                { id: 'postalCode', name: 'Kode Pos' },
                { id: 'mapLocation', name: 'Lokasi di Peta' },
                { id: 'contactPerson', name: 'Nama Penanggung Jawab' },
                { id: 'email', name: 'Email' },
                { id: 'phone', name: 'Nomor WhatsApp/Telepon' }
            ];

            // Cek semua field wajib
            requiredFields.forEach(field => {
                const input = document.getElementById(field.id);
                if (input && !input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#ef4444';
                    missingFields.push(field.name);
                } else if (input) {
                    input.style.borderColor = '';
                }
            });

            // Validasi foto fasilitas
            const facilityPreview = document.getElementById('facilityPreview');
            if (!facilityPreview || facilityPreview.children.length === 0) {
                isValid = false;
                missingFields.push('Foto Fasilitas');
            }

            // Tampilkan pesan error jika ada field yang belum lengkap
            if (!isValid) {
                showCustomAlert('error', missingFields);
                
                // Scroll ke field pertama yang error
                const firstErrorField = this.querySelector('input[style*="border-color: rgb(239, 68, 68)"]');
                if (firstErrorField) {
                    firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            // Jika semua validasi lolos, simpan perubahan
            const originalText = saveBtn.textContent;
            saveBtn.textContent = 'Menyimpan...';
            saveBtn.disabled = true;

            // Simulate API call
            setTimeout(() => {
                showCustomAlert('success');
                saveBtn.textContent = originalText;
                saveBtn.disabled = false;
            }, 1500);
        });
    }
}

// Fungsi untuk menampilkan custom alert
function showCustomAlert(type, missingFields = []) {
    // Hapus alert sebelumnya jika ada
    const existingAlert = document.querySelector('.custom-alert-overlay');
    if (existingAlert) {
        existingAlert.remove();
    }

    // Buat overlay
    const overlay = document.createElement('div');
    overlay.className = 'custom-alert-overlay';

    let modalHTML = '';

    if (type === 'error') {
        modalHTML = `
            <div class="custom-alert-modal">
                <div class="alert-header">
                    <div class="alert-icon">⚠️</div>
                    <div class="alert-header-text">
                        <h3>Data Belum Lengkap</h3>
                        <p>Harap lengkapi semua field yang wajib diisi</p>
                    </div>
                </div>
                <div class="alert-body">
                    <p class="alert-message">Terdapat <strong>${missingFields.length} field</strong> yang belum diisi. Mohon lengkapi data berikut:</p>
                    <div class="missing-fields-list">
                        <h4>📋 Field yang belum diisi:</h4>
                        <ul>
                            ${missingFields.map(field => `<li>${field}</li>`).join('')}
                        </ul>
                    </div>
                </div>
                <div class="alert-footer">
                    <button class="alert-btn alert-btn-primary" onclick="closeCustomAlert()">Mengerti</button>
                </div>
            </div>
        `;
    } else if (type === 'success') {
        modalHTML = `
            <div class="custom-alert-modal">
                <div class="alert-header success">
                    <div class="alert-header-text">
                        <h3>Berhasil Disimpan!</h3>
                    </div>
                </div>
                <div class="alert-body success">
                    <div class="success-animation">
                    </div>
                    <div class="success-content">
                        <h3 class="success-title">Data Tersimpan!</h3>
                        <p class="success-message">Perubahan profil Anda telah berhasil diperbarui dan tersimpan ke sistem.</p>
                    </div>
                </div>
                <div class="alert-footer success">
                    <button class="alert-btn alert-btn-success" onclick="redirectToProfile()">
                        Mengerti
                    </button>
                </div>
            </div>
        `;
    }

    overlay.innerHTML = modalHTML;
    document.body.appendChild(overlay);

    // Tutup saat klik overlay (hanya untuk error alert)
    if (type === 'error') {
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeCustomAlert();
            }
        });
    }
}

// Fungsi untuk redirect ke halaman profile
function redirectToProfile() {
    window.location.href = '/mitra/profil/profil';
}

// Fungsi untuk menutup custom alert
function closeCustomAlert() {
    const overlay = document.querySelector('.custom-alert-overlay');
    if (overlay) {
        overlay.style.animation = 'fadeOut 0.3s forwards';
        setTimeout(() => {
            overlay.remove();
        }, 300);
    }
}

// Tambahkan animasi fadeOut
if (!document.getElementById('custom-alert-animations')) {
    const style = document.createElement('style');
    style.id = 'custom-alert-animations';
    style.textContent = `
        @keyframes fadeOut {
            to {
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

// Fungsi untuk inisialisasi indikator bintang dinamis
function initializeDynamicRequiredIndicator() {
    // Daftar ID field yang wajib diisi
    const requiredFieldIds = [
        'businessName',
        'address',
        'city',
        'postalCode',
        'mapLocation',
        'contactPerson',
        'email',
        'phone'
    ];

    // Tambahkan listener untuk setiap field wajib
    requiredFieldIds.forEach(fieldId => {
        const input = document.getElementById(fieldId);
        if (!input) return;

        const formGroup = input.closest('.form-group');
        if (!formGroup) return;

        const label = formGroup.querySelector('label');
        if (!label) return;

        // Pastikan label memiliki class required
        if (!label.classList.contains('required')) {
            label.classList.add('required');
        }

        // Set initial state
        updateRequiredIndicator(input);

        // Add event listeners
        input.addEventListener('input', function() {
            updateRequiredIndicator(this);
        });

        input.addEventListener('change', function() {
            updateRequiredIndicator(this);
        });

        input.addEventListener('blur', function() {
            updateRequiredIndicator(this);
        });
    });

    // Initialize untuk file uploads
    const facilityPreview = document.getElementById('facilityPreview');
    if (facilityPreview) {
        updateFileUploadIndicator(facilityPreview);
    }
}

// Fungsi untuk update indikator required berdasarkan nilai input
function updateRequiredIndicator(input) {
    const formGroup = input.closest('.form-group');
    if (!formGroup) return;
    
    const value = input.value.trim();
    
    if (value !== '') {
        formGroup.classList.add('filled');
        input.style.borderColor = '';
    } else {
        formGroup.classList.remove('filled');
    }
}

// Fungsi untuk load data yang sudah ada (simulasi)
function loadExistingData() {
    // Simulasi data yang sudah ada
    const existingData = {
        businessName: 'Quick Clean Steam',
        address: 'Jl. Sudirman No. 45, Jakarta Pusat',
        city: 'Jakarta Pusat',
        postalCode: '10220',
        mapLocation: 'https://www.google.com/maps/place/example',
        contactPerson: 'Budi Santoso',
        email: 'budi@quickclean.com',
        phone: '081234567890'
    };

    // Isi form dengan data yang ada
    Object.keys(existingData).forEach(key => {
        const input = document.getElementById(key);
        if (input) {
            input.value = existingData[key];
            // Update required indicator
            updateRequiredIndicator(input);
        }
    });

    // Simulasi foto fasilitas yang sudah ada
    const facilityPreview = document.getElementById('facilityPreview');
    if (facilityPreview) {
        // Contoh menambahkan 2 foto existing
        for (let i = 1; i <= 2; i++) {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            
            previewItem.innerHTML = `
                <div class="preview-info">
                    <div class="preview-icon">
                        <img src="/images/gambar.png" alt="Image" style="width: 24px; height: 24px; object-fit: contain;">
                    </div>
                    <div class="preview-details">
                        <div class="preview-filename">fasilitas-${i}.jpg</div>
                        <div class="preview-filesize">2.5 MB</div>
                    </div>
                </div>
                <button type="button" class="remove-btn">×</button>
            `;

            const removeBtn = previewItem.querySelector('.remove-btn');
            removeBtn.addEventListener('click', function () {
                previewItem.remove();
                updateFileUploadIndicator(facilityPreview);
                
                const facilityUpload = document.getElementById('facilityUpload');
                const currentCount = facilityPreview.querySelectorAll('.preview-item').length;
                if (currentCount < 5 && facilityUpload) {
                    facilityUpload.style.display = 'block';
                }
            });

            facilityPreview.appendChild(previewItem);
        }
        
        updateFileUploadIndicator(facilityPreview);
    }
}