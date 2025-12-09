// Use real data from server if available, otherwise use mock data
console.log('🔍 Loading services data...', window.servicesData);
const servicesData = window.servicesData || [
    {
        id: 1,
        name: "Prismo Pro",
        image: "/images/gambar2.png",
        rating: 4.8,
        reviews: 124,
        location: "Jl. Sudirman No. 45, Jakarta Pusat",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Pusat",
        distance: "2.3 km",
        prices: {
            basic: 35000,
            premium: 55000,
            complete: 85000
        },
        status: "open",
        closingTime: "22:00"
    },
    {
        id: 2,
        name: "CleanCar Express",
        image: "/images/gambar2.png",
        rating: 4.6,
        reviews: 89,
        location: "Jl. Thamrin No. 12, Jakarta Selatan",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Selatan",
        distance: "3.1 km",
        prices: {
            basic: 30000,
            premium: 50000,
            complete: 80000
        },
        status: "open",
        closingTime: "21:00"
    },
    {
        id: 3,
        name: "Mobil Bersih Center",
        image: "/images/gambar2.png",
        rating: 4.9,
        reviews: 156,
        location: "Jl. Gatot Subroto No. 78, Jakarta Barat",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Barat",
        distance: "4.5 km",
        prices: {
            basic: 40000,
            premium: 60000,
            complete: 90000
        },
        status: "closed",
        closingTime: "20:00"
    },
    {
        id: 4,
        name: "Auto Shine Premium",
        image: "/images/gambar2.png",
        rating: 4.7,
        reviews: 67,
        location: "Jl. MH Thamrin No. 23, Jakarta Pusat",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Pusat",
        distance: "1.8 km",
        prices: {
            basic: 45000,
            premium: 70000,
            complete: 100000
        },
        status: "open",
        closingTime: "23:00"
    },
    {
        id: 5,
        name: "Quick Wash Hub",
        image: "/images/gambar2.png",
        rating: 4.4,
        reviews: 45,
        location: "Jl. Rasuna Said No. 15, Jakarta Selatan",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Selatan",
        distance: "5.2 km",
        prices: {
            basic: 25000,
            premium: 45000,
            complete: 75000
        },
        status: "closed",
        closingTime: "19:00"
    },
    {
        id: 6,
        name: "Luxury Car Care",
        image: "/images/gambar2.png",
        rating: 4.9,
        reviews: 203,
        location: "Jl. Kemang Raya No. 8, Jakarta Selatan",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Selatan",
        distance: "6.1 km",
        prices: {
            basic: 50000,
            premium: 80000,
            complete: 120000
        },
        status: "open",
        closingTime: "22:30"
    },
    {
        id: 7,
        name: "Steam Master Pro",
        image: "/images/gambar2.png",
        rating: 4.5,
        reviews: 98,
        location: "Jl. Kuningan Raya No. 25, Jakarta Selatan",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Selatan",
        distance: "3.8 km",
        prices: {
            basic: 32000,
            premium: 52000,
            complete: 82000
        },
        status: "open",
        closingTime: "21:30"
    },
    {
        id: 8,
        name: "Sparkle Clean",
        image: "/images/gambar2.png",
        rating: 4.6,
        reviews: 142,
        location: "Jl. Panglima Polim No. 17, Jakarta Selatan",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Selatan",
        distance: "4.2 km",
        prices: {
            basic: 38000,
            premium: 58000,
            complete: 88000
        },
        status: "closed",
        closingTime: "20:30"
    },
    {
        id: 9,
        name: "Elite Car Wash",
        image: "/images/gambar2.png",
        rating: 4.8,
        reviews: 176,
        location: "Jl. TB Simatupang No. 88, Jakarta Selatan",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Selatan",
        distance: "7.5 km",
        prices: {
            basic: 42000,
            premium: 65000,
            complete: 95000
        },
        status: "open",
        closingTime: "22:00"
    },
    {
        id: 10,
        name: "Flash Steam Center",
        image: "/images/gambar2.png",
        rating: 4.7,
        reviews: 134,
        location: "Jl. Veteran No. 12, Jakarta Pusat",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Pusat",
        distance: "2.9 km",
        prices: {
            basic: 33000,
            premium: 53000,
            complete: 83000
        },
        status: "open",
        closingTime: "23:00"
    },
    {
        id: 11,
        name: "Prime Wash Studio",
        image: "/images/gambar2.png",
        rating: 4.6,
        reviews: 112,
        location: "Jl. Senopati No. 33, Jakarta Selatan",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Selatan",
        distance: "5.4 km",
        prices: {
            basic: 36000,
            premium: 56000,
            complete: 86000
        },
        status: "open",
        closingTime: "21:00"
    },
    {
        id: 12,
        name: "Crystal Clean Pro",
        image: "/images/gambar2.png",
        rating: 4.8,
        reviews: 189,
        location: "Jl. Cikini Raya No. 45, Jakarta Pusat",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Pusat",
        distance: "3.2 km",
        prices: {
            basic: 39000,
            premium: 59000,
            complete: 89000
        },
        status: "closed",
        closingTime: "20:00"
    },
    {
        id: 13,
        name: "Shine & Gloss",
        image: "/images/gambar2.png",
        rating: 4.5,
        reviews: 95,
        location: "Jl. Pramuka No. 67, Jakarta Timur",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Timur",
        distance: "8.1 km",
        prices: {
            basic: 28000,
            premium: 48000,
            complete: 78000
        },
        status: "open",
        closingTime: "22:00"
    },
    {
        id: 14,
        name: "Metro Steam Wash",
        image: "/images/gambar2.png",
        rating: 4.7,
        reviews: 156,
        location: "Jl. Kelapa Gading No. 88, Jakarta Utara",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Utara",
        distance: "10.2 km",
        prices: {
            basic: 34000,
            premium: 54000,
            complete: 84000
        },
        status: "open",
        closingTime: "21:30"
    },
    {
        id: 15,
        name: "Royal Car Care",
        image: "/images/gambar2.png",
        rating: 4.9,
        reviews: 221,
        location: "Jl. Menteng Raya No. 22, Jakarta Pusat",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Pusat",
        distance: "2.5 km",
        prices: {
            basic: 48000,
            premium: 75000,
            complete: 110000
        },
        status: "open",
        closingTime: "23:00"
    },
    {
        id: 16,
        name: "Aqua Steam Express",
        image: "/images/gambar2.png",
        rating: 4.4,
        reviews: 87,
        location: "Jl. Fatmawati No. 56, Jakarta Selatan",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Selatan",
        distance: "6.8 km",
        prices: {
            basic: 31000,
            premium: 51000,
            complete: 81000
        },
        status: "closed",
        closingTime: "19:30"
    },
    {
        id: 17,
        name: "Diamond Wash",
        image: "/images/gambar2.png",
        rating: 4.8,
        reviews: 178,
        location: "Jl. Kebon Jeruk No. 44, Jakarta Barat",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Barat",
        distance: "5.9 km",
        prices: {
            basic: 41000,
            premium: 62000,
            complete: 92000
        },
        status: "open",
        closingTime: "22:00"
    },
    {
        id: 18,
        name: "Perfect Clean",
        image: "/images/gambar2.png",
        rating: 4.6,
        reviews: 143,
        location: "Jl. Rawamangun No. 77, Jakarta Timur",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Timur",
        distance: "9.3 km",
        prices: {
            basic: 29000,
            premium: 49000,
            complete: 79000
        },
        status: "open",
        closingTime: "21:00"
    },
    {
        id: 19,
        name: "Supreme Auto Spa",
        image: "/images/gambar2.png",
        rating: 4.7,
        reviews: 167,
        location: "Jl. Pluit Raya No. 99, Jakarta Utara",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Utara",
        distance: "11.5 km",
        prices: {
            basic: 37000,
            premium: 57000,
            complete: 87000
        },
        status: "open",
        closingTime: "22:30"
    },
    {
        id: 20,
        name: "Ultra Steam Center",
        image: "/images/gambar2.png",
        rating: 4.5,
        reviews: 102,
        location: "Jl. Tebet Raya No. 34, Jakarta Selatan",
        provinsi: "DKI Jakarta",
        kota: "Jakarta Selatan",
        distance: "4.7 km",
        prices: {
            basic: 35000,
            premium: 55000,
            complete: 85000
        },
        status: "closed",
        closingTime: "20:30"
    }
];

