<?php
// ===== RESOURCE DETAIL PAGE =====
// File: resource-detail.php
include 'config/db.php';
include 'includes/header.php';

$resource_id = $_GET['id'] ?? '';

if (!$resource_id) {
    header('Location: index.php');
    exit;
}

// Get resource details with related information using correct table name
$stmt = $pdo->prepare("
    SELECT sd.*, s.subject_name, c.class_name, d.district_name, d.board as board_name
    FROM subject_details sd 
    LEFT JOIN subjects s ON sd.subject_id = s.id
    LEFT JOIN classes c ON sd.class_id = c.id
    LEFT JOIN districts d ON sd.district_id = d.id
    WHERE sd.id = ?
");
$stmt->execute([$resource_id]);
$resource = $stmt->fetch();

if (!$resource) {
    header('Location: index.php');
    exit;
}

// Get related resources (same subject and type)
$relatedStmt = $pdo->prepare("
    SELECT sd.*, s.subject_name 
    FROM subject_details sd 
    LEFT JOIN subjects s ON sd.subject_id = s.id
    WHERE sd.subject_id = ? AND sd.type = ? AND sd.id != ?
    ORDER BY sd.id DESC
    LIMIT 6
");
$relatedStmt->execute([$resource['subject_id'], $resource['type'], $resource_id]);
$relatedResources = $relatedStmt->fetchAll();

// Get keywords (if stored)
$keywords = !empty($resource['keywords']) ? explode(',', $resource['keywords']) : [];

// Set default board name if not available
if (empty($resource['board_name'])) {
    $resource['board_name'] = 'General';
}
?>

<style>/* ===== RESOURCE DETAIL PAGE STYLES ===== */

/* Variables for consistent theming */
:root {
    --primary-color: #00027a;
    --primary-dark: #00027a;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --text-dark: #020c1cff;
    --text-light: #485567ff;
    --border-color: #e2e8f0;
    --background-light: #f8fafc;
    --white: #ffffff;
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
    --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    --border-radius: 8px;
    --border-radius-lg: 12px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Main container */
.resource-detail-page {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: calc(100vh - 120px);
    padding: 2rem 0;
}

.detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Breadcrumb */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 2rem;
    padding: 1rem;
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-sm);
    font-size: 0.875rem;
    flex-wrap: wrap;
}

.breadcrumb a {
    color: var(--primary-color);
    text-decoration: none;
    transition: var(--transition);
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
}

.breadcrumb a:hover {
    background: var(--background-light);
    color: var(--primary-dark);
}

.breadcrumb span {
    color: var(--text-light);
    font-size: 0.75rem;
}

.breadcrumb span:last-child {
    color: var(--text-dark);
    font-weight: 500;
}

/* Main content layout */
.resource-detail-content {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
    margin-bottom: 3rem;
}

.main-content {
    background: var(--white);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

/* Resource image */
.resource-image {
    position: relative;
    height: 400px;
    overflow: hidden;
}

.resource-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.resource-image:hover img {
    transform: scale(1.05);
}

.resource-type-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: rgba(59, 130, 246, 0.95);
    color: var(--white);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    backdrop-filter: blur(10px);
    box-shadow: var(--shadow-md);
}

.resource-type-badge i {
    font-size: 1rem;
}

/* Resource title */
.resource-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin: 2rem 2rem 1rem;
    line-height: 1.2;
}

/* Resource meta */
.resource-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin: 0 2rem 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid var(--border-color);
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-light);
    font-size: 0.875rem;
    background: var(--background-light);
    padding: 0.5rem 1rem;
    border-radius: 50px;
    transition: var(--transition);
}

.meta-item:hover {
    background: var(--primary-color);
    color: var(--white);
    transform: translateY(-2px);
}

.meta-item i {
    font-size: 1rem;
    width: 16px;
    text-align: center;
}

/* Resource description */
.resource-description {
    padding: 0 2rem 2rem;
    font-size: 1.125rem;
    line-height: 1.7;
    color: var(--text-dark);
}

/* Keywords section */
.keywords-section {
    padding: 0 2rem 2rem;
}

.keywords-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.keywords {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.keyword-tag {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: var(--white);
    padding: 0.375rem 0.75rem;
    border-radius: 50px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: var(--transition);
}

.keyword-tag:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Sidebar */
.sidebar {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Download card */
.download-card {
    background: linear-gradient(135deg, var(--white) 0%, var(--background-light) 100%);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    text-align: center;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
    position: sticky;
    top: 2rem;
}

.download-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    box-shadow: var(--shadow-lg);
}

.download-icon i {
    font-size: 2rem;
    color: var(--white);
}

.download-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.download-subtitle {
    color: var(--text-light);
    margin-bottom: 2rem;
    line-height: 1.5;
}

.download-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: var(--white);
    padding: 1rem 2rem;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    width: 100%;
    margin-bottom: 0.75rem;
    box-shadow: var(--shadow-md);
    border: none;
    cursor: pointer;
}

.download-btn:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

