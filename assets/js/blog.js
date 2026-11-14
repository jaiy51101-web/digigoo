/**
 * DIGIWS Blog Module - Public JS Functions
 */
document.addEventListener("DOMContentLoaded", function() {
    
    // ==========================================
    // 1. Reading Progress Bar & Back to Top
    // ==========================================
    const progress = document.getElementById('readingProgress');
    const mainContent = document.getElementById('postMainContent');
    const rpFill = document.getElementById('rpBarFill');
    const rpPercent = document.getElementById('rpPercent');

    function updateProgress() {
        if (!progress && !rpFill) return;
        
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;
        const scrolled = window.scrollY;
        
        if (docHeight > 0) {
            const percentage = Math.min(100, Math.round((scrolled / docHeight) * 100));
            
            if (progress) {
                progress.style.width = percentage + '%';
            }
            if (rpFill) {
                rpFill.style.width = percentage + '%';
            }
            if (rpPercent) {
                rpPercent.textContent = percentage + '%';
            }
        }
    }
    
    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();

    // ==========================================
    // 2. Auto-sliding Category Carousel
    // ==========================================
    const categoryCarousel = document.getElementById('categoryCarousel');
    if (categoryCarousel) {
        let autoSlideInterval;
        let isPaused = false;
        
        function autoSlide() {
            if (isPaused) return;
            
            const cardWidth = categoryCarousel.querySelector('.cat-card')?.offsetWidth + 16 || 156;
            const maxScroll = categoryCarousel.scrollWidth - categoryCarousel.clientWidth;
            
            if (categoryCarousel.scrollLeft >= maxScroll) {
                categoryCarousel.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                categoryCarousel.scrollBy({ left: cardWidth, behavior: 'smooth' });
            }
        }
        
        // Start auto-slide (every 3 seconds)
        autoSlideInterval = setInterval(autoSlide, 3000);
        
        // Pause on hover
        categoryCarousel.addEventListener('mouseenter', () => { isPaused = true; });
        categoryCarousel.addEventListener('mouseleave', () => { isPaused = false; });
        
        // Pause on touch
        categoryCarousel.addEventListener('touchstart', () => { isPaused = true; });
        categoryCarousel.addEventListener('touchend', () => { isPaused = false; });
    }

    // ==========================================
    // 3. AJAX Live Search Suggestion
    // ==========================================
    const searchInput = document.getElementById('blogSearchInput');
    const liveResults = document.getElementById('blogLiveResults');

    if (searchInput && liveResults) {
        let debounceTimer;

        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();

            if (query.length < 2) {
                liveResults.innerHTML = '';
                liveResults.style.display = 'none';
                return;
            }

            debounceTimer = setTimeout(() => {
                fetch(window.appUrl('api/blog/search?q=' + encodeURIComponent(query)))
                    .then(res => res.json())
                    .then(data => {
                        if (data.posts && data.posts.length > 0) {
                            let html = '<ul class="blog-live-search-list m-0 p-0" style="list-style:none;">';
                            data.posts.forEach(post => {
                                html += `
                                <li class="p-2 border-bottom hover-bg" style="cursor:pointer;">
                                    <a href="${post.url}" class="d-flex align-items-center text-decoration-none gap-2 text-dark">
                                        ${post.image ? `<img src="${post.image}" style="width:36px; height:36px; object-fit:cover; border-radius:4px;">` : `<div style="width:36px; height:36px; background:#e2e8f0; border-radius:4px; display:flex; align-items:center; justify-content:center;"><i class="bi bi-file-earmark-text"></i></div>`}
                                        <div>
                                            <span class="d-block fw-bold small" style="line-height:1.2;">${post.title}</span>
                                            <span class="text-muted" style="font-size:0.75rem;">${post.category ? post.category : 'General'} · ${post.date}</span>
                                        </div>
                                    </a>
                                </li>`;
                            });
                            html += '</ul>';
                            liveResults.innerHTML = html;
                            liveResults.style.display = 'block';
                        } else {
                            liveResults.innerHTML = '<div class="p-3 text-muted small text-center">No results found</div>';
                            liveResults.style.display = 'block';
                        }
                    })
                    .catch(err => console.error("Live search failed:", err));
            }, 300);
        });

        // Close search list on click outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !liveResults.contains(e.target)) {
                liveResults.innerHTML = '';
                liveResults.style.display = 'none';
            }
        });
    }

    // ==========================================
    // 3. Highlight TOC link on scroll
    // ==========================================
    const headings = document.querySelectorAll('.post-content-body h2, .post-content-body h3');
    const tocLinks = document.querySelectorAll('.toc-list a');

    if (headings.length > 0 && tocLinks.length > 0) {
        function highlightTOC() {
            let activeId = null;
            const scrollPos = window.scrollY + 120;

            headings.forEach(heading => {
                if (scrollPos >= heading.offsetTop) {
                    activeId = heading.getAttribute('id');
                }
            });

            tocLinks.forEach(link => {
                link.classList.remove('active-toc-link');
                if (link.getAttribute('href') === '#' + activeId) {
                    link.classList.add('active-toc-link');
                }
            });
        }
        
        window.addEventListener('scroll', highlightTOC, { passive: true });
        highlightTOC();
    }
});