// Data Kota berdasarkan Provinsi
const kotaByProvinsi = {
    "Aceh": ["Banda Aceh", "Sabang", "Lhokseumawe", "Langsa", "Subulussalam", "Aceh Barat", "Aceh Barat Daya", "Aceh Besar", "Aceh Jaya", "Aceh Selatan", "Aceh Singkil", "Aceh Tamiang", "Aceh Tengah", "Aceh Tenggara", "Aceh Timur", "Aceh Utara", "Bener Meriah", "Bireuen", "Gayo Lues", "Nagan Raya", "Pidie", "Pidie Jaya", "Simeulue"],
    "Sumatera Utara": ["Medan", "Binjai", "Padangsidimpuan", "Pematangsiantar", "Sibolga", "Tanjungbalai", "Tebing Tinggi", "Asahan", "Batubara", "Dairi", "Deli Serdang", "Humbang Hasundutan", "Karo", "Labuhanbatu", "Labuhanbatu Selatan", "Labuhanbatu Utara", "Langkat", "Mandailing Natal", "Nias", "Nias Barat", "Nias Selatan", "Nias Utara", "Padang Lawas", "Padang Lawas Utara", "Pakpak Bharat", "Samosir", "Serdang Bedagai", "Simalungun", "Tapanuli Selatan", "Tapanuli Tengah", "Tapanuli Utara", "Toba"],
    "Sumatera Barat": ["Padang", "Bukittinggi", "Padangpanjang", "Pariaman", "Payakumbuh", "Sawahlunto", "Solok", "Agam", "Dharmasraya", "Kepulauan Mentawai", "Lima Puluh Kota", "Padang Pariaman", "Pasaman", "Pasaman Barat", "Pesisir Selatan", "Sijunjung", "Solok", "Solok Selatan", "Tanah Datar"],
    "Riau": ["Pekanbaru", "Dumai", "Bengkalis", "Indragiri Hilir", "Indragiri Hulu", "Kampar", "Kepulauan Meranti", "Kuantan Singingi", "Pelalawan", "Rokan Hilir", "Rokan Hulu", "Siak"],
    "Jambi": ["Jambi", "Sungai Penuh", "Batang Hari", "Bungo", "Kerinci", "Merangin", "Muaro Jambi", "Sarolangun", "Tanjung Jabung Barat", "Tanjung Jabung Timur", "Tebo"],
    "Sumatera Selatan": ["Palembang", "Lubuklinggau", "Pagar Alam", "Prabumulih", "Banyuasin", "Empat Lawang", "Lahat", "Muara Enim", "Musi Banyuasin", "Musi Rawas", "Musi Rawas Utara", "Ogan Ilir", "Ogan Komering Ilir", "Ogan Komering Ulu", "Ogan Komering Ulu Selatan", "Ogan Komering Ulu Timur", "Penukal Abab Lematang Ilir"],
    "Bengkulu": ["Bengkulu", "Bengkulu Selatan", "Bengkulu Tengah", "Bengkulu Utara", "Kaur", "Kepahiang", "Lebong", "Mukomuko", "Rejang Lebong", "Seluma"],
    "Lampung": ["Bandar Lampung", "Metro", "Lampung Barat", "Lampung Selatan", "Lampung Tengah", "Lampung Timur", "Lampung Utara", "Mesuji", "Pesawaran", "Pesisir Barat", "Pringsewu", "Tanggamus", "Tulang Bawang", "Tulang Bawang Barat", "Way Kanan"],
    "Kepulauan Bangka Belitung": ["Pangkalpinang", "Bangka", "Bangka Barat", "Bangka Selatan", "Bangka Tengah", "Belitung", "Belitung Timur"],
    "Kepulauan Riau": ["Batam", "Tanjungpinang", "Bintan", "Karimun", "Kepulauan Anambas", "Lingga", "Natuna"],
    "DKI Jakarta": ["Jakarta Pusat", "Jakarta Selatan", "Jakarta Timur", "Jakarta Barat", "Jakarta Utara", "Kepulauan Seribu"],
    "Jawa Barat": ["Bandung", "Banjar", "Bekasi", "Bogor", "Cimahi", "Cirebon", "Depok", "Sukabumi", "Tasikmalaya", "Bandung Barat", "Ciamis", "Cianjur", "Garut", "Indramayu", "Karawang", "Kuningan", "Majalengka", "Pangandaran", "Purwakarta", "Subang", "Sumedang", "Kabupaten Bandung", "Kabupaten Bekasi", "Kabupaten Bogor", "Kabupaten Cirebon", "Kabupaten Sukabumi", "Kabupaten Tasikmalaya"],
    "Jawa Tengah": ["Magelang", "Pekalongan", "Salatiga", "Semarang", "Surakarta", "Tegal", "Banjarnegara", "Banyumas", "Batang", "Blora", "Boyolali", "Brebes", "Cilacap", "Demak", "Grobogan", "Jepara", "Karanganyar", "Kebumen", "Kendal", "Klaten", "Kudus", "Pati", "Pemalang", "Purbalingga", "Purworejo", "Rembang", "Sragen", "Sukoharjo", "Temanggung", "Wonogiri", "Wonosobo", "Kabupaten Magelang", "Kabupaten Pekalongan", "Kabupaten Semarang", "Kabupaten Tegal"],
    "DI Yogyakarta": ["Yogyakarta", "Bantul", "Gunung Kidul", "Kulon Progo", "Sleman"],
    "Jawa Timur": ["Batu", "Blitar", "Kediri", "Madiun", "Malang", "Mojokerto", "Pasuruan", "Probolinggo", "Surabaya", "Bangkalan", "Banyuwangi", "Bojonegoro", "Bondowoso", "Gresik", "Jember", "Jombang", "Lamongan", "Lumajang", "Magetan", "Nganjuk", "Ngawi", "Pacitan", "Pamekasan", "Ponorogo", "Sampang", "Sidoarjo", "Situbondo", "Sumenep", "Trenggalek", "Tuban", "Tulungagung", "Kabupaten Blitar", "Kabupaten Kediri", "Kabupaten Madiun", "Kabupaten Malang", "Kabupaten Mojokerto", "Kabupaten Pasuruan", "Kabupaten Probolinggo"],
    "Banten": ["Cilegon", "Serang", "Tangerang", "Tangerang Selatan", "Lebak", "Pandeglang", "Kabupaten Serang", "Kabupaten Tangerang"],
    "Bali": ["Denpasar", "Badung", "Bangli", "Buleleng", "Gianyar", "Jembrana", "Karangasem", "Klungkung", "Tabanan"],
    "Nusa Tenggara Barat": ["Bima", "Mataram", "Bima", "Dompu", "Lombok Barat", "Lombok Tengah", "Lombok Timur", "Lombok Utara", "Sumbawa", "Sumbawa Barat"],
    "Nusa Tenggara Timur": ["Kupang", "Alor", "Belu", "Ende", "Flores Timur", "Kupang", "Lembata", "Malaka", "Manggarai", "Manggarai Barat", "Manggarai Timur", "Nagekeo", "Ngada", "Rote Ndao", "Sabu Raijua", "Sikka", "Sumba Barat", "Sumba Barat Daya", "Sumba Tengah", "Sumba Timur", "Timor Tengah Selatan", "Timor Tengah Utara"],
    "Kalimantan Barat": ["Pontianak", "Singkawang", "Bengkayang", "Kapuas Hulu", "Kayong Utara", "Ketapang", "Kubu Raya", "Landak", "Melawi", "Mempawah", "Sambas", "Sanggau", "Sekadau", "Sintang"],
    "Kalimantan Tengah": ["Palangka Raya", "Barito Selatan", "Barito Timur", "Barito Utara", "Gunung Mas", "Kapuas", "Katingan", "Kotawaringin Barat", "Kotawaringin Timur", "Lamandau", "Murung Raya", "Pulang Pisau", "Seruyan", "Sukamara"],
    "Kalimantan Selatan": ["Banjarbaru", "Banjarmasin", "Balangan", "Banjar", "Barito Kuala", "Hulu Sungai Selatan", "Hulu Sungai Tengah", "Hulu Sungai Utara", "Kotabaru", "Tabalong", "Tanah Bumbu", "Tanah Laut", "Tapin"],
    "Kalimantan Timur": ["Balikpapan", "Bontang", "Samarinda", "Berau", "Kutai Barat", "Kutai Kartanegara", "Kutai Timur", "Mahakam Ulu", "Paser", "Penajam Paser Utara"],
    "Kalimantan Utara": ["Tarakan", "Bulungan", "Malinau", "Nunukan", "Tana Tidung"],
    "Sulawesi Utara": ["Bitung", "Kotamobagu", "Manado", "Tomohon", "Bolaang Mongondow", "Bolaang Mongondow Selatan", "Bolaang Mongondow Timur", "Bolaang Mongondow Utara", "Kepulauan Sangihe", "Kepulauan Siau Tagulandang Biaro", "Kepulauan Talaud", "Minahasa", "Minahasa Selatan", "Minahasa Tenggara", "Minahasa Utara"],
    "Sulawesi Tengah": ["Palu", "Banggai", "Banggai Kepulauan", "Banggai Laut", "Buol", "Donggala", "Morowali", "Morowali Utara", "Parigi Moutong", "Poso", "Sigi", "Tojo Una-Una", "Toli-Toli"],
    "Sulawesi Selatan": ["Makassar", "Palopo", "Parepare", "Bantaeng", "Barru", "Bone", "Bulukumba", "Enrekang", "Gowa", "Jeneponto", "Kepulauan Selayar", "Luwu", "Luwu Timur", "Luwu Utara", "Maros", "Pangkajene dan Kepulauan", "Pinrang", "Sidenreng Rappang", "Sinjai", "Soppeng", "Takalar", "Tana Toraja", "Toraja Utara", "Wajo"],
    "Sulawesi Tenggara": ["Bau-Bau", "Kendari", "Bombana", "Buton", "Buton Selatan", "Buton Tengah", "Buton Utara", "Kolaka", "Kolaka Timur", "Kolaka Utara", "Konawe", "Konawe Kepulauan", "Konawe Selatan", "Konawe Utara", "Muna", "Muna Barat", "Wakatobi"],
    "Gorontalo": ["Gorontalo", "Boalemo", "Bone Bolango", "Gorontalo", "Gorontalo Utara", "Pohuwato"],
    "Sulawesi Barat": ["Majene", "Mamasa", "Mamuju", "Mamuju Tengah", "Mamuju Utara", "Polewali Mandar"],
    "Maluku": ["Ambon", "Tual", "Buru", "Buru Selatan", "Kepulauan Aru", "Maluku Barat Daya", "Maluku Tengah", "Maluku Tenggara", "Maluku Tenggara Barat", "Seram Bagian Barat", "Seram Bagian Timur"],
    "Maluku Utara": ["Ternate", "Tidore Kepulauan", "Halmahera Barat", "Halmahera Selatan", "Halmahera Tengah", "Halmahera Timur", "Halmahera Utara", "Kepulauan Sula", "Pulau Morotai", "Pulau Taliabu"],
    "Papua Barat": ["Sorong", "Fakfak", "Kaimana", "Manokwari", "Manokwari Selatan", "Maybrat", "Pegunungan Arfak", "Raja Ampat", "Sorong", "Sorong Selatan", "Tambrauw", "Teluk Bintuni", "Teluk Wondama"],
    "Papua": ["Jayapura", "Asmat", "Biak Numfor", "Boven Digoel", "Deiyai", "Dogiyai", "Intan Jaya", "Jayapura", "Jayawijaya", "Keerom", "Kepulauan Yapen", "Lanny Jaya", "Mamberamo Raya", "Mamberamo Tengah", "Mappi", "Merauke", "Mimika", "Nabire", "Nduga", "Paniai", "Pegunungan Bintang", "Puncak", "Puncak Jaya", "Sarmi", "Supiori", "Tolikara", "Waropen", "Yahukimo", "Yalimo"],
    "Papua Barat Daya": ["Sorong", "Fakfak", "Kaimana", "Manokwari", "Maybrat", "Raja Ampat", "Sorong Selatan", "Tambrauw"],
    "Papua Pegunungan": ["Jayawijaya", "Lanny Jaya", "Mamberamo Tengah", "Nduga", "Pegunungan Bintang", "Tolikara", "Yahukimo", "Yalimo"],
    "Papua Selatan": ["Asmat", "Boven Digoel", "Mappi", "Merauke"],
    "Papua Tengah": ["Deiyai", "Dogiyai", "Intan Jaya", "Mimika", "Nabire", "Paniai", "Puncak", "Puncak Jaya"]
};

