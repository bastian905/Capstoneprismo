// Contoh pengelolaan active state
const navLinks = document.querySelectorAll('.nav a');

navLinks.forEach(link => {
    link.addEventListener('click', function() {
        // Hapus class active dari semua link
        navLinks.forEach(l => l.classList.remove('active'));
        
        // Tambahkan class active ke link yang diklik
        this.classList.add('active');
    });
});
 
// ATAU set active berdasarkan URL saat halaman load
window.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname;
    
    navLinks.forEach(link => {
        // Hapus semua active class dulu
        link.classList.remove('active');
        
        // Cek apakah href link cocok dengan current page
        if (link.getAttribute('href') === currentPage || 
            currentPage.includes(link.getAttribute('href'))) {
            link.classList.add('active');
        }
    });
});