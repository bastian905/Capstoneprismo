// ===== PRISMO HISTORY MANAGER =====
class PrismoHistoryManager {
    constructor() {
        this.isInitialized = false;
        this.currentView = 'daily';
        this.currentDate = new Date().toISOString().split('T')[0];
        
        // Load real data from server - NO MORE MOCK DATA
        this.transactions = [];
        this.totalTransactions = 0;
        this.totalIncome = 0;
    }

    // ===== INITIALIZATION =====
    init() {
        if (this.isInitialized) return;

        try {
            this.setupEventListeners();
            this.setupMobileViewSelector();
            this.initializeView();
            this.isInitialized = true;

            console.log('PRISMO History Manager initialized');

        } catch (error) {
            console.error('Failed to initialize PRISMO History Manager:', error);
        }
    }
    
    async loadReportData(period = 'daily', date = null) {
        try {
            const targetDate = date || this.currentDate;
            const response = await fetch(`/mitra/reports/data?period=${period}&date=${targetDate}`);
            
            if (!response.ok) {
                throw new Error('Failed to load report data');
            }
            
            const data = await response.json();
            this.transactions = data.transactions || [];
            this.totalTransactions = data.totalTransactions || 0;
            this.totalIncome = data.totalIncome || 0;
            
            console.log('Loaded report data:', { period, count: this.transactions.length });
            
            // Update display
            this.updateSummary();
            
            return data;
        } catch (error) {
            console.error('Error loading report data:', error);
            this.transactions = [];
            this.totalTransactions = 0;
            this.totalIncome = 0;
        }
    }

    // ===== EVENT HANDLERS =====
    setupEventListeners() {
        document.addEventListener('click', (event) => {
            this.handleGlobalClick(event);
        });

        document.addEventListener('keydown', (event) => {
            this.handleGlobalKeydown(event);
        });
    }

    setupMobileViewSelector() {
        const viewDropdown = document.getElementById('viewDropdown');
        if (viewDropdown) {
            viewDropdown.addEventListener('change', (event) => {
                this.switchView(event.target.value);
            });
        }
    }

    initializeView() {
        // Set default date to today
        const filterDateInput = document.getElementById('filter-date');
        if (filterDateInput) {
            filterDateInput.value = this.currentDate;
        }

        // Set mobile dropdown value
        const viewDropdown = document.getElementById('viewDropdown');
        if (viewDropdown) {
            viewDropdown.value = this.currentView;
        }

        // Load initial data
        this.loadReportData(this.currentView, this.currentDate).then(() => {
            // Tampilkan data harian secara default
            this.showDailyView();
        });
    }

    handleGlobalClick(event) {
        const target = event.target;

        // Export dropdown
        if (target.matches('.export-btn') || target.closest('.export-btn')) {
            event.preventDefault();
            this.toggleExportDropdown();
            return;
        }

        if (target.matches('#export-pdf')) {
            event.preventDefault();
            this.exportPDF();
            this.closeExportDropdown();
            return;
        }

        if (target.matches('#export-excel')) {
            event.preventDefault();
            this.exportExcel();
            this.closeExportDropdown();
            return;
        }

        // View selector (desktop)
        if (target.matches('.view-btn')) {
            event.preventDefault();
            this.switchView(target.getAttribute('data-view'));
            return;
        }

        // Date filter
        if (target.matches('#filter-btn') || target.closest('#filter-btn')) {
            event.preventDefault();
            this.applyDateFilter();
            return;
        }

        // Close dropdowns when clicking outside
        if (!target.closest('.export-container')) {
            this.closeExportDropdown();
        }
    }

    handleGlobalKeydown(event) {
        if (event.key === 'Escape') {
            this.closeExportDropdown();
        }
    }

    // ===== VIEW MANAGEMENT =====
    switchView(view) {
        this.currentView = view;
        
        // Update active button (desktop)
        const viewBtns = document.querySelectorAll('.view-btn');
        viewBtns.forEach(btn => btn.classList.remove('active'));
        
        const activeBtn = document.querySelector(`.view-btn[data-view="${view}"]`);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        // Update mobile dropdown
        const viewDropdown = document.getElementById('viewDropdown');
        if (viewDropdown) {
            viewDropdown.value = view;
        }

        // Toggle date filter visibility
        this.toggleDateFilter(view);

        // Load data for new view
        this.loadReportData(view, this.currentDate).then(() => {
            // Hide all views
            const views = document.querySelectorAll('.laporan-view');
            views.forEach(v => v.style.display = 'none');

            // Show selected view
            if (view === 'daily') {
                this.showDailyView();
            } else if (view === 'weekly') {
                this.showWeeklyView();
            } else if (view === 'monthly') {
                this.showMonthlyView();
            } else if (view === 'yearly') {
                this.showYearlyView();
            }
        });
    }

