document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const clearSearchBtn = document.getElementById('clearSearch');
    const categoryTabs = document.querySelectorAll('.category-tab');
    const faqItems = document.querySelectorAll('.faq-item');
    const faqQuestions = document.querySelectorAll('.faq-question');
    const noResults = document.getElementById('noResults');
    
    let currentCategory = 'all';
    let searchQuery = '';

    // Search functionality
    searchInput.addEventListener('input', function() {
        searchQuery = this.value.toLowerCase().trim();
        
        if (searchQuery) {
            clearSearchBtn.style.display = 'block';
        } else {
            clearSearchBtn.style.display = 'none';
        }
        
        filterFAQs();
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        searchQuery = '';
        this.style.display = 'none';
        filterFAQs();
        searchInput.focus();
    });

    // Category filter
    categoryTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            categoryTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.getAttribute('data-category');
            filterFAQs();
        });
    });

    // FAQ accordion
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const faqItem = this.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Close all other items (optional: remove these lines for multi-open accordion)
            faqItems.forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('active');
                }
            });
            
            // Toggle current item
            if (isActive) {
                faqItem.classList.remove('active');
            } else {
                faqItem.classList.add('active');
            }
        });
    });

    function filterFAQs() {
        let visibleCount = 0;
        
        faqItems.forEach(item => {
            const category = item.getAttribute('data-category');
            const question = item.querySelector('.faq-question span').textContent.toLowerCase();
            const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
            
            let matchesCategory = currentCategory === 'all' || category === currentCategory;
            let matchesSearch = !searchQuery || 
                               question.includes(searchQuery) || 
                               answer.includes(searchQuery);
            
            if (matchesCategory && matchesSearch) {
                item.classList.remove('hidden');
                visibleCount++;
                
                // Highlight matching text
                if (searchQuery) {
                    highlightText(item, searchQuery);
                } else {
                    removeHighlight(item);
                }
            } else {
                item.classList.add('hidden');
                item.classList.remove('active'); // Close if filtered out
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    function highlightText(element, query) {
        const questionSpan = element.querySelector('.faq-question span');
        const answerDiv = element.querySelector('.faq-answer');
        
        // Only highlight if not already highlighted
        if (!questionSpan.innerHTML.includes('<mark')) {
            const questionText = questionSpan.textContent;
            const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
            questionSpan.innerHTML = questionText.replace(regex, '<mark class="highlight">$1</mark>');
        }
    }

    function removeHighlight(element) {
        const questionSpan = element.querySelector('.faq-question span');
        const questionText = questionSpan.textContent;
        questionSpan.textContent = questionText; // This removes all HTML tags
    }

    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            searchInput.focus();
        }
        
        // Escape to clear search
        if (e.key === 'Escape' && searchInput === document.activeElement) {
            if (searchQuery) {
                searchInput.value = '';
                searchQuery = '';
                clearSearchBtn.style.display = 'none';
                filterFAQs();
            } else {
                searchInput.blur();
            }
        }
    });

    // Auto-expand FAQ if coming from hash
    if (window.location.hash) {
        const hash = window.location.hash.substring(1);
        const targetItem = document.querySelector(`[data-faq-id="${hash}"]`);
        if (targetItem) {
            targetItem.classList.add('active');
            targetItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

function goBack() {
    window.history.back();
}

function contactSupport() {
    // You can customize this action
    // Option 1: Open email client
    window.location.href = 'mailto:support@prismo.id?subject=Bantuan PRISMO';
    
    // Option 2: Open WhatsApp
    // window.open('https://wa.me/6281234567890?text=Halo, saya membutuhkan bantuan', '_blank');
    
    // Option 3: Navigate to contact page
    // window.location.href = '/contact';
}

// Export for potential external use
window.FAQManager = {
    openFAQ: function(faqId) {
        const faqItem = document.querySelector(`[data-faq-id="${faqId}"]`);
        if (faqItem) {
            faqItem.classList.add('active');
            faqItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    },
    
    searchFAQ: function(query) {
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.value = query;
            searchInput.dispatchEvent(new Event('input'));
        }
    },
    
    filterByCategory: function(category) {
        const tab = document.querySelector(`[data-category="${category}"]`);
        if (tab) {
            tab.click();
        }
    }
};