// Filter state
let currentFilters = {
    provinsi: '',
    kota: '',
    searchTerm: ''
};

// Navigation Active State Management
function initNavigation() {
    const navLinks = document.querySelectorAll('.nav-menu .nav-link');
    
    function setActiveNav() {
        const currentPage = window.location.pathname;
        
        navLinks.forEach(link => {
            link.classList.remove('active');
            
            const linkHref = link.getAttribute('href');
            
            // Check for exact match or if current page ends with the link path
            if (currentPage === linkHref || 
                currentPage === '/dashboard' && linkHref.includes('/dashboard') ||
                currentPage.includes('/customer/dashboard') && linkHref.includes('/dashboard')) {
                link.classList.add('active');
            }
        });
    }
    
    // Event listeners untuk click
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Set active state on page load
    setActiveNav();
    window.addEventListener('popstate', setActiveNav);
}

// Notification Permission Popup (for browser notification permission)
function initNotificationPermission() {
    const notificationPopup = document.querySelector('.notification-popup');
    const btnBlokir = document.querySelector('.btn-blokir');
    const btnIzinkan = document.querySelector('.btn-izinkan');
    
    // Notification Popup
    if (btnBlokir && notificationPopup) {
        btnBlokir.addEventListener('click', () => {
            notificationPopup.style.display = 'none';
        });
    }
    
    if (btnIzinkan && notificationPopup) {
        btnIzinkan.addEventListener('click', () => {
            notificationPopup.style.display = 'none';
            if ('Notification' in window) {
                Notification.requestPermission();
            }
        });
    }
    
    // Notification panel toggle is now handled by notification-system.js
    console.log('🔔 Using shared notification-system.js');
}