    toggleDateFilter(view) {
        const dateFilter = document.getElementById('dateFilter');
        if (dateFilter) {
            // Tampilkan filter tanggal hanya untuk view harian
            if (view === 'daily') {
                dateFilter.style.display = 'flex';
            } else {
                dateFilter.style.display = 'none';
            }
        }
    }

    showDailyView() {
        const dailyView = document.querySelector('.daily-view');
        if (dailyView) {
            dailyView.style.display = 'flex';
            this.renderTransactionTable(this.transactions);
        }
    }

    showWeeklyView() {
        const weeklyView = document.querySelector('.weekly-view');
        if (weeklyView) {
            weeklyView.style.display = 'flex';
            this.renderPeriodData('weekly');
        }
    }

    showMonthlyView() {
        const monthlyView = document.querySelector('.monthly-view');
        if (monthlyView) {
            monthlyView.style.display = 'flex';
            this.renderPeriodData('monthly');
        }
    }

    showYearlyView() {
        const yearlyView = document.querySelector('.yearly-view');
        if (yearlyView) {
            yearlyView.style.display = 'flex';
            this.renderPeriodData('yearly');
        }
    }

    // ===== EXPORT FUNCTIONALITY =====
    toggleExportDropdown() {
        const dropdown = document.querySelector('.export-dropdown');
        if (dropdown) {
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
            dropdown.classList.toggle('show', !isVisible);
        }
    }

    closeExportDropdown() {
        const dropdown = document.querySelector('.export-dropdown');
        if (dropdown) {
            dropdown.style.display = 'none';
            dropdown.classList.remove('show');
        }
    }

    exportPDF() {
        console.log('Exporting to PDF...');
        const filterDate = document.getElementById('filter-date')?.value || this.currentDate;
        window.location.href = `/mitra/reports/export-pdf?period=${this.currentView}&date=${filterDate}`;
    }

    exportExcel() {
        console.log('Exporting to Excel...');
        const filterDate = document.getElementById('filter-date')?.value || this.currentDate;
        window.location.href = `/mitra/reports/export-excel?period=${this.currentView}&date=${filterDate}`;
    }

    // ===== FILTER FUNCTIONALITY =====
    applyDateFilter() {
        const filterDateInput = document.getElementById('filter-date');
        if (!filterDateInput) return;

        const filterDate = filterDateInput.value;
        
        if (!filterDate) {
            this.showAlert('warning', 'Peringatan', 'Silakan pilih tanggal terlebih dahulu');
            return;
        }
        
        this.currentDate = filterDate;
        
        // Reload data with new date
        this.loadReportData(this.currentView, filterDate).then(() => {
            this.showDailyView();
            this.showAlert('success', 'Filter Diterapkan', `Menampilkan data untuk tanggal ${filterDate}`);
        });
    }

