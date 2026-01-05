// Header functionality - vanilla JS replacement for Alpine

export function initHeader() {
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
    const searchToggleBtn = document.getElementById('search-toggle-btn');
    const searchBar = document.getElementById('search-bar');
    const headerInner = document.getElementById('header-inner');

    let isMenuOpen = false;
    let isSearchOpen = false;

    // Mobile menu toggle
    function toggleMobileMenu() {
        isMenuOpen = !isMenuOpen;
        
        if (isMenuOpen) {
            mobileMenu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
            mobileMenu.classList.add('opacity-100', 'scale-100');
            mobileMenu.setAttribute('aria-hidden', 'false');
            mobileMenuOverlay.classList.remove('hidden');
            mobileMenuBtn.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
            
            // Animate hamburger to X
            const lines = mobileMenuBtn.querySelectorAll('.menu-line');
            lines[0].classList.remove('-translate-y-2');
            lines[0].classList.add('rotate-45', 'translate-y-0');
            lines[1].classList.add('opacity-0');
            lines[2].classList.remove('translate-y-2');
            lines[2].classList.add('-rotate-45', 'translate-y-0');
        } else {
            closeMobileMenu();
        }
    }

    function closeMobileMenu() {
        isMenuOpen = false;
        if (!mobileMenu) return;
        
        mobileMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
        mobileMenu.classList.remove('opacity-100', 'scale-100');
        mobileMenu.setAttribute('aria-hidden', 'true');
        mobileMenuOverlay?.classList.add('hidden');
        mobileMenuBtn?.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        
        // Animate X back to hamburger
        const lines = mobileMenuBtn?.querySelectorAll('.menu-line');
        if (lines) {
            lines[0].classList.add('-translate-y-2');
            lines[0].classList.remove('rotate-45', 'translate-y-0');
            lines[1].classList.remove('opacity-0');
            lines[2].classList.add('translate-y-2');
            lines[2].classList.remove('-rotate-45', 'translate-y-0');
        }
    }

    // Search toggle
    function toggleSearch() {
        isSearchOpen = !isSearchOpen;
        
        if (isSearchOpen) {
            searchBar.classList.remove('opacity-0', '-translate-y-2', 'pointer-events-none');
            searchBar.classList.add('opacity-100', 'translate-y-0');
            searchBar.setAttribute('aria-hidden', 'false');
            searchToggleBtn.setAttribute('aria-expanded', 'true');
            // Focus the search input
            const input = searchBar.querySelector('input[type="search"], input[type="text"]');
            if (input) input.focus();
        } else {
            closeSearch();
        }
    }

    function closeSearch() {
        isSearchOpen = false;
        if (!searchBar) return;
        
        searchBar.classList.add('opacity-0', '-translate-y-2', 'pointer-events-none');
        searchBar.classList.remove('opacity-100', 'translate-y-0');
        searchBar.setAttribute('aria-hidden', 'true');
        searchToggleBtn?.setAttribute('aria-expanded', 'false');
    }

    // Scroll effect for header
    function handleScroll() {
        if (!headerInner) return;
        
        const hasScrolled = window.scrollY > 20;
        if (hasScrolled) {
            headerInner.classList.remove('lg:py-6');
            headerInner.classList.add('lg:py-3');
        } else {
            headerInner.classList.remove('lg:py-3');
            headerInner.classList.add('lg:py-6');
        }
    }

    // Submenu handling for nav dropdowns
    function initSubmenus() {
        const submenuToggles = document.querySelectorAll('.submenu-toggle');
        const submenuBtns = document.querySelectorAll('.submenu-btn');
        
        // Handle link clicks (toggle on mobile, navigate on desktop)
        submenuToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                const isMobile = window.innerWidth < 1024;
                const li = toggle.closest('.has-submenu');
                const submenu = li?.querySelector('.submenu');
                
                if (isMobile && submenu) {
                    e.preventDefault();
                    toggleSubmenu(li, submenu);
                } else {
                    // Desktop: navigate to href
                    const href = toggle.dataset.href || toggle.href;
                    if (href && href !== 'javascript:void(0)') {
                        window.location.href = href;
                    }
                }
            });
        });
        
        // Handle button clicks (mobile only)
        submenuBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const li = btn.closest('.has-submenu');
                const submenu = li?.querySelector('.submenu');
                if (submenu) {
                    toggleSubmenu(li, submenu);
                }
            });
        });
        
        // Close submenus when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.has-submenu')) {
                closeAllSubmenus();
            }
        });
    }
    
    function toggleSubmenu(li, submenu) {
        const isOpen = !submenu.classList.contains('hidden');
        
        // Close other submenus first
        closeAllSubmenus();
        
        if (!isOpen) {
            submenu.classList.remove('hidden');
            const btn = li.querySelector('.submenu-btn');
            const icon = li.querySelector('.submenu-icon');
            btn?.setAttribute('aria-expanded', 'true');
            icon?.classList.add('rotate-180');
        }
    }
    
    function closeAllSubmenus() {
        document.querySelectorAll('.submenu').forEach(submenu => {
            submenu.classList.add('hidden');
        });
        document.querySelectorAll('.submenu-btn').forEach(btn => {
            btn.setAttribute('aria-expanded', 'false');
        });
        document.querySelectorAll('.submenu-icon').forEach(icon => {
            icon.classList.remove('rotate-180');
        });
    }

    // Event listeners
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
    
    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', closeMobileMenu);
    }

    if (searchToggleBtn) {
        searchToggleBtn.addEventListener('click', toggleSearch);
    }

    // Click outside to close search
    document.addEventListener('click', (e) => {
        if (isSearchOpen && searchBar && !searchBar.contains(e.target) && !searchToggleBtn.contains(e.target)) {
            closeSearch();
        }
    });

    // Escape key to close menus
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (isMenuOpen) closeMobileMenu();
            if (isSearchOpen) closeSearch();
            closeAllSubmenus();
        }
    });

    // Scroll listener
    window.addEventListener('scroll', handleScroll, { passive: true });
    
    // Initial setup
    handleScroll();
    initSubmenus();
}