// Service Cards Generation - Now handled by pagination
function generateServiceCards() {
    // This function is now handled by initServicesNavigation with pagination
    // Keeping for compatibility with other code that might call it
    return;
}

// Search Functionality
function initSearch() {
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.toLowerCase().trim();
                if (searchTerm.length > 0) {
                    filterServices(searchTerm);
                } else {
                    resetServiceFilter();
                }
            }, 300);
        });
    }
}

function filterServices(searchTerm) {
    const serviceCards = document.querySelectorAll('.service-card');
    let hasResults = false;
    
    serviceCards.forEach(card => {
        const serviceName = card.querySelector('.service-name').textContent.toLowerCase();
        const serviceLocation = card.querySelector('.service-location').textContent.toLowerCase();
        
        if (serviceName.includes(searchTerm) || serviceLocation.includes(searchTerm)) {
            card.style.display = 'block';
            hasResults = true;
        } else {
            card.style.display = 'none';
        }
    });
    
    showNoResults(!hasResults);
}

function resetServiceFilter() {
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach(card => {
        card.style.display = 'block';
    });
    showNoResults(false);
}

function showNoResults(show) {
    let noResults = document.getElementById('noResults');
    const servicesGrid = document.getElementById('servicesGrid');
    
    if (show && !noResults && servicesGrid) {
        noResults = document.createElement('div');
        noResults.id = 'noResults';
        noResults.className = 'no-results';
        noResults.textContent = 'Tidak ada layanan yang sesuai dengan pencarian';
        servicesGrid.appendChild(noResults);
    } else if (!show && noResults) {
        noResults.remove();
    }
}

