document.addEventListener('DOMContentLoaded', function() {
    initSpoilers();
});

function initSpoilers() {
    const spoilers = document.querySelectorAll('[data-spoiler]');
    
    spoilers.forEach(function(spoiler) {
        const toggle = spoiler.querySelector('.spoiler-toggle');
        const content = spoiler.querySelector('.spoiler-content');
        
        if (!toggle || !content) return;
        
        const isOpen = spoiler.classList.contains('show');
        
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        content.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSpoiler(spoiler, toggle, content);
        });
    });
}

function toggleSpoiler(spoiler, toggle, content) {
    const isOpen = spoiler.classList.contains('show');
    const hasAnimation = !spoiler.classList.contains('no-animation');
    
    if (isOpen) {
        if (hasAnimation) {
            content.style.animation = 'spoilerFadeOut 0.2s ease-out';
            setTimeout(function() {
                spoiler.classList.remove('show');
                content.style.animation = '';
            }, 150);
        } else {
            spoiler.classList.remove('show');
        }
        toggle.setAttribute('aria-expanded', 'false');
        content.setAttribute('aria-hidden', 'true');
    } else {
        spoiler.classList.add('show');
        toggle.setAttribute('aria-expanded', 'true');
        content.setAttribute('aria-hidden', 'false');
        
        if (hasAnimation) {
            content.style.animation = 'spoilerFadeIn 0.3s ease-out';
            setTimeout(function() {
                content.style.animation = '';
            }, 300);
        }
    }
}

const style = document.createElement('style');
style.textContent = `
    @keyframes spoilerFadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }
`;
document.head.appendChild(style);