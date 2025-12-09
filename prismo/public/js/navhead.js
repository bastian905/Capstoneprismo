document.addEventListener('DOMContentLoaded', function() {
    // Initialization
    const menuToggle = document.getElementById('menuToggle');
    const mainNav = document.getElementById('mainNav');
    
    // Debug: Check if elements exist
    console.log('Menu Toggle:', menuToggle);
    console.log('Main Nav:', mainNav);
    console.log('Window Width:', window.innerWidth);
    
    if (!menuToggle || !mainNav) {
        console.error('Header elements not found!');
        return;
    }
    
    // Ensure correct initial state
    function resetMenuState() {
        if (window.innerWidth > 600) {
            // Desktop mode
            mainNav.classList.remove('active');
            menuToggle.innerHTML = '☰';
            menuToggle.style.display = 'none';
        } else {
            // Mobile mode
            menuToggle.style.display = 'block';
            if (!mainNav.classList.contains('active')) {
                menuToggle.innerHTML = '☰';
            }
        }
    }
    
    // Initial state
    resetMenuState();
    
    // Toggle menu
    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        mainNav.classList.toggle('active');
        
        // Update icon
        if (mainNav.classList.contains('active')) {
            this.innerHTML = '✕';
            this.setAttribute('aria-expanded', 'true');
            console.log('Menu opened');
        } else {
            this.innerHTML = '☰';
            this.setAttribute('aria-expanded', 'false');
            console.log('Menu closed');
        }
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 600 && 
            !event.target.closest('.header-content') && 
            mainNav.classList.contains('active')) {
            mainNav.classList.remove('active');
            menuToggle.innerHTML = '☰';
            menuToggle.setAttribute('aria-expanded', 'false');
            console.log('Menu closed by outside click');
        }
    });
    
    // Close menu when clicking nav links
    const navLinks = mainNav.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (window.innerWidth <= 600) {
                mainNav.classList.remove('active');
                menuToggle.innerHTML = '☰';
                menuToggle.setAttribute('aria-expanded', 'false');
            }
            
            // Update active state
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            resetMenuState();
            console.log('Window resized to:', window.innerWidth);
        }, 250);
    });
});