// Service Card Interactions
function initServiceCards() {
    const serviceCards = document.querySelectorAll('.service-card');
    
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-8px)';
            card.style.transition = 'transform 0.3s ease, box-shadow 0.3s ease';
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
}

// Promo Button Handler
function handleKlaim() {
    console.log('Promo diklaim');
    // Redirect logic bisa ditambahkan di sini
    // window.location.href = '/customer/booking/Rbooking.html?promo=WELCOME2024';
}

// Load User Profile - Avatar now loaded from database via Blade template
function loadUserProfile() {
    // All data loaded from database via Blade template
    console.log('✅ User profile loaded from database');
}

// Main Initialization Function
function initializePrismoApp() {
    // Initialize semua komponen
    initNavigation();
    initNotificationPermission();
    initSortPanel();
    generateServiceCards();
    initSearch();
    initServiceCards();
    initServicesNavigation();
    loadUserProfile();
    initMobileMenu();
    
    // Expose handleKlaim ke global scope untuk HTML onclick
    window.handleKlaim = handleKlaim;
    
    console.log('Prismo App Initialized Successfully');
}

// Mobile Menu Toggle
function initMobileMenu() {
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!menuToggle.contains(event.target) && !mainNav.contains(event.target)) {
                mainNav.classList.remove('active');
            }
        });
        
        // Close menu when clicking a nav link
        const navLinks = mainNav.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                mainNav.classList.remove('active');
            });
        });
    }
}