.download-btn.secondary {
    background: linear-gradient(135deg, var(--secondary-color), #475569);
    margin-bottom: 0;
}

.download-btn i {
    font-size: 1.125rem;
}

/* Resource stats */
.resource-stats {
    background: var(--white);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    border: 1px solid var(--border-color);
}

.stats-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    color: var(--text-dark);
    background: var(--background-light);
    border-radius: var(--border-radius);
    transition: var(--transition);
}

.stat-item:hover {
    background: var(--primary-color);
    color: var(--white);
    transform: translateY(-2px);
}

.stat-number {
    font-size: 1.125rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
     color: var(--text-dark);
}

.stat-label {
    font-size: 0.875rem;
    opacity: 0.8;
     color: var(--text-dark);
}

/* Related resources */
.related-resources {
    background: var(--white);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-lg);
}

.section-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -0.5rem;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--primary-dark));
    border-radius: 2px;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.related-card {
    background: var(--white);
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: var(--transition);
    text-decoration: none;
    border: 1px solid var(--border-color);
}

.related-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.related-image {
    height: 200px;
    overflow: hidden;
}

.related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.related-card:hover .related-image img {
    transform: scale(1.05);
}

.related-content {
    padding: 1.5rem;
}

.related-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.75rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.related-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-light);
    font-size: 0.875rem;
}

.related-meta i {
    color: var(--primary-color);
}

/* Responsive design */
@media (max-width: 1024px) {
    .resource-detail-content {
        grid-template-columns: 1fr;
    }
    
    .sidebar {
        order: -1;
    }
    
    .download-card {
        position: static;
    }
}

