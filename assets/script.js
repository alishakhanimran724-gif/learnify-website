// File: assets/js/main.js

// Global variables
let selectedBoard = '';
let selectedClass = '';
let selectedResourceType = '';

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize Application
function initializeApp() {
    // Initialize event listeners
    setupEventListeners();
    
    // Initialize animations
    initializeAnimations();
    
    // Setup modal handlers
    setupModalHandlers();
    
    // Setup mobile menu
    setupMobileMenu();
    
    // Setup form handlers
    setupFormHandlers();
}

// Setup Event Listeners
function setupEventListeners() {
    // Board selection functionality
    document.querySelectorAll('.board-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.board-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            selectedBoard = this.dataset.board;
        });
    });

    // Class selection in search
    const classSelect = document.getElementById('classSelect');
    if (classSelect) {
        classSelect.addEventListener('change', function() {
            loadSubjects(this.value);
        });
    }

    // Dropdown navigation handling
    setupDropdownNavigation();

    // Smooth scroll for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add ripple effect to interactive elements
    document.addEventListener('click', function(e) {
        if (e.target.matches('.explore-btn, .search-btn, .class-card, .resource-card, .subject-card, .resource-type-card')) {
            addRippleEffect(e);
        }
    });
}

// Setup Dropdown Navigation
function setupDropdownNavigation() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (toggle && menu) {
            // Handle mouse events
            dropdown.addEventListener('mouseenter', () => {
                menu.style.display = 'block';
                setTimeout(() => {
                    menu.classList.add('active');
                }, 10);
            });
            
            dropdown.addEventListener('mouseleave', () => {
                menu.classList.remove('active');
                setTimeout(() => {
                    if (!menu.classList.contains('active')) {
                        menu.style.display = 'none';
                    }
                }, 300);
            });
            
            // Handle mobile touch events
            toggle.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                    menu.classList.toggle('active');
                }
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.dropdown')) {
            dropdowns.forEach(dropdown => {
                const menu = dropdown.querySelector('.dropdown-menu');
                if (menu) {
                    menu.classList.remove('active');
                    setTimeout(() => {
                        if (!menu.classList.contains('active')) {
                            menu.style.display = 'none';
                        }
                    }, 300);
                }
            });
        }
    });
}

// Modal Functions
function openClassModal(boardName, boardId) {
    document.getElementById('modalTitle').textContent = `${boardName} - Select Your Class`;
    document.getElementById('classModal').classList.add('active');
    selectedBoard = boardId;
}

function closeClassModal() {
    document.getElementById('classModal').classList.remove('active');
}

function selectClass(classId, classNumber) {
    selectedClass = classId;
    closeClassModal();
    
    // Check if resource modal exists, if not redirect directly
    const resourceModal = document.getElementById('resourceModal');
    if (resourceModal) {
        document.getElementById('resourceModalTitle').textContent = 
            `Class ${classNumber} - Choose Resource Type`;
        resourceModal.classList.add('active');
    } else {
        // Redirect directly to search page with class and board
        window.location.href = `search.php?class_id=${selectedClass}&board_id=${selectedBoard}`;
    }
}

function closeResourceModal() {
    const resourceModal = document.getElementById('resourceModal');
    if (resourceModal) {
        resourceModal.classList.remove('active');
    }
}

function openResource(resourceType) {
    selectedResourceType = resourceType;
    closeResourceModal();
    
    // Redirect to resources page
    window.location.href = `search.php?class_id=${selectedClass}&board_id=${selectedBoard}&type=${resourceType}`;
}

// Setup Modal Handlers
function setupModalHandlers() {
    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('class-modal') || e.target.classList.contains('resource-modal')) {
            e.target.classList.remove('active');
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.class-modal, .resource-modal').forEach(modal => {
                modal.classList.remove('active');
            });
        }
    });
}

