/* =========================
   Property Show Page JavaScript
========================= */

// Image Modal Variables
let currentImageIndex = 0;
let imageSources = [];
let totalImages = 0;

// Image Modal Functions
function openImageModal(imageSrc) {
    // Get all image sources from the gallery
    const galleryImages = document.querySelectorAll('.gallery-image');
    imageSources = Array.from(galleryImages).map(img => img.src);
    totalImages = imageSources.length;
    
    // Find the index of the clicked image
    currentImageIndex = imageSources.indexOf(imageSrc);
    
    // If image not found in gallery, add it to the gallery (but check for duplicates first)
    if (currentImageIndex === -1) {
        // Check if the image is already in the array to avoid duplicates
        if (!imageSources.includes(imageSrc)) {
            imageSources.push(imageSrc);
            totalImages = imageSources.length;
            currentImageIndex = totalImages - 1;
        } else {
            // If it's already there, find its index
            currentImageIndex = imageSources.indexOf(imageSrc);
        }
    }
    
    // Show modal first
    document.getElementById('imageModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Update modal content (this will show the correct image)
    updateModalImage();
    updateImageCounter();
    updateNavigationButtons();
    createThumbnails();
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function updateModalImage() {
    const modalImage = document.getElementById('modalImage');
    modalImage.src = imageSources[currentImageIndex];
    
    // Add loading effect
    modalImage.style.opacity = '0.7';
    modalImage.onload = function() {
        this.style.opacity = '1';
    };
}

function updateImageCounter() {
    document.getElementById('currentImageIndex').textContent = currentImageIndex + 1;
    document.getElementById('totalImages').textContent = totalImages;
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    prevBtn.disabled = currentImageIndex === 0;
    nextBtn.disabled = currentImageIndex === totalImages - 1;
}

function createThumbnails() {
    const thumbnailStrip = document.getElementById('thumbnailStrip');
    thumbnailStrip.innerHTML = '';
    
    imageSources.forEach((src, index) => {
        const thumbnailItem = document.createElement('div');
        thumbnailItem.className = `thumbnail-item ${index === currentImageIndex ? 'active' : ''}`;
        thumbnailItem.onclick = () => goToImage(index);
        
        const img = document.createElement('img');
        img.src = src;
        img.alt = `Thumbnail ${index + 1}`;
        
        thumbnailItem.appendChild(img);
        thumbnailStrip.appendChild(thumbnailItem);
    });
}

function goToImage(index) {
    if (index >= 0 && index < totalImages) {
        currentImageIndex = index;
        updateModalImage();
        updateImageCounter();
        updateNavigationButtons();
        updateThumbnailSelection();
    }
}

function updateThumbnailSelection() {
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    thumbnails.forEach((thumb, index) => {
        thumb.classList.toggle('active', index === currentImageIndex);
    });
}

function nextImage() {
    if (currentImageIndex < totalImages - 1) {
        goToImage(currentImageIndex + 1);
    }
}

function previousImage() {
    if (currentImageIndex > 0) {
        goToImage(currentImageIndex - 1);
    }
}

// Close modal when clicking outside the image
document.addEventListener('click', function(event) {
    const modal = document.getElementById('imageModal');
    if (event.target === modal) {
        closeImageModal();
    }
});

// Keyboard navigation
document.addEventListener('keydown', function(event) {
    const modal = document.getElementById('imageModal');
    if (modal.style.display === 'flex') {
        switch(event.key) {
            case 'Escape':
                closeImageModal();
                break;
            case 'ArrowLeft':
                event.preventDefault();
                previousImage();
                break;
            case 'ArrowRight':
                event.preventDefault();
                nextImage();
                break;
            case ' ':
                event.preventDefault();
                nextImage();
                break;
        }
    }
});

// Like functionality
function initLikeButton() {
    const likeBtn = document.querySelector('.like-btn');
    if (likeBtn) {
        likeBtn.addEventListener('click', async function() {
            const propertyId = this.getAttribute('data-property-id');
            const heartIcon = this.querySelector('i');
            const likeText = this.querySelector('span');
            const likeCount = document.querySelector('.like-count');
            
            // Add loading state
            this.classList.add('loading');
            this.disabled = true;
            
            try {
                const response = await fetch(`/properties/${propertyId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const data = await response.json();
                
                if (data.status === 'liked') {
                    heartIcon.className = 'fa-solid fa-heart';
                    likeText.textContent = 'Liked';
                    this.setAttribute('data-liked', 'true');
                    
                    // Add animation effect
                    this.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 200);
                } else {
                    heartIcon.className = 'fa-regular fa-heart';
                    likeText.textContent = 'Like';
                    this.setAttribute('data-liked', 'false');
                }
                
                if (likeCount) {
                    likeCount.textContent = `${data.count} likes`;
                }
            } catch (error) {
                console.error('Error toggling like:', error);
                
                // Show error message to user
                const errorMsg = document.createElement('div');
                errorMsg.className = 'error-message';
                errorMsg.textContent = 'Failed to update like. Please try again.';
                errorMsg.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #ff6b6b;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 10px;
                    z-index: 1000;
                    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
                `;
                document.body.appendChild(errorMsg);
                
                // Remove error message after 3 seconds
                setTimeout(() => {
                    errorMsg.remove();
                }, 3000);
            } finally {
                // Remove loading state
                this.classList.remove('loading');
                this.disabled = false;
            }
        });
    }
}

// Contact button functionality
function initContactButtons() {
    const contactBtns = document.querySelectorAll('.contact-btn');
    
    contactBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (this.classList.contains('primary')) {
                // Call Now functionality
                showContactModal('call');
            } else if (this.classList.contains('secondary')) {
                // Send Message functionality
                showContactModal('message');
            }
        });
    });
}

