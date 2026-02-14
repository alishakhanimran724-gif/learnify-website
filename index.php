<?php
include 'config/db.php';
include 'includes/header.php';

// Fetch boards and classes with error handling
try {
    $boards = $pdo->query("SELECT * FROM boards ORDER BY board_name")->fetchAll();
    $classes = $pdo->query("SELECT * FROM classes ORDER BY class_name")->fetchAll();
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $boards = [];
    $classes = [];
}
?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-subtitle">A Better Learning Journey</div>
            <h1>Future Starts Here</h1>
            <p>Access books, video lectures, past papers, notes, and solutions - organized by class, subject, and board.</p>
            <div class="hero-actions">
                <button class="search-btn" onclick="showSearch()">
                    Browse Material<i class="fa-solid fa-arrow-right"></i>
                </button>
                
                <button class="search-btn" style="background: #00027a;border: 1px solid white;" onclick="showSearch()">
                   Search Resources<i class="fa-solid fa-arrow-right"></i> 
                </button>
            </div>
        </div>
        <div class="hero-image">
            <div class="hero-circle">
                <img src="https://i.pinimg.com/736x/3f/b2/62/3fb262c2961a50eb1fa88102cc80d679.jpg" alt="Students learning together" loading="lazy">
            
            </div><div class="img-content">
              <p>Search by Class, Subject or Board</p>
              <a href="search.php" class="btn"
                >Search Now <i class="fa-solid fa-magnifying-glass"></i
              ></a>
            </div>
        </div>
        
    </section>

    

    <!-- Board Selection -->
    <section class="board-selection">
        <h2 class="section-title">Choose Your Board</h2>
        <p class="section-subtitle">Select your board to access all study materials organized by class and subject</p>

        <div class="board-grid">
            <?php if (empty($boards)): ?>
                <div class="no-boards-message">
                    <p>No boards available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <?php foreach($boards as $board): ?>
                <div class="board-card" data-board="<?= htmlspecialchars($board['board_name']) ?>">
                    <div class="board-header">
                        
                        <h3><?= htmlspecialchars($board['board_name']) ?></h3>
                    </div>
                    <p><?= htmlspecialchars($board['board_name']) ?> Board Resources</p>
                    <div class="class-tags">
                        
                        <?php foreach($classes as $class): ?>
                            <span class="class-tag"><?= htmlspecialchars($class['class_name']) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="resource-tags">
                        <span class="resource-tag video">Video Lectures</span>
                        <span class="resource-tag pdf">PDF Books</span>
                        <span class="resource-tag test">Past Papers</span>
                        <span class="resource-tag notes">Notes & PPTs</span>
                    </div>
                    <button class="explore-btn" onclick="openClassModal('<?= htmlspecialchars($board['board_name']) ?>', <?= htmlspecialchars($board['id']) ?>)">
                        <i class="fas fa-arrow-right"></i> Explore <?= htmlspecialchars($board['board_name']) ?>
                    </button>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Class Selection Modal -->
    <div class="class-modal" id="classModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Select Your Class</h3>
                <button class="close-modal" onclick="closeClassModal()" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="class-grid">
                <?php foreach($classes as $class): ?>
                <button class="class-card" onclick="selectClass(<?= htmlspecialchars($class['id']) ?>, '<?= htmlspecialchars($class['class_name']) ?>')">
                    <h4><?= htmlspecialchars($class['class_name']) ?></h4>
                    <p>Study Materials for <?= htmlspecialchars($class['class_name']) ?></p>
                </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Resource Type Selection Modal -->
    <div class="resource-modal" id="resourceModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="resourceModalTitle">Choose Resource Type</h3>
                <button class="close-modal" onclick="closeResourceModal()" aria-label="Close modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="resource-grid">
                <button class="resource-card" onclick="openResource('notes')">
                    <div class="resource-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h4>Notes</h4>
                </button>
                <button class="resource-card" onclick="openResource('pdf')">
                    <div class="resource-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4>Books</h4>
                </button>
                <button class="resource-card" onclick="openResource('test')">
                    <div class="resource-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>Past Papers</h4>
                </button>
                <button class="resource-card" onclick="openResource('video')">
                    <div class="resource-icon">
                        <i class="fas fa-play"></i>
                    </div>
                    <h4>Video Lectures</h4>
                </button>
                <button class="resource-card" onclick="openResource('')">
                    <div class="resource-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h4>Online Test</h4>
                </button>
                <button class="resource-card" onclick="openResource('test')">
                    <div class="resource-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h4>Guess Papers</h4>
                </button>
            </div>
        </div>
    </div>

    

    <!-- Why Choose Section -->
    <section class="why-choose">
        <h2>Why Choose EduBoard?</h2>
        <p>Everything you need for academic success in one place</p>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-download"></i>
                </div>
                <h3>Free Downloads</h3>
                <p>All educational resources are completely free to download and use</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3>24/7 Access</h3>
                <p>Study materials available anytime, anywhere with instant access</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3>Mobile Friendly</h3>
                <p>Optimized for all devices - study on your phone, tablet, or computer</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3>Board Specific</h3>
                <p>Content tailored for Pakistani education boards and curriculum</p>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-card">
                
                <div class="stat-number" id="booksCount">200+</div>
                <div class="stat-label">Books & Resources</div>
            </div>
            <div class="stat-card">
               
                <div class="stat-number" id="videosCount">150+</div>
                <div class="stat-label">Video Lectures</div>
            </div>
            <div class="stat-card">
                
                <div class="stat-number" id="papersCount">50+</div>
                <div class="stat-label">Past Papers</div>
            </div>
            <div class="stat-card">
                
                <div class="stat-number" id="studentsCount">5000+</div>
                <div class="stat-label">Students</div>
            </div>
        </div>
    </section>
