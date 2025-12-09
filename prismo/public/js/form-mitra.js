document.addEventListener('DOMContentLoaded', function () {
    // Initialize counters
    initializeCounters();

    // Initialize form submission
    initializeFormSubmission();

    // Initialize add service functionality
    initializeServiceManagement();

    // Initialize file uploads
    initializeFileUploads();

    // Initialize dynamic required indicator
    initializeDynamicRequiredIndicator();

    // Initialize logout functionality
    initializeLogout();

    // Initialize phone number validation
    initializePhoneValidation();

    // Initialize business name character limit
    initializeBusinessNameValidation();
});

function initializeCounters() {
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('decrease')) {
            const input = e.target.parentElement.querySelector('input');
            if (input.value > parseInt(input.min)) {
                input.value = parseInt(input.value) - 1;
            }
        }

        if (e.target.classList.contains('increase')) {
            const input = e.target.parentElement.querySelector('input');
            if (input.value < parseInt(input.max)) {
                input.value = parseInt(input.value) + 1;
            }
        }
    });
}

// FUNGSI BARU: Validasi maksimal 30 karakter untuk nama bisnis
function initializeBusinessNameValidation() {
    const businessNameInput = document.getElementById('businessName');
    if (businessNameInput) {
        // Tambahkan counter karakter
        const formGroup = businessNameInput.closest('.form-group');
        if (formGroup) {
            const charCounter = document.createElement('div');
            charCounter.className = 'char-counter';
            charCounter.style.fontSize = '12px';
            charCounter.style.color = '#6b7280';
            charCounter.style.marginTop = '4px';
            charCounter.style.textAlign = 'right';
            charCounter.textContent = '0/30 karakter';
            formGroup.appendChild(charCounter);

            // Update counter saat input
            businessNameInput.addEventListener('input', function(e) {
                const currentLength = this.value.length;
                charCounter.textContent = `${currentLength}/30 karakter`;
                
                if (currentLength > 30) {
                    charCounter.style.color = '#ef4444';
                    this.style.borderColor = '#ef4444';
                } else {
                    charCounter.style.color = '#6b7280';
                    this.style.borderColor = '';
                }
            });

            // Validasi paste event
            businessNameInput.addEventListener('paste', function(e) {
                const pastedData = e.clipboardData.getData('text');
                if (pastedData.length > 30) {
                    e.preventDefault();
                    showCustomAlert('error', [], 'Nama bisnis maksimal 30 karakter');
                }
            });

            // Validasi saat blur
            businessNameInput.addEventListener('blur', function() {
                if (this.value.length > 30) {
                    showCustomAlert('error', [], 'Nama bisnis maksimal 30 karakter');
                    // Potong teks yang berlebih
                    this.value = this.value.substring(0, 30);
                    charCounter.textContent = `30/30 karakter`;
                    charCounter.style.color = '#6b7280';
                    this.style.borderColor = '';
                }
            });

            // Set initial counter value
            charCounter.textContent = `${businessNameInput.value.length}/30 karakter`;
        }

        // Prevent input lebih dari 30 karakter
        businessNameInput.addEventListener('input', function(e) {
            if (this.value.length > 30) {
                this.value = this.value.substring(0, 30);
            }
        });
    }
}

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

        // Drag and drop functionality untuk facility photos
        facilityUpload.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.borderColor = '#2563eb';
            this.style.background = '#f0f7ff';
        });

        facilityUpload.addEventListener('dragleave', function (e) {
            e.preventDefault();
            this.style.borderColor = '#d1d5db';
            this.style.background = '#f9fafb';
        });

        facilityUpload.addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.borderColor = '#d1d5db';
            this.style.background = '#f9fafb';
            
            if (e.dataTransfer.files.length > 0) {
                handleFileUpload(e.dataTransfer.files, facilityPreview, 5, ['image/jpeg', 'image/png'], facilityUpload);
            }
        });
    }

    // Legal document upload (max 1 PDF)
    const legalDocUpload = document.getElementById('legalDocUpload');
    const legalDocPreview = document.getElementById('legalDocPreview');

    if (legalDocUpload) {
        legalDocUpload.addEventListener('click', function () {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = '.pdf';

            input.addEventListener('change', function (e) {
                handleFileUpload(e.target.files, legalDocPreview, 1, ['application/pdf'], legalDocUpload);
            });

            input.click();
        });
    }

    // KTP upload (max 1 JPG/PNG) - DIUBAH: tambah PNG
    const ktpUpload = document.getElementById('ktpUpload');
    const ktpPreview = document.getElementById('ktpPreview');

    if (ktpUpload) {
        ktpUpload.addEventListener('click', function () {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png'; // DIUBAH: tambah PNG

            input.addEventListener('change', function (e) {
                handleFileUpload(e.target.files, ktpPreview, 1, ['image/jpeg', 'image/png'], ktpUpload); // DIUBAH: tambah PNG
            });

            input.click();
        });
    }

    // QRIS upload (max 1 JPG/PNG) - DIUBAH: tambah PNG
    const qrisUpload = document.getElementById('qrisUpload');
    const qrisPreview = document.getElementById('qrisPreview');

    if (qrisUpload) {
        qrisUpload.addEventListener('click', function () {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png'; // DIUBAH: tambah PNG

            input.addEventListener('change', function (e) {
                handleFileUpload(e.target.files, qrisPreview, 1, ['image/jpeg', 'image/png'], qrisUpload); // DIUBAH: tambah PNG
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
        showCustomAlert('error', [], `Maksimal ${maxFiles} file yang diizinkan. Anda dapat mengupload ${remainingSlots} file lagi.`);
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

            showCustomAlert('error', [], `File ${file.name} tidak diizinkan. Hanya file ${allowedExtensions} yang diterima.`);
            continue;
        }

        // Check file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            showCustomAlert('error', [], `File ${file.name} terlalu besar. Maksimal 5MB per file.`);
            continue;
        }

        // Read file as base64
        const reader = new FileReader();
        reader.onload = function(e) {
            const base64Data = e.target.result;
            
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';

            // Format file size
            const fileSize = formatFileSize(file.size);

            // Store base64 data
            if (file.type.startsWith('image/')) {
                // For images, store as img element
                previewItem.innerHTML = `
                    <div class="preview-info">
                        <div class="preview-icon"><img src="/images/gambar.png" alt="Image" style="width: 24px; height: 24px; object-fit: contain;"></div>
                        <div class="preview-details">
                            <div class="preview-filename">${file.name}</div>
                            <div class="preview-filesize">${fileSize}</div>
                        </div>
                    </div>
                    <button type="button" class="remove-btn">×</button>
                    <img src="${base64Data}" style="display:none;" data-file-data="${base64Data}">
                `;
            } else {
                // For PDF, store in data attribute
                previewItem.innerHTML = `
                    <div class="preview-info">
                        <div class="preview-icon"><img src="/images/pdf.png" alt="PDF" style="width: 24px; height: 24px; object-fit: contain;"></div>
                        <div class="preview-details">
                            <div class="preview-filename">${file.name}</div>
                            <div class="preview-filesize">${fileSize}</div>
                        </div>
                    </div>
                    <button type="button" class="remove-btn">×</button>
                    <div style="display:none;" data-file-data="${base64Data}"></div>
                `;
            }

            // Add event listener to remove button
            const removeBtn = previewItem.querySelector('.remove-btn');
            removeBtn.addEventListener('click', function () {
                previewItem.remove();

                // Update bintang saat file dihapus
                updateFileUploadIndicator(previewContainer);

                // Show upload button again when file is removed
                if (uploadButton) {
                    if (maxFiles === 1) {
                        uploadButton.style.display = 'block';
                    } else if (maxFiles > 1) {
                        const currentCount = previewContainer.querySelectorAll('.preview-item').length;
                        if (currentCount < maxFiles) {
                            uploadButton.style.display = 'block';
                        }
                    }
                }
            });

            previewContainer.appendChild(previewItem);

            // Update bintang saat file ditambahkan
            updateFileUploadIndicator(previewContainer);

            // Hide upload button logic
            if (uploadButton) {
                const currentCount = previewContainer.querySelectorAll('.preview-item').length;

                if (maxFiles === 1 && currentCount > 0) {
                    uploadButton.style.display = 'none';
                } else if (maxFiles > 1) {
                    if (currentCount >= maxFiles) {
                        uploadButton.style.display = 'none';
                    } else {
                        uploadButton.style.display = 'block';
                    }
                }
            }
        };
        
        reader.readAsDataURL(file);
    }
}

// Fungsi baru untuk update indikator bintang pada upload file
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

// FUNGSI BARU: Validasi nomor telepon hanya angka
function initializePhoneValidation() {
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            // Hanya izinkan input angka
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Validasi paste event
        phoneInput.addEventListener('paste', function(e) {
            const pastedData = e.clipboardData.getData('text');
            if (!/^\d+$/.test(pastedData)) {
                e.preventDefault();
                showCustomAlert('error', [], 'Nomor WhatsApp hanya boleh berisi angka');
            }
        });

        // Validasi saat blur
        phoneInput.addEventListener('blur', function() {
            if (this.value && !/^\d+$/.test(this.value)) {
                this.value = this.value.replace(/[^0-9]/g, '');
                showCustomAlert('error', [], 'Nomor WhatsApp hanya boleh berisi angka');
            }
        });
    }
}

