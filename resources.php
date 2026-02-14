<?php
// ===== REDESIGNED RESOURCES PAGE =====
// File: resources.php
include 'config/db.php';
include 'includes/header.php';

$board_id = $_GET['board_id'] ?? '';
$board_name = $_GET['board_name'] ?? '';
$class_id = $_GET['class_id'] ?? '';
$class_name = $_GET['class_name'] ?? '';
$subject_id = $_GET['subject_id'] ?? '';
$subject_name = $_GET['subject_name'] ?? '';
$type = $_GET['type'] ?? '';

if (!$board_id || !$class_id || !$subject_id) {
    header('Location: index.php');
    exit;
}

// Build query for resources using the correct table name 'subject_details'
$query = "SELECT sd.*, s.subject_name, c.class_name, b.board_name 
          FROM subject_details sd 
          LEFT JOIN subjects s ON sd.subject_id = s.id
          LEFT JOIN classes c ON sd.class_id = c.id
          LEFT JOIN boards b ON b.board_name = (SELECT board FROM districts d WHERE d.id = sd.district_id)
          WHERE sd.class_id = ? 
          AND sd.subject_id = ?";

$params = [$class_id, $subject_id];

// Add district filter if board_name is provided
if ($board_name) {
    $query .= " AND (sd.district_id IS NULL OR EXISTS (SELECT 1 FROM districts d WHERE d.id = sd.district_id AND d.board = ?))";
    $params[] = $board_name;
}

if ($type) {
    $query .= " AND sd.type = ?";
    $params[] = $type;
}

$query .= " ORDER BY sd.id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$resources = $stmt->fetchAll();

// Get resource type display name
$type_names = [
    'video' => 'Video Lectures',
    'books' => 'PDF Books',
    'notes' => 'Notes & PPTs',
    'past_paper' => 'Past Papers',
    'guess_paper' => 'Guess Papers'
];
$type_display = $type_names[$type] ?? 'All Resources';
?>