</div>

<!-- Enhanced JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const searchForm = document.getElementById('mainSearchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const classSelect = document.getElementById('classSelect');
            const boardSelect = document.getElementById('boardSelect');
            
            if (!classSelect.value || !boardSelect.value) {
                e.preventDefault();
                showNotification('Please select both class and board', 'warning');
                return false;
            }
        });
    }

    // Load dynamic statistics
    loadStatistics();
    
    // Initialize tooltips
    initializeTooltips();
});

// Load real statistics from server
function loadStatistics() {
    fetch('ajax/get_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatCounter('booksCount', data.books || '200+');
                updateStatCounter('videosCount', data.videos || '150+');
                updateStatCounter('papersCount', data.papers || '50+');
                updateStatCounter('studentsCount', data.students || '5000+');
            }
        })
        .catch(error => {
            console.log('Stats loading failed, using default values');
        });
}

// Animate stat counters
function updateStatCounter(elementId, value) {
    const element = document.getElementById(elementId);
    if (element && typeof value === 'number') {
        animateCounter(element, 0, value, 2000);
    } else if (element) {
        element.textContent = value;
    }
}

function animateCounter(element, start, end, duration) {
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            element.textContent = end.toLocaleString() + '+';
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 16);
}

// Enhanced notification system
function showNotification(message, type = 'info') {
    const notification = createNotificationElement(message, type);
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => notification.classList.add('show'), 10);
    
    // Auto remove
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function createNotificationElement(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">×</button>
        </div>
    `;
    return notification;
}

function getNotificationIcon(type) {
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'info-circle';
}

// Enhanced modal functionality
function openClassModal(boardName, boardId) {
    const modal = document.getElementById('classModal');
    const title = document.getElementById('modalTitle');
    
    if (modal && title) {
        title.textContent = `${boardName} - Select Your Class`;
        modal.classList.add('active');
        selectedBoard = boardId;
        
        // Focus management for accessibility
        const firstButton = modal.querySelector('.class-card');
        if (firstButton) firstButton.focus();
    }
}

function closeClassModal() {
    const modal = document.getElementById('classModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

function selectClass(classId, className) {
    selectedClass = classId;
    closeClassModal();
    
    const resourceModal = document.getElementById('resourceModal');
    const resourceTitle = document.getElementById('resourceModalTitle');
    
    if (resourceModal && resourceTitle) {
        resourceTitle.textContent = `${className} - Choose Resource Type`;
        resourceModal.classList.add('active');
        
        const firstButton = resourceModal.querySelector('.resource-card');
        if (firstButton) firstButton.focus();
    } else {
        // Fallback: redirect to search
        window.location.href = `search.php?class_id=${selectedClass}&board_id=${selectedBoard}`;
    }
}

function closeResourceModal() {
    const modal = document.getElementById('resourceModal');
    if (modal) {
        modal.classList.remove('active');
    }
}

function openResource(resourceType) {
    closeResourceModal();
    
    if (selectedClass && selectedBoard) {
        const params = new URLSearchParams({
            class_id: selectedClass,
            board_id: selectedBoard
        });
        
        if (resourceType) {
            params.append('type', resourceType);
        }
        
        window.location.href = `search.php?${params.toString()}`;
    } else {
        showNotification('Please select a class and board first', 'warning');
    }
}

function showSearch() {
    const searchSection = document.querySelector('.search-section');
    if (searchSection) {
        searchSection.scrollIntoView({ 
            behavior: 'smooth',
            block: 'center'
        });
        
        // Focus on first select element
        const firstSelect = searchSection.querySelector('select');
        if (firstSelect) {
            setTimeout(() => firstSelect.focus(), 500);
        }
    }
}

// Initialize tooltips for better UX
function initializeTooltips() {
    const elements = document.querySelectorAll('[title]');
    elements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = e.target.title;
    document.body.appendChild(tooltip);
    
    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + rect.width / 2 - tooltip.offsetWidth / 2 + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
    
    e.target.title = ''; // Remove original title
    e.target._originalTitle = tooltip.textContent;
}

function hideTooltip(e) {
    const tooltip = document.querySelector('.tooltip');
    if (tooltip) {
        tooltip.remove();
    }
    
    if (e.target._originalTitle) {
        e.target.title = e.target._originalTitle;
    }
}

// Global variables
let selectedBoard = '';
let selectedClass = '';
</script>

<script src="assets/js/main.js"></script>
<?php include 'includes/footer.php'; ?>