function initializeServiceManagement() {
    const addServiceBtn = document.getElementById('addService');
    const servicesContainer = document.getElementById('servicesContainer');

    // Jika elemen service management tidak ada, skip initialization
    if (!addServiceBtn || !servicesContainer) {
        return;
    }

    addServiceBtn.addEventListener('click', function () {
        const serviceCount = servicesContainer.querySelectorAll('.service-item').length;

        if (serviceCount >= 5) {
            showCustomAlert('error', [], 'Maksimal 5 layanan yang dapat ditambahkan');
            return;
        }

        const newService = document.createElement('div');
        newService.className = 'service-item';
        newService.innerHTML = `
            <div class="form-group">
                <label class="required">Nama Layanan</label>
                <input type="text" name="serviceName" placeholder="Nama Layanan" required>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="required">Harga</label>
                        <input type="number" name="servicePrice" placeholder="50000" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label>Jumlah slot harian</label>
                        <div class="counter">
                            <button type="button" class="counter-btn decrease">-</button>
                            <input type="number" name="serviceSlots" value="5" min="1" max="20">
                            <button type="button" class="counter-btn increase">+</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="required">Deskripsi Singkat</label>
                <textarea name="serviceDesc" rows="2" placeholder="Deskripsi layanan" required></textarea>
            </div>
            
            <button type="button" class="btn btn-danger remove-service">Hapus Layanan</button>
        `;

        servicesContainer.appendChild(newService);

        // Add event listener to remove button
        newService.querySelector('.remove-service').addEventListener('click', function () {
            if (servicesContainer.querySelectorAll('.service-item').length > 1) {
                newService.remove();
            } else {
                showCustomAlert('error', [], 'Minimal harus ada satu layanan');
            }
        });

        // Initialize dynamic required indicator for new service inputs
        initializeInputListenersForService(newService);
    });

    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-service').forEach(btn => {
        btn.addEventListener('click', function () {
            const servicesContainer = this.closest('#servicesContainer');
            if (servicesContainer && servicesContainer.querySelectorAll('.service-item').length > 1) {
                this.closest('.service-item').remove();
            } else {
                showCustomAlert('error', [], 'Minimal harus ada satu layanan');
            }
        });
    });

    // Initialize dynamic required indicator for existing service inputs
    document.querySelectorAll('.service-item').forEach(service => {
        initializeInputListenersForService(service);
    });
}