<style>
    /* Modern Design System - Same as Board Page */
    :root {
        --primary-color: #00027A;
        --primary-light: #00027a;
        --primary-dark: #000154;
        --accent-color: #FF6B35;
        --accent-light: #FF8A65;
        --success-color: #10B981;
        --warning-color: #F59E0B;
        --error-color: #EF4444;
        --text-primary: #1F2937;
        --text-secondary: #6B7280;
        --bg-primary: #FFFFFF;
        --bg-secondary: #F9FAFB;
        --bg-tertiary: #F3F4F6;
        --border-color: #E5E7EB;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        --gradient-primary: linear-gradient(135deg, #00027A 0%, #0D1B9E 50%, #FF6B35 100%);
        --gradient-secondary: linear-gradient(135deg, #F8FAFC 0%, #E2E8F0 100%);
    }

    /* Global Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        padding-top: 80px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        background: var(--bg-secondary);
        color: var(--text-primary);
        line-height: 1.6;
    }

    .header {
        height: 80px;
        display: flex;
        align-items: center;
    }

    /* Main Resources Container */
    .resources-page {
        min-height: calc(100vh - 80px);
        background: var(--gradient-secondary);
        position: relative;
        overflow: hidden;
    }

    /* Decorative Background Elements */
    .resources-page::before {
        content: '';
        position: absolute;
        top: -30%;
        right: -15%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(0, 2, 122, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .resources-page::after {
        content: '';
        position: absolute;
        bottom: -20%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255, 107, 53, 0.06) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    /* Hero Section */
    .resources-hero {
        position: relative;
        z-index: 10;
        padding: 60px 0 40px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        margin-bottom: 0;
    }

    .resources-hero::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: linear-gradient(135deg, transparent 0%, rgba(255,255,255,0.1) 50%, transparent 100%);
        transform: skewY(-1deg);
        transform-origin: bottom left;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        position: relative;
        z-index: 2;
    }

    /* Breadcrumb */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        opacity: 0.9;
        flex-wrap: wrap;
    }

    .breadcrumb a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .breadcrumb a:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
    }

    .breadcrumb span {
        color: rgba(255, 255, 255, 0.6);
        font-size: 12px;
        display: flex;
        align-items: center;
    }

    /* Hero Content */
    .resources-hero-content {
        text-align: center;
        max-width: 900px;
        margin: 0 auto;
    }

    .resources-hero h1 {
        font-size: clamp(2rem, 4vw, 2.5rem);
        font-weight: 800;
        margin-bottom: 12px;
        background: linear-gradient(135deg, #FFFFFF 0%, #E0E7FF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .resources-hero p {
        font-size: 1.125rem;
        opacity: 0.9;
        margin-bottom: 32px;
        font-weight: 400;
    }

    /* Filter Buttons */
    .filter-buttons {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: repeat(2, auto);
    gap: 12px;
    align-items: center;
    justify-content: center;
    max-width: 800px;
    margin: 0 auto;
}

    .filter-btn {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        color: white;
        padding: 12px 20px;
        border-radius: 25px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
    }

    .filter-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s;
    }

    .filter-btn:hover::before {
        left: 100%;
    }

    .filter-btn:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .filter-btn.active {
        background: var(--accent-color);
        color: white;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    .filter-btn.active:hover {
        background: var(--accent-light);
    }

    /* Main Content Area */
    .resources-display {
        position: relative;
        z-index: 10;
        padding: 60px 0;
        background: var(--bg-primary);
        margin-top: -30px;
        border-radius: 30px 30px 0 0;
        box-shadow: var(--shadow-xl);
    }

    /* Resources Count */
    .resources-count {
        text-align: center;
        margin-bottom: 40px;
    }

    .resources-count p {
        color: var(--text-secondary);
        font-size: 1rem;
        font-weight: 500;
    }

    /* Resources Grid */
    .resources-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 24px;
        margin-top: 40px;
    }

    /* Resource Card */
    .resource-card {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
        position: relative;
    }

    .resource-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .resource-card:hover::before {
        transform: scaleX(1);
    }

    .resource-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-color);
    }

    /* Resource Image */
    .resource-image {
        position: relative;
        height: 200px;
        overflow: hidden;
    }

    .resource-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    .resource-card:hover .resource-image img {
        transform: scale(1.05);
    }

    .resource-type-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: var(--primary-color);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        box-shadow: var(--shadow-md);
        backdrop-filter: blur(10px);
    }

    /* Resource Content */
    .resource-content {
        padding: 24px;
    }

    .resource-content h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 12px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .resource-content > p {
        color: var(--text-secondary);
        margin-bottom: 20px;
        line-height: 1.5;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Resource Info */
    .resource-info {
        margin-bottom: 20px;
    }

    .resource-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }

    .meta-item {
        color: var(--text-secondary);
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .meta-item i {
        color: var(--primary-color);
    }

    /* Resource Actions */
    .resource-actions {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .resource-btn {
        padding: 10px 16px;
        border-radius: 12px;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        box-shadow: var(--shadow-sm);
    }

    .resource-btn.primary {
        background: var(--primary-color);
        color: white;
    }

    .resource-btn.primary:hover {
        background: var(--primary-light);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .resource-btn.secondary {
        background: var(--bg-tertiary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .resource-btn.secondary:hover {
        background: var(--bg-secondary);
        border-color: var(--primary-color);
        transform: translateY(-1px);
    }

    .view-details-btn {
        width: 100%;
        justify-content: center;
        background: var(--bg-secondary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .view-details-btn:hover {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
    }

    /* No Resources State */
    .no-resources {
        text-align: center;
        
        padding: 80px 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    .no-resources i {
        font-size: 4rem;
        color: var(--text-secondary);
        margin-bottom: 24px;
        opacity: 0.6;
    }

    .no-resources h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 12px;
    }

    .no-resources p {
        color: var(--text-secondary);
        margin-bottom: 32px;
        line-height: 1.6;
    }

    .no-resources-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    
/* Search Button - Smaller Size */
.search-btn {
    background: var(--primary-color);
    color: white;
    padding: 8px 16px; /* Reduced from 12px 20px */
    border-radius: 8px; /* Reduced from 10px */
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem; /* Added smaller font size */
    display: flex;
    align-items: center;
    gap: 6px; /* Reduced from 8px */
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
    height: auto; /* Ensure consistent height */
    min-height: 36px; /* Set minimum height for touch targets */
}
.search-btn:hover {
    background: var(--primary-light);
    transform: translateY(-1px); /* Reduced from -2px */
    box-shadow: var(--shadow-md);
}

.search-btn[style] {
    background: rgba(255, 255, 255, 0.2) !important;
    backdrop-filter: blur(10px);
    border: 1px solid var(--border-color);
    color: var(--text-primary) !important;
    padding: 8px 16px !important; /* Maintain smaller padding */
    font-size: 0.875rem !important; /* Maintain smaller font */
}

/* Search button icon sizing */
.search-btn i {
    font-size: 0.875rem; /* Smaller icon */
}

    /* Loading Animation */
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

    @keyframes shimmer {
        0% {
            background-position: -200px 0;
        }
        100% {
            background-position: calc(200px + 100%) 0;
        }
    }

    .loading-card {
        background: linear-gradient(90deg, var(--bg-secondary) 25%, var(--bg-tertiary) 37%, var(--bg-secondary) 63%);
        background-size: 400px 100%;
        animation: shimmer 1.5s ease-in-out infinite;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        body {
            padding-top: 70px;
        }
        
        .header {
            height: 70px;
        }
        
        .resources-hero {
            padding: 40px 0 30px;
        }

        .resources-display {
            padding: 40px 0;
            margin-top: -20px;
            border-radius: 20px 20px 0 0;
        }

        .resources-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .filter-buttons {
            gap: 8px;
        }

        .filter-btn {
            padding: 10px 16px;
            font-size: 0.8rem;
        }

        .breadcrumb {
            font-size: 13px;
        }

        .resource-actions {
            flex-direction: column;
        }

        .resource-btn {
            justify-content: center;
        }
    }

    @media (max-width: 640px) {
        .resources-grid {
            grid-template-columns: 1fr;
        }

        .container {
            padding: 0 16px;
        }

        .filter-buttons {
            flex-direction: column;
            align-items: center;
        }

        .filter-btn {
            min-width: 200px;
            justify-content: center;
        }

        .no-resources-actions {
            flex-direction: column;
            align-items: center;
        }

        .search-btn {
            min-width: 200px;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .resources-hero h1 {
            font-size: 1.75rem;
        }

        .resource-content {
            padding: 20px;
        }

        .resource-image {
            height: 180px;
        }

        .meta-item {
            font-size: 0.8rem;
        }
    }
</style>

<div class="resources-page">
    <!-- Hero Section -->
    <section class="resources-hero">
        <div class="container">
            <div class="resources-hero-content">
                <nav class="breadcrumb">
                    <a href="index.php">
                        <i class="fas fa-home"></i>
                        Home
                    </a>
                    <span>
                        <i class="fas fa-chevron-right"></i>
                    </span>
                    <a href="board.php?id=<?= $board_id ?>&name=<?= urlencode($board_name) ?>">
                        <?= htmlspecialchars($board_name) ?>
                    </a>
                    <span>
                        <i class="fas fa-chevron-right"></i>
                    </span>
                    <span><?= htmlspecialchars($class_name) ?></span>
                    <span>
                        <i class="fas fa-chevron-right"></i>
                    </span>
                    <span><?= htmlspecialchars($subject_name) ?></span>
                </nav>
                
                <h1><?= htmlspecialchars($subject_name) ?> - <?= $type_display ?></h1>
                <p><?= htmlspecialchars($class_name) ?> • <?= htmlspecialchars($board_name) ?> Board</p>
                
                <!-- Filter Buttons -->
                <div class="filter-buttons">
                    <a href="resources.php?board_id=<?= $board_id ?>&board_name=<?= urlencode($board_name) ?>&class_id=<?= $class_id ?>&class_name=<?= urlencode($class_name) ?>&subject_id=<?= $subject_id ?>&subject_name=<?= urlencode($subject_name) ?>" 
                       class="filter-btn <?= empty($type) ? 'active' : '' ?>">
                        <i class="fas fa-th-large"></i>
                        All Resources
                    </a>
                    <a href="resources.php?board_id=<?= $board_id ?>&board_name=<?= urlencode($board_name) ?>&class_id=<?= $class_id ?>&class_name=<?= urlencode($class_name) ?>&subject_id=<?= $subject_id ?>&subject_name=<?= urlencode($subject_name) ?>&type=video" 
                       class="filter-btn <?= $type == 'video' ? 'active' : '' ?>">
                        <i class="fas fa-play-circle"></i>
                        Videos
                    </a>
                    <a href="resources.php?board_id=<?= $board_id ?>&board_name=<?= urlencode($board_name) ?>&class_id=<?= $class_id ?>&class_name=<?= urlencode($class_name) ?>&subject_id=<?= $subject_id ?>&subject_name=<?= urlencode($subject_name) ?>&type=books" 
                       class="filter-btn <?= $type == 'books' ? 'active' : '' ?>">
                        <i class="fas fa-book-open"></i>
                        Books
                    </a>
                    <a href="resources.php?board_id=<?= $board_id ?>&board_name=<?= urlencode($board_name) ?>&class_id=<?= $class_id ?>&class_name=<?= urlencode($class_name) ?>&subject_id=<?= $subject_id ?>&subject_name=<?= urlencode($subject_name) ?>&type=notes" 
                       class="filter-btn <?= $type == 'notes' ? 'active' : '' ?>">
                        <i class="fas fa-sticky-note"></i>
                        Notes
                    </a>
                    <a href="resources.php?board_id=<?= $board_id ?>&board_name=<?= urlencode($board_name) ?>&class_id=<?= $class_id ?>&class_name=<?= urlencode($class_name) ?>&subject_id=<?= $subject_id ?>&subject_name=<?= urlencode($subject_name) ?>&type=past_paper" 
                       class="filter-btn <?= $type == 'past_paper' ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i>
                        Past Papers
                    </a>
                    <a href="resources.php?board_id=<?= $board_id ?>&board_name=<?= urlencode($board_name) ?>&class_id=<?= $class_id ?>&class_name=<?= urlencode($class_name) ?>&subject_id=<?= $subject_id ?>&subject_name=<?= urlencode($subject_name) ?>&type=guess_paper" 
                       class="filter-btn <?= $type == 'guess_paper' ? 'active' : '' ?>">
                        <i class="fas fa-lightbulb"></i>
                        Guess Papers
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Resources Display -->
    <section class="resources-display">
        <div class="container">
            <?php if (empty($resources)): ?>
                <div class="no-resources">
                    <i class="fas fa-folder-open"></i>
                    <h3>No Resources Found</h3>
                    <p>We're working hard to add more study materials for this subject. Please check back later or explore other resource types.</p>
                    <div class="no-resources-actions">
                        <a href="board.php?id=<?= $board_id ?>&name=<?= urlencode($board_name) ?>" class="search-btn">
                            <i class="fas fa-arrow-left"></i>
                            Back to Subjects
                        </a>
                        <a href="resources.php?board_id=<?= $board_id ?>&board_name=<?= urlencode($board_name) ?>&class_id=<?= $class_id ?>&class_name=<?= urlencode($class_name) ?>&subject_id=<?= $subject_id ?>&subject_name=<?= urlencode($subject_name) ?>" class="search-btn" style="background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);">
                            <i class="fas fa-th-large"></i>
                            View All Types
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="resources-count">
                    <p>Found <?= count($resources) ?> resource<?= count($resources) !== 1 ? 's' : '' ?> for <?= htmlspecialchars($subject_name) ?></p>
                </div>
                
                <div class="resources-grid">
                    <?php foreach($resources as $resource): ?>
                        <div class="resource-card" onclick="window.location.href='resource-detail.php?id=<?= $resource['id'] ?>'">
                            <div class="resource-image">
                                <img src="<?= !empty($resource['front_image']) && file_exists($resource['front_image']) ? htmlspecialchars($resource['front_image']) : 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80' ?>" 
                                     alt="<?= htmlspecialchars($resource['heading']) ?>"
                                     onerror="this.src='https://images.unsplash.com/photo-1543002588-bfa74002ed7e?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80'">
                                
                                <div class="resource-type-badge">
                                    <?php
                                    $type_icons = [
                                        'video' => 'fas fa-play-circle',
                                        'books' => 'fas fa-book-open',
                                        'notes' => 'fas fa-sticky-note',
                                        'past_paper' => 'fas fa-file-alt',
                                        'guess_paper' => 'fas fa-lightbulb'
                                    ];
                                    $icon = $type_icons[$resource['type']] ?? 'fas fa-file';
                                    ?>
                                    <i class="<?= $icon ?>"></i>
                                    <?= ucfirst(str_replace('_', ' ', htmlspecialchars($resource['type']))) ?>
                                </div>
                            </div>
                            
                            <div class="resource-content">
                                <h3><?= htmlspecialchars($resource['heading']) ?></h3>
                                <p><?= htmlspecialchars($resource['description']) ?></p>
                                
                                <div class="resource-info">
                                    <div class="resource-meta">
                                        <span class="meta-item">
                                            <i class="fas fa-tag"></i>
                                            <?= ucfirst(str_replace('_', ' ', htmlspecialchars($resource['type']))) ?>
                                        </span>
                                        <span class="meta-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <?= date('M d, Y', strtotime($resource['created_at'] ?? 'now')) ?>
                                        </span>
                                        <?php if (!empty($resource['keywords'])): ?>
                                        <span class="meta-item">
                                            <i class="fas fa-hashtag"></i>
                                            <?= htmlspecialchars(substr($resource['keywords'], 0, 20)) ?><?= strlen($resource['keywords']) > 20 ? '...' : '' ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="resource-actions">
                                    <?php if ($resource['type'] === 'video'): ?>
                                        <?php if (!empty($resource['video_link'])): ?>
                                            <a href="<?= htmlspecialchars($resource['video_link']) ?>" target="_blank" class="resource-btn primary" onclick="event.stopPropagation();">
                                                <i class="fas fa-play"></i>
                                                Watch Video
                                            </a>
                                        <?php endif; ?>
                                        
                                        <!-- Also provide a download/access button that goes through download.php -->
                                        <a href="download.php?id=<?= $resource['id'] ?>" class="resource-btn <?= !empty($resource['video_link']) ? 'secondary' : 'primary' ?>" onclick="event.stopPropagation();">
                                            <i class="fas fa-external-link-alt"></i>
                                            Open Video
                                        </a>
                                    <?php else: ?>
                                        <!-- For non-video resources, show download button -->
                                        <?php if (!empty($resource['file'])): ?>
                                            <a href="download.php?id=<?= $resource['id'] ?>" class="resource-btn primary" onclick="event.stopPropagation();">
                                                <i class="fas fa-download"></i>
                                                Download
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <a href="resource-detail.php?id=<?= $resource['id'] ?>" class="resource-btn view-details-btn" onclick="event.stopPropagation();">
                                    <i class="fas fa-eye"></i>
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
// Modern JavaScript for the redesigned resources page

document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 Resources page loaded successfully!');
    
    // Initialize page animations
    initializeAnimations();
    
    // Setup image loading
    setupImageLoading();
    
    // Setup card interactions
    setupCardInteractions();
    
    // Setup filter animations
    setupFilterAnimations();
});

// Initialize page animations
function initializeAnimations() {
    // Animate hero section
    const hero = document.querySelector('.resources-hero');
    if (hero) {
        hero.style.opacity = '0';
        hero.style.transform = 'translateY(30px)';
        setTimeout(() => {
            hero.style.transition = 'all 0.6s ease';
            hero.style.opacity = '1';
            hero.style.transform = 'translateY(0)';
        }, 100);
    }
    
    // Animate filter buttons
    const filterButtons = document.querySelectorAll('.filter-btn');
    filterButtons.forEach((btn, index) => {
        btn.style.opacity = '0';
        btn.style.transform = 'translateY(20px)';
        setTimeout(() => {
            btn.style.transition = 'all 0.4s ease';
            btn.style.opacity = '1';
            btn.style.transform = 'translateY(0)';
        }, 200 + (index * 50));
    });
    
    // Animate resource cards with staggered effect
    const cards = document.querySelectorAll('.resource-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, 400 + (index * 100));
    });
    
    // Animate resources count
    const resourcesCount = document.querySelector('.resources-count');
    if (resourcesCount) {
        resourcesCount.style.opacity = '0';
        resourcesCount.style.transform = 'translateY(20px)';
        setTimeout(() => {
            resourcesCount.style.transition = 'all 0.4s ease';
            resourcesCount.style.opacity = '1';
            resourcesCount.style.transform = 'translateY(0)';
        }, 300);
    }
    
    // Animate no resources state
    const noResources = document.querySelector('.no-resources');
    if (noResources) {
        noResources.style.opacity = '0';
        noResources.style.transform = 'translateY(30px)';
        setTimeout(() => {
            noResources.style.transition = 'all 0.6s ease';
            noResources.style.opacity = '1';
            noResources.style.transform = 'translateY(0)';
        }, 400);
    }
}

// Setup image loading with fade-in effect
function setupImageLoading() {
    const images = document.querySelectorAll('.resource-image img');
    
    images.forEach(img => {
        // Show loading placeholder
        img.style.opacity = '0';
        img.style.background = 'linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 37%, #f0f0f0 63%)';
        img.style.backgroundSize = '400px 100%';
        
        // Handle successful load
        img.addEventListener('load', function() {
            this.style.transition = 'opacity 0.3s ease';
            this.style.opacity = '1';
            this.style.background = 'none';
        });
        
        // Handle error
        img.addEventListener('error', function() {
            this.style.transition = 'opacity 0.3s ease';
            this.style.opacity = '0.8';
            this.style.background = 'none';
        });
        
        // If image is already loaded (cached)
        if (img.complete) {
            img.style.opacity = '1';
            img.style.background = 'none';
        }
    });
}

// Setup card interactions
function setupCardInteractions() {
    // Prevent card click when clicking on buttons
    const buttons = document.querySelectorAll('.resource-btn');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // Add ripple effect
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
            `;
            
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.resource-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const image = this.querySelector('.resource-image img');
            if (image) {
                image.style.transform = 'scale(1.05)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const image = this.querySelector('.resource-image img');
            if (image) {
                image.style.transform = 'scale(1)';
            }
        });
    });
}

// Setup filter animations
function setupFilterAnimations() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Add loading state
            const originalText = this.innerHTML;
            this.style.pointerEvents = 'none';
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            
            // Reset after navigation (this won't actually run due to page change, but good practice)
            setTimeout(() => {
                this.innerHTML = originalText;
                this.style.pointerEvents = 'auto';
            }, 1000);
        });
        
        // Add subtle animation on hover
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
}

// Add CSS for ripple effect
const rippleCSS = `
    @keyframes ripple {
        0% {
            transform: scale(0);
            opacity: 1;
        }
        100% {
            transform: scale(1);
            opacity: 0;
        }
    }
    
    .resource-btn {
        position: relative;
        overflow: hidden;
    }
    
    .resource-card {
        will-change: transform;
    }
    
    .resource-image img {
        will-change: transform;
    }
    
    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }
    
    /* Selection styles */
    ::selection {
        background-color: var(--primary-color);
        color: white;
    }
    
    ::-moz-selection {
        background-color: var(--primary-color);
        color: white;
    }
    
    /* Focus styles for accessibility */
    .filter-btn:focus,
    .resource-btn:focus,
    .search-btn:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }
    
    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
    
    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .resource-card {
            border: 2px solid var(--text-primary);
        }
        
        .filter-btn {
            border: 2px solid;
        }
    }
    
    /* Print styles */
    @media print {
        .resources-hero {
            background: white !important;
            color: black !important;
        }
        
        .resource-card {
            border: 1px solid black;
            box-shadow: none;
            break-inside: avoid;
        }
        
        .resource-actions,
        .filter-buttons {
            display: none;
        }
    }
`;

// Inject the additional CSS
const styleSheet = document.createElement('style');
styleSheet.textContent = rippleCSS;
document.head.appendChild(styleSheet);

// Intersection Observer for scroll-triggered animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-in');
        }
    });
}, observerOptions);

// Observe elements for scroll animations
document.querySelectorAll('.resource-card, .filter-btn, .no-resources').forEach(el => {
    observer.observe(el);
});

// Performance optimization: Lazy load images
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            }
        });
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Add keyboard navigation support
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        // Close any open modals or return to previous page
        if (document.referrer) {
            window.history.back();
        }
    }
});

console.log('✨ Resources page fully initialized with modern interactions!');
</script>

<?php include 'includes/footer.php'; ?>