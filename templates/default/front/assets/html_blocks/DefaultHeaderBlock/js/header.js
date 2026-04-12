(function() {
    'use strict';
    let docClickListener = null;
    function attachGlobalCloseHandler() {
        if (docClickListener) return;
        docClickListener = (e) => {
            document.querySelectorAll('[data-profile].is-open').forEach(profile => {
                const toggle = profile.querySelector('[data-profile-toggle]');
                if (!profile.contains(e.target)) {
                    profile.classList.remove('is-open');
                    if (toggle) toggle.setAttribute('aria-expanded', 'false');
                    document.querySelector('.header__overlay')?.classList.remove('is-visible');
                    restoreBodyScroll();
                }
            });
        };
        document.addEventListener('click', docClickListener);
    }
    function restoreBodyScroll() {
        if (!document.querySelector('[data-nav].is-open, [data-profile].is-open')) document.body.style.overflow = '';
    }
    function initHeader(header) {
        if (header.dataset.headerInit === 'true') return;
        header.dataset.headerInit = 'true';

        const burger = header.querySelector('[data-burger]');
        const nav = header.querySelector('[data-nav]');
        const searchToggle = header.querySelector('[data-search-toggle]');
        const searchPanel = header.querySelector('[data-search-panel]');
        const searchInput = searchPanel?.querySelector('input[type="text"]');
        const profile = header.querySelector('[data-profile]');
        const profileToggle = header.querySelector('[data-profile-toggle]');
        const mobileBreakpoint = parseInt(header.dataset.mobileBreakpoint) || 992;
        let overlay = document.querySelector('.header__overlay');
        if (!overlay) { overlay = document.createElement('div'); overlay.className = 'header__overlay'; document.body.appendChild(overlay); }
        let isMobile = window.innerWidth <= mobileBreakpoint;

        function closeAll(except = null) {
            if (except !== 'nav' && nav) { nav.classList.remove('is-open'); burger?.classList.remove('is-active'); burger?.setAttribute('aria-expanded', 'false'); }
            if (except !== 'search' && searchPanel) { searchPanel.hidden = true; searchToggle?.setAttribute('aria-expanded', 'false'); }
            if (except !== 'profile' && profile) { profile.classList.remove('is-open'); profileToggle?.setAttribute('aria-expanded', 'false'); overlay?.classList.remove('is-visible'); }
            restoreBodyScroll();
        }
        if (burger && nav) {
            burger.addEventListener('click', e => {
                e.stopPropagation();
                const isOpen = nav.classList.contains('is-open');
                if (!isOpen) { closeAll('nav'); nav.classList.add('is-open'); burger.classList.add('is-active'); burger.setAttribute('aria-expanded', 'true'); document.body.style.overflow = 'hidden'; }
                else closeAll();
            });
        }
        if (searchToggle && searchPanel) {
            searchToggle.addEventListener('click', e => {
                e.stopPropagation();
                const isOpen = !searchPanel.hidden;
                if (!isOpen) { closeAll('search'); searchPanel.hidden = false; searchToggle.setAttribute('aria-expanded', 'true'); searchInput?.focus(); }
                else { searchPanel.hidden = true; searchToggle.setAttribute('aria-expanded', 'false'); }
            });
        }
        if (profileToggle && profile) {
            profileToggle.addEventListener('click', e => {
                e.stopPropagation();
                if (!profile.querySelector('.header__dropdown')) return;
                const isOpen = profile.classList.contains('is-open');
                if (!isOpen) {
                    closeAll('profile'); profile.classList.add('is-open'); profileToggle.setAttribute('aria-expanded', 'true');
                    if (isMobile) { overlay.classList.add('is-visible'); document.body.style.overflow = 'hidden'; }
                } else { profile.classList.remove('is-open'); profileToggle.setAttribute('aria-expanded', 'false'); overlay.classList.remove('is-visible'); restoreBodyScroll(); }
            });
        }
        if (overlay) overlay.addEventListener('click', () => { if (isMobile && profile?.classList.contains('is-open')) { profile.classList.remove('is-open'); profileToggle?.setAttribute('aria-expanded', 'false'); overlay.classList.remove('is-visible'); restoreBodyScroll(); } });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeAll(); overlay?.classList.remove('is-visible'); restoreBodyScroll(); } });

        function initSubmenuAccordion(container) {
            if (!container) return;
            container.addEventListener('click', e => {
                if (!isMobile) return;
                const parentLink = e.target.closest('.has-children > a');
                if (!parentLink) return;
                const href = parentLink.getAttribute('href');
                if (href && href !== '#') return;
                e.preventDefault(); e.stopPropagation();
                const submenu = parentLink.nextElementSibling;
                if (submenu && submenu.classList.contains('submenu')) {
                    const isExpanded = parentLink.getAttribute('aria-expanded') === 'true';
                    const parentLi = parentLink.closest('.menu-item');
                    if (parentLi) {
                        Array.from(parentLi.parentElement.children).forEach(sib => {
                            if (sib !== parentLi) { const sl = sib.querySelector(':scope > a'); const ss = sib.querySelector(':scope > .submenu'); if (sl && ss) { sl.setAttribute('aria-expanded', 'false'); ss.style.display = 'none'; } }
                        });
                    }
                    parentLink.setAttribute('aria-expanded', !isExpanded);
                    submenu.style.display = isExpanded ? 'none' : 'block';
                }
            });
        }
        initSubmenuAccordion(nav); initSubmenuAccordion(profile);

        window.addEventListener('resize', () => {
            const wasMobile = isMobile; isMobile = window.innerWidth <= mobileBreakpoint;
            if (wasMobile && !isMobile) {
                profile?.classList.remove('is-open'); profileToggle?.setAttribute('aria-expanded', 'false'); overlay.classList.remove('is-visible');
                nav?.classList.remove('is-open'); burger?.classList.remove('is-active'); burger?.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
            }
        });
    }
    attachGlobalCloseHandler();
    document.querySelectorAll('[data-header]').forEach(initHeader);
    window.BloggyCMS = window.BloggyCMS || {}; window.BloggyCMS.hooks = window.BloggyCMS.hooks || {};
    window.BloggyCMS.hooks.contentUpdated = () => { document.querySelectorAll('[data-header]').forEach(h => delete h.dataset.headerInit); document.querySelectorAll('[data-header]').forEach(initHeader); };
})();