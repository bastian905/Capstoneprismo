/**
 * Prevent Back Button Navigation
 * 
 * This script prevents users from navigating back to login/register pages
 * after they have successfully logged in to their dashboard.
 * 
 * Usage: Include this script in dashboard pages
 */

(function() {
    'use strict';
    
    // Push current state to prevent back navigation
    function preventBack() {
        window.history.pushState(null, "", window.location.href);
        window.onpopstate = function() {
            window.history.pushState(null, "", window.location.href);
        };
    }
    
    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', preventBack);
    } else {
        preventBack();
    }
    
    // Handle popstate event (back/forward button)
    window.addEventListener('popstate', function(event) {
        // Prevent navigation back
        window.history.pushState(null, "", window.location.href);
        
        // Optional: Show message to user
        // console.log('Back button is disabled on this page');
    });
    
    // Disable back button keyboard shortcut (Alt + Left Arrow, Backspace)
    document.addEventListener('keydown', function(event) {
        // Backspace key (except in input fields)
        if (event.keyCode === 8) {
            const target = event.target;
            const isInput = target.tagName === 'INPUT' || 
                          target.tagName === 'TEXTAREA' || 
                          target.isContentEditable;
            
            if (!isInput) {
                event.preventDefault();
                return false;
            }
        }
        
        // Alt + Left Arrow (back button shortcut)
        if (event.altKey && event.keyCode === 37) {
            event.preventDefault();
            return false;
        }
    });
    
})();
