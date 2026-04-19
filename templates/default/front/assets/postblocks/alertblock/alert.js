(function() {
    'use strict';
    
    function initAlerts() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        
        alerts.forEach(alert => {
            const closeButton = alert.querySelector('.btn-close');
            
            if (closeButton && !closeButton.hasAttribute('data-alert-initialized')) {
                closeButton.setAttribute('data-alert-initialized', 'true');
                
                closeButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const alertElement = this.closest('.alert');
                    if (alertElement) {
                        alertElement.classList.add('fade-out');
                        
                        setTimeout(() => {
                            if (alertElement.parentNode) {
                                alertElement.remove();
                            }
                        }, 300);
                    }
                });
            }
        });
    }
    
    function observeNewAlerts() {
        const observer = new MutationObserver(function(mutations) {
            let needInit = false;
            
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) {
                            if (node.classList && node.classList.contains('alert-dismissible')) {
                                needInit = true;
                            }
                            if (node.querySelectorAll) {
                                if (node.querySelectorAll('.alert-dismissible').length > 0) {
                                    needInit = true;
                                }
                            }
                        }
                    });
                }
            });
            
            if (needInit) {
                initAlerts();
            }
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        return observer;
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initAlerts();
            observeNewAlerts();
        });
    } else {
        initAlerts();
        observeNewAlerts();
    }
    
})();