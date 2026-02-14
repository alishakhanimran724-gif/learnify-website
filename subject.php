<?php
// ===== SUBJECTS PAGE =====
// File: subjects.php
include 'config/db.php';
include 'includes/header.php';

$board_id = $_GET['board_id'] ?? '';
$board_name = $_GET['board_name'] ?? '';
$class_id = $_GET['class_id'] ?? '';
$class_name = $_GET['class_name'] ?? '';

if (!$board_id || !$class_id) {
    header('Location: index.php');
    exit;
}

// Get subjects for this class
$stmt = $pdo->prepare("SELECT * FROM subjects WHERE class_id = ? AND status = 'active' ORDER BY subject_name");
$stmt->execute([$class_id]);
$subjects = $stmt->fetchAll();
?>

<div class="subjects-page">
    <!-- Hero Section -->
    <section class="subjects-hero">
        <div class="container">
            <div class="subjects-hero-content">
                <div class="breadcrumb">
                    <a href="index.php"><i class="fas fa-home"></i> Home</a>
                    <span><i class="fas fa-chevron-right"></i></span>
                    <a href="board.php?id=<?= $board_id ?>&name=<?= urlencode($board_name) ?>"><?= htmlspecialchars($board_name) ?></a>
                    <span><i class="fas fa-chevron-right"></i></span>
                    <span><?= htmlspecialchars($class_name) ?></span>
                </div>
                <h1><?= htmlspecialchars($class_name) ?> (<?= htmlspecialchars($board_name) ?>)</h1>
                <p>Complete Study Materials for <?= htmlspecialchars($class_name) ?> Students</p>
            </div>
        </div>
    </section>

    <!-- Subjects Grid -->
    <section class="subjects-selection">
        <div class="container">
            <h2 class="section-title">Select Your Subject</h2>
            <p class="section-subtitle">Choose a subject to access study materials and resources</p>
            
            <div class="subjects-grid">
                <?php foreach($subjects as $subject): ?>
                    <a href="resources.php?board_id=<?= $board_id ?>&board_name=<?= urlencode($board_name) ?>&class_id=<?= $class_id ?>&class_name=<?= urlencode($class_name) ?>&subject_id=<?= $subject['id'] ?>&subject_name=<?= urlencode($subject['subject_name']) ?>" 
                       class="subject-card-link">
                        <div class="subject-card">
                            <div class="subject-icon">
                                <?php
                                // Set different icons for different subjects
                                $subject_icons = [
                                    'English' => 'fas fa-language',
                                    'Mathematics' => 'fas fa-calculator',
                                    'Physics' => 'fas fa-atom',
                                    'Chemistry' => 'fas fa-flask',
                                    'Biology' => 'fas fa-dna',
                                    'Urdu' => 'fas fa-book',
                                    'Pakistan Studies' => 'fas fa-flag',
                                    'Islamiat' => 'fas fa-mosque'
                                ];
                                $icon = $subject_icons[$subject['subject_name']] ?? 'fas fa-book';
                                ?>
                                <i class="<?= $icon ?>"></i>
                            </div>
                            <h3><?= htmlspecialchars($subject['subject_name']) ?></h3>
                            <p>Study materials for <?= htmlspecialchars($subject['subject_name']) ?></p>
                            <div class="subject-meta">
                                <span><i class="fas fa-video"></i> Videos</span>
                                <span><i class="fas fa-book"></i> Books</span>
                                <span><i class="fas fa-sticky-note"></i> Notes</span>
                                <span><i class="fas fa-clipboard-list"></i> Tests</span>
                            </div>
                            <div class="explore-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php if (empty($subjects)): ?>
                <div class="no-subjects">
                    <i class="fas fa-info-circle"></i>
                    <h3>No subjects available</h3>
                    <p>Subjects for this class are being updated. Please check back later.</p>
                    <a href="board.php?id=<?= $board_id ?>&name=<?= urlencode($board_name) ?>" class="search-btn">
                        <i class="fas fa-arrow-left"></i> Back to Classes
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Resource Type Preview Modal (shows on subject click) -->
    <div class="resource-type-modal" id="resourceTypeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="resourceModalTitle">Choose Resource Type</h3>
                <button class="close-modal" onclick="closeResourceTypeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="resource-type-grid">
                <button class="resource-type-card" onclick="selectResourceType('notes')">
                    <div class="resource-type-icon">
                        <i class="fas fa-edit"></i>
                    </div>
                    <h4>Notes</h4>
                    <p>Study notes and summaries</p>
                </button>
                <button class="resource-type-card" onclick="selectResourceType('pdf')">
                    <div class="resource-type-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4>Books</h4>
                    <p>Textbooks and reference materials</p>
                </button>
                <button class="resource-type-card" onclick="selectResourceType('test')">
                    <div class="resource-type-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>Past Papers</h4>
                    <p>Previous exam papers</p>
                </button>
                <button class="resource-type-card" onclick="selectResourceType('video')">
                    <div class="resource-type-icon">
                        <i class="fas fa-play"></i>
                    </div>
                    <h4>Video Lectures</h4>
                    <p>Educational video content</p>
                </button>
                <button class="resource-type-card" onclick="selectResourceType('test')">
                    <div class="resource-type-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h4>Guess Papers</h4>
                    <p>Predicted exam questions</p>
                </button>
                <button class="resource-type-card" onclick="selectResourceType('online-test')">
                    <div class="resource-type-icon">
                        <i class="fas fa-laptop"></i>
                    </div>
                    <h4>Online Test</h4>
                    <p>Interactive online assessments</p>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentSubjectUrl = '';

function showResourceTypeModal(subjectUrl, subjectName) {
    currentSubjectUrl = subjectUrl;
    document.getElementById('resourceModalTitle').textContent = `${subjectName} - Choose Resource Type`;
    document.getElementById('resourceTypeModal').classList.add('active');
}

function closeResourceTypeModal() {
    document.getElementById('resourceTypeModal').classList.remove('active');
}

function selectResourceType(type) {
    if (currentSubjectUrl) {
        window.location.href = currentSubjectUrl + '&type=' + type;
    }
    closeResourceTypeModal();
}

// Add click handlers to subject cards
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.subject-card-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const subjectName = this.querySelector('h3').textContent;
            const url = this.href;
            showResourceTypeModal(url, subjectName);
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>