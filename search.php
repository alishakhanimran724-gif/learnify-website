<?php
// ===== FIXED SEARCH PAGE =====
// File: search.php
include 'config/db.php';
include 'includes/header.php';

$class_id = $_GET['class_id'] ?? '';
$board_name = $_GET['board_name'] ?? '';  // Changed from board_id to board_name
$subject_id = $_GET['subject_id'] ?? '';
$type = $_GET['type'] ?? '';

// Build query - Fixed to properly handle board filtering
$query = "SELECT sd.*, s.subject_name, c.class_name, d.district_name, d.board as board_name
          FROM subject_details sd 
          LEFT JOIN subjects s ON sd.subject_id = s.id
          LEFT JOIN classes c ON sd.class_id = c.id
          LEFT JOIN districts d ON sd.district_id = d.id
          WHERE 1=1"; // Always true condition to make adding WHERE clauses easier

$params = [];

if ($class_id) {
    $query .= " AND sd.class_id = ?";
    $params[] = $class_id;
}

// Fixed board filtering - check both district board and allow NULL district_id
if ($board_name) {
    $query .= " AND (d.board = ? OR sd.district_id IS NULL)";
    $params[] = $board_name;
}

if ($subject_id) {
    $query .= " AND sd.subject_id = ?";
    $params[] = $subject_id;
}

if ($type) {
    $query .= " AND sd.type = ?";
    $params[] = $type;
}

$query .= " ORDER BY sd.id DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $resources = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Search query error: " . $e->getMessage());
    $resources = [];
}

// Get filter data
try {
    $boards = $pdo->query("SELECT * FROM boards ORDER BY board_name")->fetchAll();
    $classes = $pdo->query("SELECT * FROM classes ORDER BY class_name")->fetchAll();
    
    // Get subjects for selected class
    $subjects = [];
    if ($class_id) {
        $subjectStmt = $pdo->prepare("SELECT * FROM subjects WHERE class_id = ? ORDER BY subject_name");
        $subjectStmt->execute([$class_id]);
        $subjects = $subjectStmt->fetchAll();
    }
} catch (PDOException $e) {
    error_log("Filter data error: " . $e->getMessage());
    $boards = [];
    $classes = [];
    $subjects = [];
}
?>

<style>
/* Search Page Styles */
:root {
    --primary-color: #00027A;
    --primary-light: #00027a;
    --primary-dark: #000154;
    --accent-color: #FF6B35;
    --text-primary: #1F2937;
    --text-secondary: #6B7280;
    --bg-primary: #FFFFFF;
    --bg-secondary: #F9FAFB;
    --bg-tertiary: #F3F4F6;
    --border-color: #E5E7EB;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --border-radius: 8px;
    --border-radius-lg: 12px;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    color: var(--text-primary);
    line-height: 1.6;
    min-height: 100vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.search-results {
    padding: 2rem 0;
}

/* Back Button */
.back-button {
    margin-bottom: 2rem;
}

.search-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--primary-color);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
    border: none;
    cursor: pointer;
}

.search-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Search Section */
.search-section {
    background: var(--bg-primary);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
}

.search-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    align-items: end;
}

.search-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.search-item label {
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.875rem;
}

.search-select {
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    background: var(--bg-primary);
    color: var(--text-primary);
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.search-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 2, 122, 0.1);
}

.search-select:disabled {
    background: var(--bg-tertiary);
    color: var(--text-secondary);
    cursor: not-allowed;
}

/* Section Title */
.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2rem;
    text-align: center;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--bg-primary);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
}

.no-results i {
    font-size: 3rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
    opacity: 0.6;
}

.no-results h3 {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.no-results p {
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

/* Resources Grid */
.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

/* Resource Item */
.resource-item {
    background: var(--bg-primary);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
    cursor: pointer;
}

.resource-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary-color);
}

.resource-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.resource-item:hover img {
    transform: scale(1.05);
}

.resource-content {
    padding: 1.5rem;
}

.resource-content h4 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.resource-content > p {
    color: var(--text-secondary);
    margin-bottom: 1rem;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Resource Meta */
.resource-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    color: var(--text-secondary);
    font-size: 0.75rem;
    background: var(--bg-secondary);
    padding: 0.25rem 0.5rem;
    border-radius: 50px;
}

.meta-item i {
    color: var(--primary-color);
    font-size: 0.75rem;
}