// Inline Styles dynamically added for active TOC and search suggestions
const style = document.createElement('style');
style.textContent = `
.active-toc-link {
    font-weight: 700 !important;
    color: #166534 !important;
    text-decoration: underline !important;
}
.blog-live-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    margin-top: 8px;
    z-index: 999;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    max-height: 280px;
    overflow-y: auto;
    display: none;
}
.blog-live-search-list li:hover {
    background: #f8fafc;
}
`;
document.head.appendChild(style);

// Open Reply comment form
function openReplyForm(commentId, authorName) {
    const commentItem = document.getElementById('comment-' + commentId);
    if (!commentItem) return;
    
    // Check if reply form already exists
    let existingForm = document.getElementById('reply-form-' + commentId);
    if (existingForm) {
        existingForm.remove();
        return;
    }
    
    // Remove any other reply forms
    document.querySelectorAll('.reply-dynamic-form').forEach(form => form.remove());
    
    const replyFormHtml = `
    <form class="reply-dynamic-form mt-3 p-3 rounded-3 bg-light border" id="reply-form-${commentId}">
        <input type="hidden" name="post_id" value="${document.querySelector('#commentSubmitForm input[name="post_id"]').value}">
        <input type="hidden" name="parent_id" value="${commentId}">
        <input type="hidden" name="csrf_token" value="${document.querySelector('#commentSubmitForm input[name="csrf_token"]').value}">
        
        <span class="d-block small text-muted mb-2">Replying to <strong>${authorName}</strong>:</span>
        
        ${!document.querySelector('#commentSubmitForm input[name="guest_name"]') ? '' : `
        <div class="row g-2 mb-2">
            <div class="col-sm-6">
                <input type="text" name="guest_name" class="form-control form-control-sm rounded-3" placeholder="Your Name" required>
            </div>
            <div class="col-sm-6">
                <input type="email" name="guest_email" class="form-control form-control-sm rounded-3" placeholder="Email (Optional)">
            </div>
        </div>`}
        
        <div class="form-group mb-2">
            <textarea name="content" class="form-control form-control-sm rounded-3" rows="2" placeholder="Write reply..." required></textarea>
        </div>
        
        <div class="text-end">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill me-2" onclick="document.getElementById('reply-form-${commentId}').remove()">Cancel</button>
            <button type="submit" class="btn btn-sm btn-primary rounded-pill">Submit Reply</button>
        </div>
        <div class="reply-response-${commentId} mt-1 small"></div>
    </form>`;
    
    const commentBody = commentItem.querySelector('.comment-body');
    commentBody.insertAdjacentHTML('beforeend', replyFormHtml);
    
    // Attach event listener
    const replyForm = document.getElementById('reply-form-' + commentId);
    replyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const respDiv = replyForm.querySelector('.reply-response-' + commentId);
        respDiv.textContent = "Submitting reply...";
        respDiv.className = `reply-response-${commentId} text-muted mt-1 small`;
        
        const formData = new FormData(replyForm);
        fetch(window.appUrl('blog/comment/store'), {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                respDiv.textContent = data.message;
                respDiv.className = `reply-response-${commentId} text-success mt-1 small`;
                setTimeout(() => replyForm.remove(), 2000);
            } else {
                respDiv.textContent = data.message || "Failed to submit reply.";
                respDiv.className = `reply-response-${commentId} text-danger mt-1 small`;
            }
        })
        .catch(err => {
            respDiv.textContent = "Error occurred. Try again.";
            respDiv.className = `reply-response-${commentId} text-danger mt-1 small`;
        });
    });
}