// Sort Panel Functions
function initSortPanel() {
    console.log('🔧 Initializing Sort Panel...');
    const sortBtn = document.getElementById('sortBtn');
    const sortPanel = document.getElementById('sortPanel');
    const sortOverlay = document.getElementById('sortOverlay');
    const closeSortBtn = document.getElementById('closeSortBtn');
    const provinsiSelect = document.getElementById('provinsiSelect');
    const kotaSelect = document.getElementById('kotaSelect');
    const applyFilterBtn = document.getElementById('applyFilterBtn');
    const resetFilterBtn = document.getElementById('resetFilterBtn');
    const searchInput = document.getElementById('searchInput');

    console.log('Sort elements:', { sortBtn, sortPanel, provinsiSelect, kotaSelect, applyFilterBtn });

    // Open sort panel
    if (sortBtn) {
        sortBtn.addEventListener('click', function() {
            console.log('Sort button clicked');
            sortPanel.classList.add('show');
            sortOverlay.classList.add('show');
        });
    }

    // Close sort panel
    function closeSortPanel() {
        sortPanel.classList.remove('show');
        sortOverlay.classList.remove('show');
    }

    if (closeSortBtn) {
        closeSortBtn.addEventListener('click', closeSortPanel);
    }

    if (sortOverlay) {
        sortOverlay.addEventListener('click', closeSortPanel);
    }

    // Handle provinsi change
    if (provinsiSelect) {
        provinsiSelect.addEventListener('change', function() {
            const selectedProvinsi = this.value;
            kotaSelect.innerHTML = '<option value="">Semua Kota/Kabupaten</option>';
            
            if (selectedProvinsi && kotaByProvinsi[selectedProvinsi]) {
                kotaSelect.disabled = false;
                kotaByProvinsi[selectedProvinsi].forEach(kota => {
                    const option = document.createElement('option');
                    option.value = kota;
                    option.textContent = kota;
                    kotaSelect.appendChild(option);
                });
            } else {
                kotaSelect.disabled = true;
                kotaSelect.innerHTML = '<option value="">Pilih provinsi terlebih dahulu</option>';
            }
        });
    }

    // Apply filter
    if (applyFilterBtn) {
        applyFilterBtn.addEventListener('click', function() {
            currentFilters.provinsi = provinsiSelect.value.trim();
            currentFilters.kota = kotaSelect.value.trim();
            console.log('Filter applied - Provinsi:', currentFilters.provinsi, 'Kota:', currentFilters.kota);
            applyFilters();
            closeSortPanel();
        });
    }

    // Reset filter
    if (resetFilterBtn) {
        resetFilterBtn.addEventListener('click', function() {
            provinsiSelect.value = '';
            kotaSelect.value = '';
            kotaSelect.disabled = true;
            kotaSelect.innerHTML = '<option value="">Pilih provinsi terlebih dahulu</option>';
            currentFilters.provinsi = '';
            currentFilters.kota = '';
            currentFilters.searchTerm = '';
            if (searchInput) searchInput.value = '';
            clearFiltersAndShowAll();
        });
    }

    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            currentFilters.searchTerm = this.value.toLowerCase();
            applyFilters();
        });
    }
}