// Show contact modal (placeholder for future implementation)
function showContactModal(type) {
    const modal = document.createElement('div');
    modal.className = 'contact-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    `;
    
    const content = document.createElement('div');
    content.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 20px;
        text-align: center;
        max-width: 400px;
        width: 90%;
    `;
    
    if (type === 'call') {
        content.innerHTML = `
            <h3>Call Property Owner</h3>
            <p>This feature will be implemented soon. For now, please contact us through other means.</p>
            <button onclick="this.closest('.contact-modal').remove()" style="
                background: #667eea;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 10px;
                cursor: pointer;
                margin-top: 20px;
            ">Close</button>
        `;
    } else {
        content.innerHTML = `
            <h3>Send Message</h3>
            <p>This feature will be implemented soon. For now, please contact us through other means.</p>
            <button onclick="this.closest('.contact-modal').remove()" style="
                background: #667eea;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 10px;
                cursor: pointer;
                margin-top: 20px;
            ">Close</button>
        `;
    }
    
    modal.appendChild(content);
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

// Smooth scroll for back button
function initBackButton() {
    const backBtn = document.querySelector('.back-btn');
    if (backBtn) {
        backBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add loading state
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Going back...';
            this.disabled = true;
            
            // Use history.back() with a small delay for better UX
            setTimeout(() => {
                history.back();
            }, 300);
        });
    }
}

// Initialize image gallery with keyboard navigation
function initImageGallery() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    galleryItems.forEach((item, index) => {
        item.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const img = this.querySelector('img');
                if (img) {
                    openImageModal(img.src);
                }
            }
        });
        
        // Make gallery items focusable
        item.setAttribute('tabindex', '0');
        item.setAttribute('role', 'button');
        item.setAttribute('aria-label', `View image ${index + 1}`);
    });
}

// Add loading states to action buttons
function initActionButtons() {
    const actionBtns = document.querySelectorAll('.action-btn');
    
    actionBtns.forEach(btn => {
        if (btn.classList.contains('delete-btn')) {
            btn.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this property?')) {
                    e.preventDefault();
                    return false;
                }
                
                // Add loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                this.disabled = true;
                
                // Re-enable after 3 seconds if form doesn't submit
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 3000);
            });
        }
    });
}

// Initialize all functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initLikeButton();
    initContactButtons();
    initBackButton();
    initImageGallery();
    initActionButtons();
    
    // Add fade-in animation to main content
    const mainContent = document.querySelector('.property-details');
    if (mainContent) {
        mainContent.style.opacity = '0';
        mainContent.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            mainContent.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            mainContent.style.opacity = '1';
            mainContent.style.transform = 'translateY(0)';
        }, 100);
    }
});

// Export functions for global access
window.openImageModal = openImageModal;
window.closeImageModal = closeImageModal;
window.nextImage = nextImage;
window.previousImage = previousImage;