// Load Subjects based on Class
function loadSubjects(classId) {
    if (!classId) return;
    
    fetch(`ajax/get_subjects.php?class_id=${classId}`)
        .then(response => response.json())
        .then(subjects => {
            const subjectSelect = document.getElementById('subjectSelect');
            if (subjectSelect) {
                subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                subjects.forEach(subject => {
                    const option = document.createElement('option');
                    option.value = subject.id;
                    option.textContent = subject.subject_name;
                    subjectSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading subjects:', error);
        });
}

// Search Functions
function performSearch() {
    const classId = document.getElementById('classSelect')?.value;
    const boardId = document.getElementById('boardSelect')?.value;
    
    if (!classId || !boardId) {
        showNotification('Please select both class and board to search', 'warning');
        return;
    }
    
    // Redirect to search page
    window.location.href = `search.php?class_id=${classId}&board_id=${boardId}`;
}

function showSearch() {
    const searchSection = document.querySelector('.search-section');
    if (searchSection) {
        searchSection.scrollIntoView({
            behavior: 'smooth'
        });
    }
}

// Advanced Search with AJAX
function performAdvancedSearch(filters) {
    const queryParams = new URLSearchParams(filters).toString();
    
    fetch(`ajax/get_resources.php?${queryParams}`)
        .then(response => response.json())
        .then(resources => {
            displaySearchResults(resources);
        })
        .catch(error => {
            console.error('Error performing search:', error);
            showNotification('Error performing search. Please try again.', 'error');
        });
}

// Display Search Results
function displaySearchResults(resources) {
    const resultsContainer = document.getElementById('searchResults');
    if (!resultsContainer) return;
    
    if (resources.length === 0) {
        resultsContainer.innerHTML = `
            <div class="no-results">
                <i class="fas fa-info-circle"></i>
                <h3>No resources found</h3>
                <p>Try adjusting your search criteria.</p>
            </div>
        `;
        return;
    }
    
    const resourcesHTML = resources.map(resource => `
        <div class="resource-item">
            <img src="${resource.front_image || 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'}" 
                 alt="${resource.heading}" 
                 onerror="this.src='https://images.unsplash.com/photo-1543002588-bfa74002ed7e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">
            <div class="resource-content">
                <h4>${resource.heading}</h4>
                <p>${resource.description}</p>
                <div class="resource-meta">
                    <span class="meta-item">
                        <i class="fas fa-graduation-cap"></i> ${resource.class_name}
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-book"></i> ${resource.subject_name}
                    </span>
                    <span class="meta-item">
                        <i class="fas fa-building"></i> ${resource.board_name}
                    </span>
                </div>
                ${resource.type === 'video' ? 
                    `<a href="${resource.video_link}" target="_blank" class="resource-btn">
                        <i class="fas fa-play"></i> Watch Video
                    </a>` :
                    `<a href="download.php?id=${resource.id}" class="resource-btn">
                        <i class="fas fa-download"></i> Download
                    </a>`
                }
            </div>
        </div>
    `).join('');
    
    resultsContainer.innerHTML = resourcesHTML;
}

// Mobile Menu Setup
function setupMobileMenu() {
    const mobileMenu = document.querySelector('.mobile-menu');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenu && navLinks) {
        mobileMenu.addEventListener('click', function() {
            navLinks.classList.toggle('active');
        });
    }
}

// Form Handlers
function setupFormHandlers() {
    // Search form handler
    const searchForm = document.querySelector('.search-grid');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
    }
    
    // Filter form handler
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const filters = Object.fromEntries(formData);
            performAdvancedSearch(filters);
        });
    }
}

// Animation Functions
function initializeAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Initialize animations on load
    window.addEventListener('load', function() {
        // Observe animated elements
        document.querySelectorAll('.board-card, .feature-card, .stat-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });

        // Staggered animation for board cards
        document.querySelectorAll('.board-card').forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 200);
        });
    });
}

// Ripple Effect
function addRippleEffect(e) {
    const ripple = document.createElement('span');
    const rect = e.target.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple 0.6s linear;
        left: ${x}px;
        top: ${y}px;
        width: ${size}px;
        height: ${size}px;
        pointer-events: none;
    `;
    
    // Add ripple styles if not exists
    if (!document.querySelector('#ripple-styles')) {
        const style = document.createElement('style');
        style.id = 'ripple-styles';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Set position relative if needed
    if (e.target.style.position !== 'absolute' && e.target.style.position !== 'relative') {
        e.target.style.position = 'relative';
    }
    e.target.style.overflow = 'hidden';
    
    e.target.appendChild(ripple);
    
    setTimeout(() => {
        if (ripple.parentNode) {
            ripple.parentNode.removeChild(ripple);
        }
    }, 600);
}

// Notification System
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
            <span>${message}</span>
            <button class="notification-close">&times;</button>
        </div>
    `;
    
    // Add styles for notification
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 100px;
                right: 20px;
                z-index: 9999;
                background: white;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                animation: slideInRight 0.3s ease-out;
                max-width: 400px;
            }
            .notification-content {
                padding: 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }
            .notification-error { border-left: 4px solid #ef4444; }
            .notification-warning { border-left: 4px solid #f59e0b; }
            .notification-info { border-left: 4px solid #3b82f6; }
            .notification-close {
                background: none;
                border: none;
                font-size: 1.2rem;
                cursor: pointer;
                margin-left: auto;
                color: #6b7280;
            }
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideInRight 0.3s ease-out reverse';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
    
    // Close button handler
    notification.querySelector('.notification-close').addEventListener('click', () => {
        notification.style.animation = 'slideInRight 0.3s ease-out reverse';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    });
}

// Utility Functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Loading state management
function showLoading(element) {
    if (element) {
        element.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        element.disabled = true;
    }
}

function hideLoading(element, originalText) {
    if (element) {
        element.innerHTML = originalText;
        element.disabled = false;
    }
}
document.getElementById("class_id").addEventListener("change", function () {
    const classId = this.value;
    const boardId = document.getElementById("board_id").value;

    if (classId && boardId) {
        fetch("ajax/get_subjects.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `class_id=${classId}&board_id=${boardId}`
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById("subject_id").innerHTML = data;
        })
        .catch(error => {
            console.error("Error loading subjects:", error);
        });
    }
});