@media (max-width: 768px) {
    .detail-container {
        padding: 0 0.5rem;
    }
    
    .resource-detail-page {
        padding: 1rem 0;
    }
    
    .resource-title {
        font-size: 2rem;
        margin: 1.5rem 1rem 1rem;
    }
    
    .resource-meta {
        margin: 0 1rem 1.5rem;
        gap: 0.5rem;
    }
    
    .meta-item {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
    
    .resource-description {
        padding: 0 1rem 1.5rem;
        font-size: 1rem;
    }
    
    .keywords-section {
        padding: 0 1rem 1.5rem;
    }
    
    .download-card {
        padding: 1.5rem;
    }
    
    .download-btn {
        padding: 0.875rem 1.5rem;
        font-size: 0.875rem;
    }
    
    .related-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .related-resources {
        padding: 1.5rem;
    }
    
    .section-title {
        font-size: 1.5rem;
    }
}

@media (max-width: 480px) {
    .breadcrumb {
        font-size: 0.75rem;
        padding: 0.75rem;
    }
    
    .resource-image {
        height: 250px;
    }
    
    .resource-title {
        font-size: 1.5rem;
        margin: 1rem 0.75rem 0.75rem;
    }
    
    .resource-meta {
        margin: 0 0.75rem 1rem;
        flex-direction: column;
        align-items: stretch;
    }
    
    .meta-item {
        justify-content: center;
    }
    
    .resource-description,
    .keywords-section {
        padding: 0 0.75rem 1rem;
    }
    
    .download-card {
        padding: 1rem;
    }
    
    .download-icon {
        width: 60px;
        height: 60px;
        margin-bottom: 1rem;
    }
    
    .download-icon i {
        font-size: 1.5rem;
    }
    
    .download-title {
        font-size: 1.25rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
}

/* Loading states and animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.resource-detail-content,
.related-resources {
    animation: fadeInUp 0.6s ease-out;
}

.related-card:nth-child(even) {
    animation-delay: 0.1s;
}

.related-card:nth-child(odd) {
    animation-delay: 0.2s;
}

/* Print styles */
@media print {
    .resource-detail-page {
        background: none;
    }
    
    .download-card,
    .breadcrumb {
        display: none;
    }
    
    .resource-detail-content {
        grid-template-columns: 1fr;
    }
    
    .related-resources {
        page-break-before: always;
    }
}</style>

<div class="resource-detail-page">
    <div class="detail-container">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <span><i class="fas fa-chevron-right"></i></span>
            <a href="index.php">
                <?= htmlspecialchars($resource['board_name']) ?>
            </a>
            <span><i class="fas fa-chevron-right"></i></span>
            <a href="resources.php?board_id=1&board_name=<?= urlencode($resource['board_name']) ?>&class_id=<?= $resource['class_id'] ?>&class_name=<?= urlencode($resource['class_name']) ?>&subject_id=<?= $resource['subject_id'] ?>&subject_name=<?= urlencode($resource['subject_name']) ?>">
                <?= htmlspecialchars($resource['subject_name']) ?>
            </a>
            <span><i class="fas fa-chevron-right"></i></span>
            <span><?= htmlspecialchars($resource['heading']) ?></span>
        </div>

        <!-- Main Content -->
        <div class="resource-detail-content">
            <div class="main-content">
                <!-- Resource Image -->
                <div class="resource-image">
                    <img src="<?= !empty($resource['front_image']) && file_exists($resource['front_image']) ? htmlspecialchars($resource['front_image']) : 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' ?>" 
                         alt="<?= htmlspecialchars($resource['heading']) ?>"
                         onerror="this.src='https://images.unsplash.com/photo-1543002588-bfa74002ed7e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                    <div class="resource-type-badge">
                        <?php
                        $type_icons = [
                            'video' => 'fas fa-play',
                            'books' => 'fas fa-book',
                            'notes' => 'fas fa-sticky-note',
                            'past_paper' => 'fas fa-file-alt',
                            'guess_paper' => 'fas fa-clipboard-list'
                        ];
                        $icon = $type_icons[$resource['type']] ?? 'fas fa-file';
                        ?>
                        <i class="<?= $icon ?>"></i>
                        <?= ucfirst(str_replace('_', ' ', htmlspecialchars($resource['type']))) ?>
                    </div>
                </div>

                <!-- Resource Title -->
                <h1 class="resource-title"><?= htmlspecialchars($resource['heading']) ?></h1>

                <!-- Resource Meta -->
                <div class="resource-meta">
                    <div class="meta-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Class <?= htmlspecialchars($resource['class_name']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-book"></i>
                        <span><?= htmlspecialchars($resource['subject_name']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-building"></i>
                        <span><?= htmlspecialchars($resource['board_name']) ?></span>
                    </div>
                    <?php if (!empty($resource['district_name'])): ?>
                    <div class="meta-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($resource['district_name']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="meta-item">
                        <i class="fas fa-calendar"></i>
                        <span><?= date('M d, Y') ?></span>
                    </div>
                </div>

                <!-- Resource Description -->
                <div class="resource-description">
                    <?= nl2br(htmlspecialchars($resource['description'])) ?>
                </div>

                <!-- Keywords -->
                <?php if (!empty($keywords)): ?>
                <div class="keywords-section">
                    <h3 class="keywords-title">Related Keywords</h3>
                    <div class="keywords">
                        <?php foreach($keywords as $keyword): ?>
                            <span class="keyword-tag"><?= htmlspecialchars(trim($keyword)) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Download Card -->
                <div class="download-card">
                    <div class="download-icon">
                        <i class="<?= $type_icons[$resource['type']] ?? 'fas fa-file' ?>"></i>
                    </div>
                    <h3 class="download-title">Access Resource</h3>
                    <p class="download-subtitle">
                        <?php if ($resource['type'] === 'video'): ?>
                            Watch this video lecture online
                        <?php else: ?>
                            Download this <?= str_replace('_', ' ', $resource['type']) ?> for offline study
                        <?php endif; ?>
                    </p>
                    
                    <?php if ($resource['type'] === 'video'): ?>
                        <?php if (!empty($resource['video_link'])): ?>
                            <a href="<?= htmlspecialchars($resource['video_link']) ?>" target="_blank" class="download-btn">
                                <i class="fas fa-play"></i> Watch on YouTube
                            </a>
                        <?php endif; ?>
                        
                        <!-- Always provide access button for videos that goes through download.php -->
                        <a href="download.php?id=<?= $resource['id'] ?>" class="download-btn <?= !empty($resource['video_link']) ? 'secondary' : '' ?>">
                            <i class="fas fa-external-link-alt"></i> Open Video
                        </a>
                    <?php else: ?>
                        <!-- For non-video resources, show download button -->
                        <?php if (!empty($resource['file'])): ?>
                            <a href="download.php?id=<?= $resource['id'] ?>" class="download-btn">
                                <i class="fas fa-download"></i> Download File
                            </a>
                        <?php else: ?>
                            <div class="download-btn secondary" style="opacity: 0.6; cursor: not-allowed;">
                                <i class="fas fa-exclamation-circle"></i> File Not Available
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Resource Stats -->
                <div class="resource-stats">
                    <h3 class="stats-title">Resource Info</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number"><?= ucfirst(str_replace('_', ' ', $resource['type'])) ?></div>
                            <div class="stat-label">Type</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?= htmlspecialchars($resource['class_name']) ?></div>
                            <div class="stat-label">Class</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Resources -->
        <?php if (!empty($relatedResources)): ?>
        <div class="related-resources">
            <h2 class="section-title">Related Resources</h2>
            <div class="related-grid">
                <?php foreach($relatedResources as $related): ?>
                <a href="resource-detail.php?id=<?= $related['id'] ?>" class="related-card">
                    <div class="related-image">
                        <img src="<?= !empty($related['front_image']) && file_exists($related['front_image']) ? htmlspecialchars($related['front_image']) : 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80' ?>" 
                             alt="<?= htmlspecialchars($related['heading']) ?>"
                             onerror="this.src='https://images.unsplash.com/photo-1543002588-bfa74002ed7e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">
                    </div>
                    <div class="related-content">
                        <h4 class="related-title"><?= htmlspecialchars($related['heading']) ?></h4>
                        <div class="related-meta">
                            <i class="<?= $type_icons[$related['type']] ?? 'fas fa-file' ?>"></i>
                            <span><?= ucfirst(str_replace('_', ' ', htmlspecialchars($related['type']))) ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>