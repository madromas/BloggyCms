document.addEventListener('DOMContentLoaded', function() {
    const cookieConsent = document.querySelector('.cookie-consent-container');
    if (!cookieConsent) return;

    const cookieName = cookieConsent.dataset.cookieName;
    const cookieExpiry = parseInt(cookieConsent.dataset.cookieExpiry, 10);
    const autoShow = cookieConsent.dataset.autoShow === '1';
    const position = cookieConsent.dataset.position;

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value}; expires=${date.toUTCString()}; path=/; SameSite=Lax`;
    }

    function showConsent() {
        cookieConsent.style.transform = 'translateY(0)';
        cookieConsent.style.opacity = '1';
    }

    function hideConsent() {
        cookieConsent.style.transform = position === 'bottom' ? 'translateY(100%)' : 'translateY(-100%)';
        cookieConsent.style.opacity = '0';
        setTimeout(function() {
            cookieConsent.style.display = 'none';
        }, 300);
    }

    const consentGiven = getCookie(cookieName);
    if (consentGiven === 'accepted') {
        cookieConsent.style.display = 'none';
    } else if (consentGiven === 'declined') {
        cookieConsent.style.display = 'none';
    } else if (autoShow) {
        showConsent();
    }

    const acceptBtn = cookieConsent.querySelector('.cookie-accept-btn');
    const declineBtn = cookieConsent.querySelector('.cookie-decline-btn');

    if (acceptBtn) {
        acceptBtn.addEventListener('click', function() {
            setCookie(cookieName, 'accepted', cookieExpiry);
            hideConsent();

            const event = new CustomEvent('cookieConsentAccepted', { detail: { consent: true } });
            document.dispatchEvent(event);
        });
    }

    if (declineBtn) {
        declineBtn.addEventListener('click', function() {
            setCookie(cookieName, 'declined', cookieExpiry);
            hideConsent();

            const event = new CustomEvent('cookieConsentDeclined', { detail: { consent: false } });
            document.dispatchEvent(event);
        });
    }
});