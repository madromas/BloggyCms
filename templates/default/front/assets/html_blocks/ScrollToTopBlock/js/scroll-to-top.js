document.addEventListener('DOMContentLoaded', function() {
    const scrollBtns = document.querySelectorAll('.scroll-to-top-btn');
    
    scrollBtns.forEach(function(scrollBtn) {
        const scrollThreshold = parseInt(scrollBtn.dataset.scrollThreshold, 10) || 300;
        const animationDuration = parseInt(scrollBtn.dataset.animationDuration, 10) || 500;
        
        function checkScroll() {
            if (window.scrollY > scrollThreshold) {
                scrollBtn.style.opacity = '1';
                scrollBtn.style.visibility = 'visible';
            } else {
                scrollBtn.style.opacity = '0';
                scrollBtn.style.visibility = 'hidden';
            }
        }
        
        window.addEventListener('scroll', checkScroll);
        checkScroll();
        
        scrollBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if ('scrollBehavior' in document.documentElement.style) {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            } else {
                const startPosition = window.scrollY;
                const startTime = performance.now();
                
                function animation(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / animationDuration, 1);
                    const easeInOutCubic = progress < 0.5
                        ? 4 * progress * progress * progress
                        : 1 - Math.pow(-2 * progress + 2, 3) / 2;
                    
                    window.scrollTo(0, startPosition * (1 - easeInOutCubic));
                    
                    if (elapsed < animationDuration) {
                        requestAnimationFrame(animation);
                    }
                }
                
                requestAnimationFrame(animation);
            }
        });
    });
});