    // ===== DATA RENDERING =====
    renderTransactionTable(transactions) {
        const transactionTable = document.getElementById('transaction-table');
        if (!transactionTable) return;

        transactionTable.innerHTML = '';

        if (transactions.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="4" style="text-align: center; padding: 20px; color: var(--gray-500);">
                    Tidak ada transaksi pada tanggal yang dipilih
                </td>
            `;
            transactionTable.appendChild(row);
            return;
        }

        transactions.forEach(transaction => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><span class="service-type">${transaction.service}</span></td>
                <td>${transaction.vehicle}</td>
                <td>${transaction.plate}</td>
                <td class="amount">Rp ${transaction.amount.toLocaleString('id-ID')}</td>
            `;
            transactionTable.appendChild(row);
        });
    }

    renderWeeklyData() {
        this.renderPeriodData('weekly');
    }

    renderMonthlyData() {
        this.renderPeriodData('monthly');
    }

    renderYearlyData() {
        this.renderPeriodData('yearly');
    }
    
    renderPeriodData(period) {
        const tableId = `${period}-table`;
        const transactionsId = `${period}-transactions`;
        const incomeId = `${period}-income`;
        
        const table = document.getElementById(tableId);
        if (!table) return;

        table.innerHTML = '';
        
        this.transactions.forEach(data => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${data.period}</td>
                <td>${data.count}</td>
                <td class="amount">Rp ${data.income.toLocaleString('id-ID')}</td>
            `;
            table.appendChild(row);
        });
        
        document.getElementById(transactionsId).textContent = this.totalTransactions;
        document.getElementById(incomeId).textContent = 'Rp ' + this.totalIncome.toLocaleString('id-ID');
    }
    
    updateSummary() {
        // Update summary for daily view
        const dailyTransactions = document.getElementById('total-transactions');
        const dailyIncome = document.getElementById('total-income');
        
        if (dailyTransactions) {
            dailyTransactions.textContent = this.totalTransactions;
        }
        if (dailyIncome) {
            dailyIncome.textContent = 'Rp ' + this.totalIncome.toLocaleString('id-ID');
        }
    }
        
        this.yearlyData.forEach(data => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${data.year}</td>
                <td>${data.transactions}</td>
                <td class="amount">Rp ${data.income.toLocaleString('id-ID')}</td>
            `;
            yearlyTable.appendChild(row);
            
            totalTransactions += data.transactions;
            totalIncome += data.income;
        });
        
        document.getElementById('yearly-transactions').textContent = totalTransactions;
        document.getElementById('yearly-income').textContent = 'Rp ' + totalIncome.toLocaleString('id-ID');
    }

    // ===== CALCULATION FUNCTIONS =====
    calculateTotals(transactions) {
        const totalTransactions = transactions.length;
        const totalIncome = transactions.reduce((sum, transaction) => sum + transaction.amount, 0);
        
        document.getElementById('total-transactions').textContent = totalTransactions;
        document.getElementById('total-income').textContent = 'Rp ' + totalIncome.toLocaleString('id-ID');
    }

    // ===== ALERT SYSTEM =====
    showAlert(type, title, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert--${type}`;
        
        const icon = type === 'success' ? '✓' : 
                    type === 'error' ? '✕' : 
                    type === 'warning' ? '!' : 
                    type === 'info' ? 'ℹ' : 'i';
        
        alert.innerHTML = `
            <div class="alert__icon">${icon}</div>
            <div class="alert__content">
                <div class="alert__title">${title}</div>
                <div class="alert__message">${message}</div>
            </div>
            <button class="alert__close">✕</button>
        `;

        document.body.appendChild(alert);

        const closeBtn = alert.querySelector('.alert__close');
        closeBtn.addEventListener('click', () => {
            this.closeAlert(alert);
        });

        setTimeout(() => {
            if (document.body.contains(alert)) {
                this.closeAlert(alert);
            }
        }, 5000);
    }

    closeAlert(alert) {
        alert.classList.add('alert--closing');
        setTimeout(() => {
            if (document.body.contains(alert)) {
                alert.remove();
            }
        }, 300);
    }
}

// ===== GLOBAL FUNCTIONS =====
function goBack() {
    window.history.back();
}

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    try {
        const historyManager = new PrismoHistoryManager();
        historyManager.init();
        window.prismoHistory = historyManager;

        console.log('PRISMO History System loaded successfully');
        
    } catch (error) {
        console.error('Failed to load PRISMO History System:', error);
        
        const main = document.querySelector('.main');
        if (main) {
            main.innerHTML = `
                <div style="text-align: center; padding: 2rem;">
                    <p>Terjadi kesalahan saat memuat halaman history. Silakan refresh halaman.</p>
                    <button onclick="location.reload()" class="btn" style="margin-top: 1rem;">
                        Refresh Halaman
                    </button>
                </div>
            `;
        }
    }
});

// Add alert styles
const style = document.createElement('style');
style.textContent = `
    .alert {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        z-index: 1060;
        max-width: 400px;
        animation: alert-slide-in 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-left: 4px solid;
    }

    .alert--success {
        border-left-color: #10b981;
    }

    .alert--error {
        border-left-color: #ef4444;
    }

    .alert--warning {
        border-left-color: #f59e0b;
    }

    .alert--info {
        border-left-color: #3b82f6;
    }

    .alert__icon {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .alert__content {
        flex: 1;
    }

    .alert__title {
        font-weight: 600;
        margin-bottom: 0.25rem;
        text-align: left;
    }

    .alert__message {
        font-size: 0.875rem;
        opacity: 0.9;
        text-align: left;
    }

    .alert__close {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.25rem;
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }

    .alert__close:hover {
        opacity: 1;
    }

    @keyframes alert-slide-in {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .alert--closing {
        animation: alert-slide-out 0.3s ease forwards;
    }

    @keyframes alert-slide-out {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);