function initializeFormSubmission() {
    const form = document.getElementById('registrationForm');
    const submitBtn = document.querySelector('.btn-submit');

    if (form && submitBtn) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Validasi semua field yang wajib diisi
            let isValid = true;
            const missingFields = [];

            // List semua field yang wajib diisi
            const requiredFields = [
                { id: 'businessName', name: 'Nama Bisnis' },
                { id: 'establishmentYear', name: 'Tahun Berdiri' },
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

            // Validasi khusus untuk nama bisnis (maksimal 30 karakter)
            const businessNameInput = document.getElementById('businessName');
            if (businessNameInput && businessNameInput.value.length > 30) {
                isValid = false;
                businessNameInput.style.borderColor = '#ef4444';
                showCustomAlert('error', [], 'Nama bisnis maksimal 30 karakter');
                return;
            }

            // Validasi file uploads
            const facilityPreview = document.getElementById('facilityPreview');
            const legalDocPreview = document.getElementById('legalDocPreview');
            const ktpPreview = document.getElementById('ktpPreview');
            const qrisPreview = document.getElementById('qrisPreview');

            if (!facilityPreview || facilityPreview.children.length === 0) {
                isValid = false;
                missingFields.push('Foto Fasilitas');
            }

            if (!legalDocPreview || legalDocPreview.children.length === 0) {
                isValid = false;
                missingFields.push('SIUP/TDP/NIB');
            }

            if (!ktpPreview || ktpPreview.children.length === 0) {
                isValid = false;
                missingFields.push('KTP Penanggung Jawab');
            }

            if (!qrisPreview || qrisPreview.children.length === 0) {
                isValid = false;
                missingFields.push('QRIS');
            }

            // Validasi checkbox persetujuan
            const agreeTerms = document.getElementById('agreeTerms');
            if (!agreeTerms || !agreeTerms.checked) {
                isValid = false;
                missingFields.push('Persetujuan Syarat & Ketentuan');
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

            // Jika semua validasi lolos, proses pendaftaran
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Memproses...';
            submitBtn.disabled = true;

            // Collect all form data
            const formData = {
                businessName: document.getElementById('businessName').value,
                establishmentYear: document.getElementById('establishmentYear').value,
                address: document.getElementById('address').value,
                province: document.getElementById('province').value,
                city: document.getElementById('city').value,
                postalCode: document.getElementById('postalCode').value,
                mapLocation: document.getElementById('mapLocation').value,
                contactPerson: document.getElementById('contactPerson').value,
                phone: document.getElementById('phone').value,
                facilityPhotos: [],
                legalDoc: null,
                ktpPhoto: null,
                qrisPhoto: null
            };

            // Collect facility photos (base64)
            const facilityItems = facilityPreview.querySelectorAll('.preview-item');
            facilityItems.forEach(item => {
                const imgData = item.querySelector('[data-file-data]');
                if (imgData) {
                    formData.facilityPhotos.push(imgData.getAttribute('data-file-data'));
                }
            });

            // Get legal doc (base64)
            const legalDocItem = legalDocPreview.querySelector('[data-file-data]');
            if (legalDocItem) {
                formData.legalDoc = legalDocItem.getAttribute('data-file-data');
            }

            // Get KTP photo (base64)
            const ktpItem = ktpPreview.querySelector('[data-file-data]');
            if (ktpItem) {
                formData.ktpPhoto = ktpItem.getAttribute('data-file-data');
            }

            // Get QRIS photo (base64)
            const qrisItem = qrisPreview.querySelector('[data-file-data]');
            if (qrisItem) {
                formData.qrisPhoto = qrisItem.getAttribute('data-file-data');
            }

            // Send data to server
            fetch('/mitra/form-mitra', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCustomAlert('success', [], data.message, true); // redirect to pending page
                } else {
                    showCustomAlert('error', [], data.message || 'Terjadi kesalahan saat mengirim data');
                }
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomAlert('error', [], 'Terjadi kesalahan koneksi. Silakan coba lagi.');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
}

// Fungsi untuk inisialisasi logout
function initializeLogout() {
    const logoutBtn = document.querySelector('.btn-logout');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            handleLogout();
        });
    }
}

