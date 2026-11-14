/**
 * Digiws - Admin JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    // Delete Confirmation
    const deleteForms = document.querySelectorAll('form[action*="/delete"]');
    deleteForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            if(!confirm('Are you sure you want to perform this delete action? This cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Sidebar Active State Auto-scroll
    const activeLink = document.querySelector('.sidebar-link.active');
    if (activeLink) {
        activeLink.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Image Preview for File Inputs
    const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
    imageInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    let previewDiv = this.previousElementSibling;
                    if (previewDiv && previewDiv.classList.contains('mb-2')) {
                        const img = previewDiv.querySelector('img');
                        if (img) img.src = e.target.result;
                    } else {
                        previewDiv = document.createElement('div');
                        previewDiv.className = 'mb-2';
                        previewDiv.innerHTML = `<img src="${e.target.result}" style="height:80px;border-radius:8px;">`;
                        this.parentNode.insertBefore(previewDiv, this);
                    }
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Sidebar Collapse / Expand via Logo
    const sidebar = document.getElementById('adminSidebar');
    const brandBtn = document.getElementById('sidebarBrandBtn');
    const overlay = document.getElementById('sidebarOverlay');
    const STORAGE_KEY = 'digiws_sidebar_collapsed';

    function updateMainMargin() {
        const main = document.querySelector('.admin-main');
        if (!main) return;
        const width = sidebar.classList.contains('collapsed')
            ? getComputedStyle(document.documentElement).getPropertyValue('--sidebar-collapsed-width').trim()
            : getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width').trim();
        main.style.marginLeft = width;
        main.style.transition = 'margin-left 0.22s cubic-bezier(0.16, 1, 0.3, 1)';
    }

    function collapseSidebar() {
        sidebar.classList.add('collapsed');
        brandBtn.setAttribute('title', 'Expand Menu');
        brandBtn.setAttribute('aria-label', 'Expand sidebar');
        localStorage.setItem(STORAGE_KEY, '1');
        updateMainMargin();
    }

    function expandSidebar() {
        sidebar.classList.remove('collapsed');
        brandBtn.setAttribute('title', 'Collapse Menu');
        brandBtn.setAttribute('aria-label', 'Collapse sidebar');
        localStorage.setItem(STORAGE_KEY, '0');
        updateMainMargin();
    }

    function openMobileSidebar() {
        sidebar.classList.add('show');
        if (overlay) overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (brandBtn) {
        brandBtn.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                if (sidebar.classList.contains('show')) {
                    closeMobileSidebar();
                } else {
                    openMobileSidebar();
                }
            } else {
                if (sidebar.classList.contains('collapsed')) {
                    expandSidebar();
                } else {
                    collapseSidebar();
                }
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    // Restore state
    const saved = localStorage.getItem(STORAGE_KEY);
    if (window.innerWidth < 1024) {
        closeMobileSidebar();
    } else if (saved === '1') {
        collapseSidebar();
    } else {
        expandSidebar();
    }

    // On resize, ensure consistency
    window.addEventListener('resize', () => {
        if (window.innerWidth < 1024) {
            closeMobileSidebar();
        } else {
            if (saved === '1') {
                collapseSidebar();
            } else {
                expandSidebar();
            }
        }
        updateMainMargin();
    });
});
