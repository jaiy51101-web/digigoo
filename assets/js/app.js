/**
 * Digiws - Frontend Main JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    // Smart Sticky Header removed to prevent layout fluctuation
    // Header sticking will be handled purely by CSS

    // Scroll Reveal Observer
    const revealOption = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    };

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('dc-revealed');
                observer.unobserve(entry.target);
            }
        });
    }, revealOption);

    document.querySelectorAll('.dc-reveal, .dc-reveal-stagger').forEach(el => {
        revealObserver.observe(el);
    });

    // --- Hero Parallax Animation ---
    const heroCol = document.getElementById('heroAnimCol');
    if (heroCol && window.innerWidth > 992) {
        window.addEventListener('scroll', () => {
            const scrollPercent = window.scrollY / 500;
            if (scrollPercent <= 1.2) {
                const yPos = scrollPercent * 150;
                const scale = 1 - (scrollPercent * 0.2);
                const opacity = 1 - (scrollPercent * 1);
                heroCol.style.transform = `translateY(${yPos}px) scale(${scale})`;
                heroCol.style.opacity = Math.max(0, opacity);
            }
        }, { passive: true });

        // --- Mouse Parallax for Floating Boxes ---
        document.querySelector('.dc-hero').addEventListener('mousemove', (e) => {
            const boxes = document.querySelectorAll('.dc-hero-stat-float');
            const { clientX, clientY } = e;
            const centerX = window.innerWidth / 2;
            const centerY = window.innerHeight / 2;

            boxes.forEach((box, index) => {
                const depth = (index + 1) * 15;
                const moveX = (clientX - centerX) / depth;
                const moveY = (clientY - centerY) / depth;
                box.style.marginLeft = `${moveX}px`;
                box.style.marginTop = `${moveY}px`;
            });
        });
    }

    // --- Add to Cart AJAX ---
    const addToCartForms = document.querySelectorAll('#addToCartForm');
    const addToCartBtns = document.querySelectorAll('.add-to-cart-btn');

    // From product single page
    if (addToCartForms.length) {
        addToCartForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                processAddToCart(new FormData(form), form.querySelector('button[type="submit"]'));
            });
        });
    }

    // From product cards
    if (addToCartBtns.length) {
        addToCartBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = btn.getAttribute('data-product-id');
                const formData = new FormData();
                formData.append('product_id', productId);
                processAddToCart(formData, btn);
            });
        });
    }


    function processAddToCart(formData, btnElement) {
        const originalContent = btnElement.innerHTML;
        btnElement.innerHTML = '<span class="dc-spinner"></span>';
        btnElement.disabled = true;

        fetch(window.appUrl('cart/add'), {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                updateCartBadge(data.cart_count);
                
                // If buy now, redirect to checkout immediately
                if (formData.get('buy_now') === '1') {
                    window.location.href = window.appUrl('checkout');
                    return;
                }

                showToast('success', data.message || 'Added to cart successfully!');
                
                // Change button text temporarily
                btnElement.innerHTML = '<i class="bi bi-check-lg"></i> Added';
                setTimeout(() => {
                    btnElement.innerHTML = originalContent;
                    btnElement.disabled = false;
                }, 2000);
            } else {
                showToast('danger', data.message || 'Failed to add to cart.');
                btnElement.innerHTML = originalContent;
                btnElement.disabled = false;
            }
        })
        .catch(err => {
            console.error('Add to cart error:', err);
            showToast('danger', 'An error occurred. Please try again.');
            btnElement.innerHTML = originalContent;
            btnElement.disabled = false;
        });
    }


    function updateCartBadge(count) {
        let badge = document.getElementById('cartBadge');
        const cartBtn = document.getElementById('cartBtn');
        
        if (count > 0) {
            if (!badge && cartBtn) {
                badge = document.createElement('span');
                badge.className = 'badge';
                badge.id = 'cartBadge';
                cartBtn.appendChild(badge);
            }
            if (badge) badge.innerText = count;
        } else if (badge) {
            badge.remove();
        }
    }

    // --- Wishlist Toggle AJAX ---
    const wishlistBtns = document.querySelectorAll('.wishlist-btn-pill');
    if (wishlistBtns.length) {
        wishlistBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = btn.getAttribute('data-product-id');
                const icon = btn.querySelector('i');
                
                fetch(window.appUrl('api/wishlist/toggle'), {
                    method: 'POST',
                    body: JSON.stringify({ product_id: parseInt(productId) }),
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest' 
                    }
                })
                .then(res => {
                    if (res.status === 401) {
                        window.location.href = window.appUrl('login');
                        return Promise.reject('login_required');
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        if (data.status === 'added') {
                            btn.classList.add('active');
                            icon.classList.replace('bi-heart', 'bi-heart-fill');
                        } else {
                            btn.classList.remove('active');
                            icon.classList.replace('bi-heart-fill', 'bi-heart');
                        }
                        showToast('success', data.message);
                        updateWishlistBadge(data.count);
                    } else {
                        showToast('danger', data.message);
                    }
                })
                .catch(err => {
                    console.error('Wishlist error:', err);
                });
            });
        });
    }

    function updateWishlistBadge(count) {
        let badge = document.getElementById('wishlistBadge');
        const wishlistBtn = document.getElementById('wishlistBtn');
        
        if (count > 0) {
            if (!badge && wishlistBtn) {
                badge = document.createElement('span');
                badge.className = 'badge';
                badge.id = 'wishlistBadge';
                wishlistBtn.appendChild(badge);
            }
            if (badge) badge.innerText = count;
        } else if (badge) {
            badge.remove();
        }
    }

    // --- Live Search ---
    const searchInputs = document.querySelectorAll('#searchInput, .mobile-search');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    searchInputs.forEach(input => {
        input.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                if (searchResults) searchResults.classList.remove('show');
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(window.appUrl('api/products/search?q=' + encodeURIComponent(query)))
                .then(res => res.json())
                .then(data => {
                    const currentResults = input.nextElementSibling && input.nextElementSibling.classList.contains('search-results') 
                        ? input.nextElementSibling 
                        : input.closest('.dc-search').querySelector('.search-results');

                    if (data.products && data.products.length > 0 && currentResults) {
                        currentResults.innerHTML = '';
                        data.products.forEach(p => {
                            currentResults.innerHTML += `
                                <div class="search-item" onclick="window.location.href='${p.url}'">
                                    <img src="${p.thumb_url}" alt="">
                                    <div class="info">
                                        <h6>${p.title}</h6>
                                        <small>${p.formatted_price}</small>
                                    </div>
                                </div>
                            `;
                        });
                        currentResults.classList.add('show');
                    } else if (currentResults) {
                        currentResults.innerHTML = '<div class="p-3 text-center text-muted small">No products found.</div>';
                        currentResults.classList.add('show');
                    }
                });
            }, 400);
        });
    });

    // Close search results on click outside
    document.addEventListener('click', (e) => {
        if (searchResults && !e.target.closest('.dc-search')) {
            searchResults.classList.remove('show');
        }
    });

    // --- Mobile Drawer Logic ---
    const mobileToggle = document.getElementById('mobileMenuToggle');
    const closeDrawer = document.getElementById('closeDrawer');
    const drawerOverlay = document.getElementById('drawerOverlay');
    const mobileDrawer = document.getElementById('mobileDrawer');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            document.body.classList.add('menu-open');
        });
    }

    if (closeDrawer) {
        closeDrawer.addEventListener('click', () => {
            document.body.classList.remove('menu-open');
        });
    }

    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', () => {
            document.body.classList.remove('menu-open');
        });
    }

    // --- Toast Notification Helper ---
    function showToast(type, message) {
        let container = document.querySelector('.dc-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'dc-toast-container';
            document.body.appendChild(container);
        }

        const icon = type === 'success' ? 'check-circle-fill' : (type === 'danger' ? 'x-circle-fill' : 'exclamation-circle-fill');
        
        const toast = document.createElement('div');
        toast.className = `dc-toast ${type}`;
        toast.innerHTML = `<i class="bi bi-${icon}"></i><span>${message}</span>`;
        container.appendChild(toast);

        setTimeout(() => {
            toast.style.transition = 'opacity 0.5s';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }
    
    // Make showToast globally available
    window.showToast = showToast;
});