// Fungsi handleLogout yang sebelumnya missing
function handleLogout() {
    showCustomAlert('success', [], 'Logout berhasil!', false, true);
}

// Fungsi untuk menampilkan custom alert
function showCustomAlert(type, missingFields = [], customMessage = '', shouldRedirectToPending = false, isLogout = false) {
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
                        <h3>${customMessage || 'Data Belum Lengkap'}</h3>
                        <p>${customMessage ? '' : 'Harap lengkapi semua field yang wajib diisi'}</p>
                    </div>
                </div>
                ${missingFields.length > 0 ? `
                <div class="alert-body">
                    <p class="alert-message">Terdapat <strong>${missingFields.length} field</strong> yang belum diisi. Mohon lengkapi data berikut:</p>
                    <div class="missing-fields-list">
                        <h4>📋 Field yang belum diisi:</h4>
                        <ul>
                            ${missingFields.map(field => `<li>${field}</li>`).join('')}
                        </ul>
                    </div>
                </div>
                ` : ''}
                <div class="alert-footer">
                    <button class="alert-btn alert-btn-primary" onclick="closeCustomAlert()">Mengerti</button>
                </div>
            </div>
        `;
    } else if (type === 'success') {
        if (isLogout) {
            modalHTML = `
                <div class="custom-alert-modal">
                    <div class="alert-header success">
                        <div class="alert-icon">✓</div>
                        <div class="alert-header-text">
                            <h3>${customMessage || 'Logout Berhasil'}</h3>
                            <p>Anda akan diarahkan ke halaman login</p>
                        </div>
                    </div>
                    <div class="alert-body success">
                        <div class="success-icon-large">👋</div>
                        <p class="alert-message">Sampai jumpa kembali!</p>
                    </div>
                    <div class="alert-footer">
                        <button class="alert-btn alert-btn-primary" onclick="handleLogoutRedirect()">Tutup</button>
                    </div>
                </div>
            `;
        } else if (shouldRedirectToPending) {
            modalHTML = `
                <div class="custom-alert-modal">
                    <div class="alert-header success">
                        <div class="alert-icon">✓</div>
                        <div class="alert-header-text">
                            <h3>Pendaftaran Berhasil!</h3>
                            <p>Anda akan dialihkan...</p>
                        </div>
                    </div>
                    <div class="alert-body success">
                        <div class="success-icon-large">🎉</div>
                        <p class="alert-message">Terima kasih telah mendaftar sebagai Mitra Steam! Data Anda akan segera diproses oleh tim kami.</p>
                    </div>
                </div>
            `;
            // Auto redirect setelah 2 detik
            setTimeout(() => {
                window.location.href = '/mitra/form-mitra-pending';
            }, 2000);
        } else {
            modalHTML = `
                <div class="custom-alert-modal">
                    <div class="alert-header success">
                        <div class="alert-icon">✓</div>
                        <div class="alert-header-text">
                            <h3>${customMessage || 'Berhasil'}</h3>
                            <p>Operasi berhasil dilakukan</p>
                        </div>
                    </div>
                    <div class="alert-body success">
                        <div class="success-icon-large">✅</div>
                        <p class="alert-message">${customMessage || 'Operasi berhasil!'}</p>
                    </div>
                    <div class="alert-footer">
                        <button class="alert-btn alert-btn-primary" onclick="closeCustomAlert()">Tutup</button>
                    </div>
                </div>
            `;
        }
    }

    overlay.innerHTML = modalHTML;
    document.body.appendChild(overlay);

    // Tutup saat klik overlay
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            closeCustomAlert();
        }
    });

    // Auto redirect untuk logout setelah 2 detik
    if (isLogout) {
        setTimeout(() => {
            handleLogoutRedirect();
        }, 2000);
    }
}

// Fungsi untuk redirect ke halaman pending setelah pendaftaran berhasil
function handlePendingRedirect() {
    closeCustomAlert();
    setTimeout(() => {
        window.location.href = '/mitra/form-mitra-pending';
    }, 500);
}

// Fungsi untuk redirect ke halaman register setelah logout
function handleLogoutRedirect() {
    closeCustomAlert();
    setTimeout(() => {
        window.location.href = '/login';
    }, 500);
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

// Tambahkan animasi fadeOut di CSS (akan ditambahkan via style tag)
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

// Fungsi reset form
function resetForm() {
    const form = document.getElementById('registrationForm');

    // Reset form fields
    form.reset();

    // Clear file previews
    document.querySelectorAll('.upload-preview').forEach(preview => {
        if (preview) preview.innerHTML = '';
    });

    // Tampilkan kembali semua tombol upload
    const uploadButtons = [
        'facilityUpload',
        'legalDocUpload',
        'ktpUpload',
        'qrisUpload'
    ];

    uploadButtons.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.style.display = 'block';
        }
    });

    // Reset semua form-group ke state unfilled
    document.querySelectorAll('.form-group').forEach(group => {
        group.classList.remove('filled');
    });

    // Reset border color
    document.querySelectorAll('input, textarea').forEach(input => {
        input.style.borderColor = '';
    });

    // Reset counter karakter nama bisnis
    const businessNameInput = document.getElementById('businessName');
    const charCounter = businessNameInput?.closest('.form-group')?.querySelector('.char-counter');
    if (charCounter) {
        charCounter.textContent = '0/30 karakter';
        charCounter.style.color = '#6b7280';
    }

    // Reset service to only one (jika ada)
    const servicesContainer = document.getElementById('servicesContainer');
    if (servicesContainer) {
        servicesContainer.innerHTML = `
            <div class="service-item">
                <div class="form-group">
                    <label class="required">Nama Layanan</label>
                    <input type="text" name="serviceName" placeholder="Basic Steam" required>
                </div>
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="required">Harga</label>
                            <input type="number" name="servicePrice" placeholder="40000" required>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label>Jumlah slot harian</label>
                            <div class="counter">
                                <button type="button" class="counter-btn decrease">-</button>
                                <input type="number" name="serviceSlots" value="7" min="1" max="20">
                                <button type="button" class="counter-btn increase">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="required">Deskripsi Singkat</label>
                    <textarea name="serviceDesc" rows="2" placeholder="Cuci eksterior mobil dengan sabun khusus dan air bersih" required></textarea>
                </div>
                <button type="button" class="btn btn-danger remove-service">Hapus Layanan</button>
            </div>
        `;
    }

    // Re-initialize dynamic required indicator after reset
    setTimeout(() => {
        initializeDynamicRequiredIndicator();
        initializeServiceManagement();
        initializePhoneValidation(); // JANGAN LUPA re-initialize phone validation
        initializeBusinessNameValidation(); // JANGAN LUPA re-initialize business name validation
    }, 30);
}

// Fungsi untuk inisialisasi indikator bintang dinamis
function initializeDynamicRequiredIndicator() {
    // Daftar ID field yang wajib diisi
    const requiredFieldIds = [
        'businessName',
        'establishmentYear',
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
    const fileUploadGroups = [
        { preview: 'facilityPreview' },
        { preview: 'legalDocPreview' },
        { preview: 'ktpPreview' },
        { preview: 'qrisPreview' }
    ];

    fileUploadGroups.forEach(upload => {
        const previewElement = document.getElementById(upload.preview);
        if (previewElement) {
            updateFileUploadIndicator(previewElement);
        }
    });
}

// Fungsi untuk update indikator required berdasarkan nilai input
function updateRequiredIndicator(input) {
    const formGroup = input.closest('.form-group');
    if (!formGroup) return;
    
    const value = input.value.trim();
    
    if (value !== '') {
        formGroup.classList.add('filled');
        input.style.borderColor = ''; // Reset border color jika terisi
    } else {
        formGroup.classList.remove('filled');
    }
}

// Fungsi untuk initialize input listeners pada service items
function initializeInputListenersForService(serviceElement) {
    const requiredInputs = serviceElement.querySelectorAll('input[required], textarea[required]');
    
    requiredInputs.forEach(input => {
        const formGroup = input.closest('.form-group');
        if (formGroup) {
            const label = formGroup.querySelector('label');
            if (label && !label.classList.contains('required')) {
                label.classList.add('required');
            }
        }
        
        updateRequiredIndicator(input);
        
        input.addEventListener('input', function() {
            updateRequiredIndicator(this);
        });
        
        input.addEventListener('change', function() {
            updateRequiredIndicator(this);
        });
    });
}

// Tambahkan event listener untuk checkbox persetujuan
document.addEventListener('DOMContentLoaded', function() {
    const agreeTerms = document.getElementById('agreeTerms');
    if (agreeTerms) {
        agreeTerms.addEventListener('change', function() {
            const formGroup = this.closest('.form-group');
            if (formGroup) {
                if (this.checked) {
                    formGroup.classList.add('filled');
                } else {
                    formGroup.classList.remove('filled');
                }
            }
        });
    }
});