/* Resource Button */
.resource-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--primary-color);
    color: white;
    padding: 0.75rem 1rem;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    width: 100%;
    justify-content: center;
}

.resource-btn:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.resource-btn[style*="opacity"] {
    background: var(--bg-tertiary);
    color: var(--text-secondary);
}

/* Loading States */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.resource-item {
    animation: fadeInUp 0.5s ease-out;
}

.resource-item:nth-child(even) {
    animation-delay: 0.1s;
}

.resource-item:nth-child(odd) {
    animation-delay: 0.2s;
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .resources-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .search-section {
        padding: 1.5rem;
    }
    
    .container {
        padding: 0 0.5rem;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .search-results {
        padding: 1rem 0;
    }
    
    .search-section {
        padding: 1rem;
    }
    
    .resource-content {
        padding: 1rem;
    }
    
    .resource-meta {
        gap: 0.5rem;
    }
    
    .meta-item {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
}
</style>

<div class="container" style="margin-top: 6rem;">
    <div class="search-results">
        <div class="back-button">
            <a href="index.php" class="search-btn">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
        
        <!-- Advanced Search Filters -->
        <section class="search-section">
            <form class="search-grid" method="GET" action="search.php" id="advancedSearchForm">
                <div class="search-item">
                    <label>Select Class</label>
                    <select class="search-select" name="class_id" id="classSelect">
                        <option value="">Select Class</option>
                        <?php foreach($classes as $class): ?>
                            <option value="<?= $class['id'] ?>" <?= $class_id == $class['id'] ? 'selected' : '' ?>>
                                Class <?= htmlspecialchars($class['class_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="search-item">
                    <label>Select Board</label>
                    <select class="search-select" name="board_name" id="boardSelect">
                        <option value="">Select Board</option>
                        <?php foreach($boards as $board): ?>
                            <option value="<?= htmlspecialchars($board['board_name']) ?>" <?= $board_name == $board['board_name'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($board['board_name']) ?> Board
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="search-item">
                    <label>Select Subject</label>
                    <select class="search-select" name="subject_id" id="subjectSelect">
                        <option value="">Select Subject</option>
                        <?php foreach($subjects as $subject): ?>
                            <option value="<?= $subject['id'] ?>" <?= $subject_id == $subject['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($subject['subject_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="search-item">
                    <label>Resource Type</label>
                    <select class="search-select" name="type" id="typeSelect">
                        <option value="">All Types</option>
                        <option value="video" <?= $type == 'video' ? 'selected' : '' ?>>Video Lectures</option>
                        <option value="books" <?= $type == 'books' ? 'selected' : '' ?>>Books</option>
                        <option value="notes" <?= $type == 'notes' ? 'selected' : '' ?>>Notes</option>
                        <option value="past_paper" <?= $type == 'past_paper' ? 'selected' : '' ?>>Past Papers</option>
                        <option value="guess_paper" <?= $type == 'guess_paper' ? 'selected' : '' ?>>Guess Papers</option>
                    </select>
                </div>
                
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i> Search Now
                </button>
            </form>
        </section>
        
        <h2 class="section-title">
            Search Results 
            <?php if (!empty($resources)): ?>
                <span style="font-size: 1rem; color: var(--text-secondary);">(<?= count($resources) ?> found)</span>
            <?php endif; ?>
        </h2>
        
        <?php if (empty($resources)): ?>
            <div class="no-results">
                <i class="fas fa-search"></i>
                <h3>No resources found</h3>
                <p>Try adjusting your search criteria or browse our available materials.</p>
                <a href="index.php" class="search-btn" style="margin-top: 1rem; display: inline-flex;">
                    <i class="fas fa-home"></i> Go to Home
                </a>
            </div>
        <?php else: ?>
            <div class="resources-grid" id="searchResults">
                <?php foreach($resources as $resource): ?>
                <div class="resource-item" onclick="window.location.href='resource-detail.php?id=<?= $resource['id'] ?>'">
                    <img src="<?= !empty($resource['front_image']) && file_exists($resource['front_image']) ? htmlspecialchars($resource['front_image']) : 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80' ?>" 
                         alt="<?= htmlspecialchars($resource['heading']) ?>"
                         onerror="this.src='https://images.unsplash.com/photo-1543002588-bfa74002ed7e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">
                    
                    <div class="resource-content">
                        <h4><?= htmlspecialchars($resource['heading']) ?></h4>
                        <p><?= htmlspecialchars($resource['description']) ?></p>
                        
                        <div class="resource-meta">
                            <span class="meta-item">
                                <i class="fas fa-graduation-cap"></i> 
                                Class <?= htmlspecialchars($resource['class_name']) ?>
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-book"></i> 
                                <?= htmlspecialchars($resource['subject_name']) ?>
                            </span>
                            <?php if ($resource['board_name']): ?>
                            <span class="meta-item">
                                <i class="fas fa-building"></i> 
                                <?= htmlspecialchars($resource['board_name']) ?>
                            </span>
                            <?php endif; ?>
                            <?php if ($resource['district_name']): ?>
                            <span class="meta-item">
                                <i class="fas fa-map-marker-alt"></i> 
                                <?= htmlspecialchars($resource['district_name']) ?>
                            </span>
                            <?php endif; ?>
                            <span class="meta-item">
                                <i class="fas fa-tag"></i> 
                                <?= ucfirst(str_replace('_', ' ', htmlspecialchars($resource['type']))) ?>
                            </span>
                        </div>
                        
                        <?php if ($resource['type'] === 'video' && !empty($resource['video_link'])): ?>
                            <a href="<?= htmlspecialchars($resource['video_link']) ?>" target="_blank" class="resource-btn" onclick="event.stopPropagation();">
                                <i class="fas fa-play"></i> Watch Video
                            </a>
                        <?php elseif (!empty($resource['file'])): ?>
                            <a href="download.php?id=<?= $resource['id'] ?>" class="resource-btn" onclick="event.stopPropagation();">
                                <i class="fas fa-download"></i> Download
                            </a>
                        <?php else: ?>
                            <span class="resource-btn" style="opacity: 0.6; cursor: not-allowed;" onclick="event.stopPropagation();">
                                <i class="fas fa-info-circle"></i> No File Available
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Initialize search page functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 Search page loaded');
    
    const classSelect = document.getElementById('classSelect');
    const subjectSelect = document.getElementById('subjectSelect');
    
    if (classSelect) {
        classSelect.addEventListener('change', function() {
            console.log('Class changed to:', this.value);
            loadSubjects(this.value);
        });
    }
    
    // Load subjects for current class selection on page load
    if (classSelect && classSelect.value) {
        console.log('Loading subjects for class:', classSelect.value);
        loadSubjects(classSelect.value);
    }
    
    // Add form validation
    const form = document.getElementById('advancedSearchForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const hasFilters = classSelect.value || 
                             document.getElementById('boardSelect').value || 
                             document.getElementById('subjectSelect').value || 
                             document.getElementById('typeSelect').value;
            
            if (!hasFilters) {
                e.preventDefault();
                alert('Please select at least one filter to search.');
                return false;
            }
        });
    }
});

function loadSubjects(classId) {
    const subjectSelect = document.getElementById('subjectSelect');
    if (!subjectSelect || !classId) {
        if (subjectSelect) {
            subjectSelect.innerHTML = '<option value="">Select Subject</option>';
            subjectSelect.disabled = false;
        }
        return;
    }
    
    console.log('Loading subjects for class ID:', classId);
    
    // Show loading state
    subjectSelect.innerHTML = '<option value="">Loading subjects...</option>';
    subjectSelect.disabled = true;
    
    // Create the AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `ajax/get_subjects.php?class_id=${encodeURIComponent(classId)}`, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const subjects = JSON.parse(xhr.responseText);
                    console.log('Received subjects:', subjects);
                    
                    subjectSelect.innerHTML = '<option value="">Select Subject</option>';
                    
                    if (Array.isArray(subjects)) {
                        subjects.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.id;
                            option.textContent = subject.subject_name;
                            
                            // Maintain selection if it exists
                            if (subject.id == '<?= $subject_id ?>') {
                                option.selected = true;
                            }
                            
                            subjectSelect.appendChild(option);
                        });
                    }
                    
                    subjectSelect.disabled = false;
                } catch (error) {
                    console.error('Error parsing subjects response:', error);
                    subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
                    subjectSelect.disabled = false;
                }
            } else {
                console.error('HTTP Error:', xhr.status);
                subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
                subjectSelect.disabled = false;
            }
        }
    };
    
    xhr.onerror = function() {
        console.error('Network error loading subjects');
        subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
        subjectSelect.disabled = false;
    };
    
    xhr.send();
}

// Add smooth scrolling for better UX
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

console.log('✅ Search page JavaScript initialized');
</script>

<?php include 'includes/footer.php'; ?>