function applyFilters() {
    let filteredServices = servicesData;

    console.log('Applying filters:', currentFilters);
    console.log('Total services before filter:', filteredServices.length);

    // Check if any filter is active
    const hasActiveFilter = currentFilters.provinsi || currentFilters.kota || currentFilters.searchTerm;

    if (!hasActiveFilter) {
        // No filter active, show all with pagination
        clearFiltersAndShowAll();
        return;
    }

    // Filter by provinsi
    if (currentFilters.provinsi) {
        filteredServices = filteredServices.filter(service => 
            service.provinsi === currentFilters.provinsi
        );
        console.log('After provinsi filter:', filteredServices.length);
    }

    // Filter by kota
    if (currentFilters.kota) {
        filteredServices = filteredServices.filter(service => {
            const match = service.kota === currentFilters.kota;
            console.log(`Comparing service.kota="${service.kota}" with filter="${currentFilters.kota}": ${match}`);
            return match;
        });
        console.log('After kota filter:', filteredServices.length);
    }

    // Filter by search term
    if (currentFilters.searchTerm) {
        filteredServices = filteredServices.filter(service => 
            service.name.toLowerCase().includes(currentFilters.searchTerm) ||
            service.location.toLowerCase().includes(currentFilters.searchTerm)
        );
    }

    console.log('Final filtered services:', filteredServices.length);
    // Re-generate service cards with filtered data
    generateFilteredServiceCards(filteredServices);
}

function generateFilteredServiceCards(services) {
    const servicesGrid = document.getElementById('servicesGrid');
    if (!servicesGrid) return;

    if (services.length === 0) {
        servicesGrid.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <i class="fas fa-search" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
                <p style="font-size: 16px; color: #666;">Tidak ada layanan yang sesuai dengan filter</p>
            </div>
        `;
        return;
    }

    servicesGrid.innerHTML = '';
    services.forEach(service => {
        const serviceCard = document.createElement('div');
        serviceCard.className = 'service-card';
        serviceCard.onclick = () => window.location.href = '/customer/detail-mitra/minipro';
        
        const statusClass = service.status === 'open' ? 'status-open' : 'status-closed';
        const statusText = service.status === 'open' ? 'Buka' : 'Tutup';
        
        serviceCard.innerHTML = `
            <div class="service-image">
                <img src="${service.image}" alt="${service.name}">
            </div>
            <div class="service-info">
                <div class="service-header">
                    <h3 class="service-name">${service.name}</h3>
                    <div class="service-rating">
                        <i class="fas fa-star"></i>
                        <span>${service.rating} (${service.reviews})</span>
                    </div>
                </div>
                <p class="service-location">
                    <i class="fas fa-map-marker-alt"></i>
                    ${service.kota}, ${service.provinsi}
                </p>
                <p class="service-address">
                    ${service.location}
                </p>
                <div class="service-prices">
                    <div class="price-item">
                        <span>Basic Steam</span>
                        <span class="price">Rp ${service.prices.basic.toLocaleString()}</span>
                    </div>
                    <div class="price-item">
                        <span>Premium Steam</span>
                        <span class="price">Rp ${service.prices.premium.toLocaleString()}</span>
                    </div>
                    <div class="price-item">
                        <span>Complete Detail</span>
                        <span class="price">Rp ${service.prices.complete.toLocaleString()}</span>
                    </div>
                </div>
            </div>
        `;
        servicesGrid.appendChild(serviceCard);
    });
    
    // Hide pagination when filtering
    const navLeft = document.getElementById('servicesNavLeft');
    const navRight = document.getElementById('servicesNavRight');
    const pagination = document.getElementById('servicesPagination');
    
    if (navLeft) navLeft.style.display = 'none';
    if (navRight) navRight.style.display = 'none';
    if (pagination) pagination.style.display = 'none';
}

function clearFiltersAndShowAll() {
    // Show pagination again
    const navLeft = document.getElementById('servicesNavLeft');
    const navRight = document.getElementById('servicesNavRight');
    const pagination = document.getElementById('servicesPagination');
    
    if (navLeft) navLeft.style.display = '';
    if (navRight) navRight.style.display = '';
    if (pagination) pagination.style.display = '';
    
    // Re-render all data with pagination
    initServicesNavigation();
}

// ========== SERVICES NAVIGATION ==========
function initServicesNavigation() {
    const servicesGrid = document.getElementById('servicesGrid');
    const navLeft = document.getElementById('servicesNavLeft');
    const navRight = document.getElementById('servicesNavRight');
    const pagination = document.getElementById('servicesPagination');
    
    if (!servicesGrid || !navLeft || !navRight) return;
    
    // Calculate items per page based on screen width
    const getItemsPerPage = () => {
        const width = window.innerWidth;
        if (width >= 768) {
            return 15; // 3 kolom x 5 baris
        } else {
            return 10; // 2 kolom x 5 baris atau 1 kolom x 10 baris
        }
    };
    
    let itemsPerPage = getItemsPerPage();
    let currentPage = 0;
    let totalPages = Math.ceil(servicesData.length / itemsPerPage);
    
    // Render services for current page
    const renderServices = (page) => {
        servicesGrid.innerHTML = '';
        
        const startIndex = page * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, servicesData.length);
        const pageServices = servicesData.slice(startIndex, endIndex);
        
        pageServices.forEach(service => {
            const serviceCard = document.createElement('div');
            serviceCard.className = 'service-card';
            serviceCard.onclick = () => window.location.href = `/customer/detail-mitra/minipro/${service.id}`;
            
            const statusClass = service.status === 'open' ? 'status-open' : 'status-closed';
            const statusText = service.status === 'open' ? 'Buka' : 'Tutup';
            
            // Debug log for rating
            console.log(`📍 Rendering ${service.name}: rating=${service.rating}, reviews=${service.reviews}, completed=${service.completed_bookings}`);
            
            // Rating logic: (-) if no reviews, star - (count) if reviews but no rating, star rating (count) if rated
            let ratingDisplay;
            if (!service.reviews || service.reviews === 0) {
                ratingDisplay = `<i class="fas fa-star"></i><span>-</span>`;
            } else if (!service.rating || service.rating === 0) {
                ratingDisplay = `<i class="fas fa-star"></i><span>- (${service.reviews})</span>`;
            } else {
                ratingDisplay = `<i class="fas fa-star"></i><span>${service.rating} (${service.reviews})</span>`;
            }
            
            // Completed bookings info
            const completedInfo = service.completed_bookings > 0 
                ? `<span style="color: #666; font-size: 13px;">• ${service.completed_bookings} booking</span>`
                : '';
            
            serviceCard.innerHTML = `
                <div class="service-image">
                    <img src="${service.image || '/images/logo.png'}" alt="${service.name}">
                </div>
                <div class="service-info">
                    <div class="service-header">
                        <h3 class="service-name">${service.name}</h3>
                        <div class="service-rating">
                            ${ratingDisplay}
                            ${completedInfo}
                        </div>
                    </div>
                    <p class="service-location">
                        <i class="fas fa-map-marker-alt"></i>
                        ${service.kota}, ${service.provinsi}
                    </p>
                    <p class="service-address">
                        ${service.location}
                    </p>
                    <div class="service-prices">
                        ${service.services && service.services.length > 0 
                            ? service.services.slice(0, 3).map(s => `
                                <div class="price-item">
                                    <span>${s.name}</span>
                                    <span class="price">Rp ${(s.price || 0).toLocaleString('id-ID')}</span>
                                </div>
                            `).join('')
                            : '<div class="price-item"><span>Belum ada layanan</span><span class="price">-</span></div>'
                        }
                    </div>
                    <div class="service-footer" style="justify-content: center;">
                        <div class="service-status ${statusClass}">
                            <i class="fas fa-circle"></i>
                            <span>${statusText}</span>
                        </div>
                    </div>
                </div>
            `;
            
            servicesGrid.appendChild(serviceCard);
        });
        
        updateNavButtons();
    };
    
    // Generate pagination dots
    const generatePagination = () => {
        if (!pagination) return;
        pagination.innerHTML = '';
        
        if (totalPages <= 1) return;
        
        for (let i = 0; i < totalPages; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className = 'pagination-page';
            pageBtn.textContent = i + 1;
            pageBtn.setAttribute('data-page', i);
            if (i === currentPage) pageBtn.classList.add('active');
            
            pageBtn.addEventListener('click', () => {
                currentPage = i;
                renderServices(currentPage);
                updatePaginationDots();
            });
            
            pagination.appendChild(pageBtn);
        }
    };
    
    // Update pagination dots
    const updatePaginationDots = () => {
        if (!pagination) return;
        const pages = pagination.querySelectorAll('.pagination-page');
        pages.forEach((page, index) => {
            page.classList.toggle('active', index === currentPage);
        });
    };
    
    // Update navigation button states
    const updateNavButtons = () => {
        navLeft.disabled = currentPage === 0;
        navRight.disabled = currentPage === totalPages - 1;
    };
    
    // Previous page
    navLeft.addEventListener('click', () => {
        if (currentPage > 0) {
            currentPage--;
            renderServices(currentPage);
            updatePaginationDots();
        }
    });
    
    // Next page
    navRight.addEventListener('click', () => {
        if (currentPage < totalPages - 1) {
            currentPage++;
            renderServices(currentPage);
            updatePaginationDots();
        }
    });
    
    // Initial render
    generatePagination();
    renderServices(currentPage);
    
    // Handle window resize to update items per page
    window.addEventListener('resize', () => {
        const newItemsPerPage = getItemsPerPage();
        if (newItemsPerPage !== itemsPerPage) {
            itemsPerPage = newItemsPerPage;
            totalPages = Math.ceil(servicesData.length / itemsPerPage);
            currentPage = 0; // Reset to first page
            generatePagination();
            renderServices(currentPage);
            updateNavButtons();
        }
    });
}

// Auto-initialize aplikasi saat DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePrismoApp);
} else {
    initializePrismoApp();
}
