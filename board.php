<?php
// ===== FIXED CLASS ORDERING FOR BOARD PAGE =====
// File: board.php
include 'config/db.php';
include 'includes/header.php';

$board_id = $_GET['id'] ?? '';
$board_name = $_GET['name'] ?? '';

if (!$board_id) {
    header('Location: index.php');
    exit;
}

// Get board details
$stmt = $pdo->prepare("SELECT * FROM boards WHERE id = ?");
$stmt->execute([$board_id]);
$board = $stmt->fetch();

if (!$board) {
    header('Location: index.php');
    exit;
}

// FIXED: Get classes with proper numerical ordering (9, 10, 11, 12)
// This query extracts numbers from class names and sorts them numerically
$classes = $pdo->query("
    SELECT * FROM classes 
    ORDER BY 
        CASE 
            WHEN class_name REGEXP '^Class [0-9]+' THEN 
                CAST(REGEXP_REPLACE(class_name, '^Class ([0-9]+).*', '\\\\1') AS UNSIGNED)
            WHEN class_name REGEXP '^Grade [0-9]+' THEN 
                CAST(REGEXP_REPLACE(class_name, '^Grade ([0-9]+).*', '\\\\1') AS UNSIGNED)
            WHEN class_name REGEXP '^[0-9]+' THEN 
                CAST(REGEXP_REPLACE(class_name, '^([0-9]+).*', '\\\\1') AS UNSIGNED)
            ELSE 999 
        END ASC,
        class_name ASC
")->fetchAll();

?>

<style>
    /* Modern Design System */
    :root {
        --primary-color: #00027A;
        --primary-light: #00027A;
        --primary-dark: #00027A;
        --accent-color: #FF6B35;
        --accent-light: #FF8A65;
        --success-color: #10B981;
        --warning-color: #F59E0B;
        --error-color: #EF4444;
        --text-primary: #838a95ff;
        --text-secondary: #6B7280;
        --bg-primary: #ffffffff;
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

    /* Main Board Container */
    .board-container {
        min-height: calc(100vh - 80px);
        background: var(--gradient-secondary);
        position: relative;
        overflow: hidden;
    }

    /* Decorative Background Elements */
    .board-container::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(0, 2, 122, 0.08) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .board-container::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 107, 53, 0.06) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    /* Hero Section - Redesigned */
    .board-hero {
        position: relative;
        z-index: 10;
        padding: 60px 0 40px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        color: white;
        margin-bottom: 0;
    }

    .board-hero::before {
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

    /* Breadcrumb Redesign */
    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        font-size: 14px;
        opacity: 0.9;
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

    .breadcrumb-separator {
        color: rgba(255, 255, 255, 0.5);
        font-size: 12px;
    }

    /* Hero Content */
    .hero-content {
        text-align: center;
        max-width: 800px;
        margin: 0 auto;
    }

    .board-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-weight: 800;
        margin-bottom: 16px;
        background: linear-gradient(135deg, #FFFFFF 0%, #E0E7FF 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .board-subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
        margin-bottom: 32px;
        font-weight: 400;
    }

    .hero-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 20px;
        max-width: 500px;
        margin: 0 auto;
    }

    .stat-item {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 16px;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        display: block;
        color: var(--accent-color);
    }

    .stat-label {
        font-size: 0.875rem;
        opacity: 0.8;
    }

    /* Main Content Area */
    .main-content {
        position: relative;
        z-index: 10;
        padding: 60px 0;
        /* background: var(--bg-primary); */
        margin-top: -30px;
        border-radius: 30px 30px 0 0;
        box-shadow: var(--shadow-xl);
    }

    /* Section Titles */
    .section-header {
        text-align: center;
        margin-bottom: 48px;
    }

    .section-title {
        font-size: clamp(1.875rem, 4vw, 2.25rem);
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 12px;
        position: relative;
        display: inline-block;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        border-radius: 2px;
    }

    .section-subtitle {
        font-size: 1.125rem;
        color: var(--text-secondary);
        max-width: 600px;
        margin: 0 auto;
    }

    /* Class Selector - Modern Design */
    .class-selector {
        max-width: 500px;
        margin: 0 auto 60px;
        position: relative;
    }

    .custom-select {
        position: relative;
        display: block;
    }

    .select-button {
        width: 100%;
        padding: 20px 24px;
        background: var(--bg-primary);
        border: 2px solid var(--border-color);
        border-radius: 16px;
        font-size: 1.125rem;
        color: var(--text-primary);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.3s ease;
        box-shadow: var(--shadow-sm);
    }

    .select-button:hover {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .select-button.active {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 2, 122, 0.1);
    }

    .select-icon {
        transition: transform 0.3s ease;
        color: var(--primary-color);
    }

    .select-button.active .select-icon {
        transform: rotate(180deg);
    }

    .select-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--bg-primary);
        border: 2px solid var(--primary-color);
        border-top: none;
        border-radius: 0 0 16px 16px;
        box-shadow: var(--shadow-lg);
        max-height: 300px;
        overflow-y: auto;
        z-index: 1000;
        opacity: 0;
        transform: translateY(-10px);
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .select-dropdown.active {
        opacity: 1;
        transform: translateY(0);
        visibility: visible;
    }

    .select-option {
        padding: 16px 24px;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 1px solid var(--bg-tertiary);
    }

    .select-option:hover {
        background: var(--bg-secondary);
        color: var(--primary-color);
    }

    .select-option:last-child {
        border-bottom: none;
    }

    /* Subjects Section - Card Grid */
    .subjects-section,
    .resources-section {
        opacity: 0;
        visibility: hidden;
        transform: translateY(30px);
        transition: all 0.5s ease;
        margin-top: 40px;
    }

    .subjects-section.active,
    .resources-section.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .subjects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-top: 40px;
    }

    .subject-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 16px; /* Reduced from 20px */
    padding: 20px; /* Reduced from 28px */
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

    .subject-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px; /* Reduced from 4px */
    background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

    .subject-card:hover::before {
        transform: scaleX(1);
    }

    .subject-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-color);
    }

    .subject-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        font-size: 2rem;
        color: white;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .subject-icon::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        transform: rotate(-45deg);
        transition: all 0.6s ease;
    }

    .subject-card:hover .subject-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .subject-card:hover .subject-icon::before {
        transform: rotate(-45deg) translate(50%, 50%);
    }

    .subject-card h3 {
        font-size: 1.375rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 12px;
    }

    .subject-card p {
        color: var(--text-secondary);
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .subject-resources {
        display: flex;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
    }

   
/* Replace the existing .resource-badge style with this updated version */

.resource-badge {
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 4px;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
}

/* Specific colors for each resource type */
.resource-badge:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Videos - Red/YouTube color */
.resource-badge:has(i.fa-video) {
    background: linear-gradient(135deg, #FF0000, #CC0000);
}

/* Books - Blue color */
.resource-badge:has(i.fa-book) {
    background: linear-gradient(135deg, #3B82F6, #1D4ED8);
}

/* Notes - Orange color */
.resource-badge:has(i.fa-sticky-note) {
    background: linear-gradient(135deg, #F97316, #EA580C);
}

/* Tests - Purple color */
.resource-badge:has(i.fa-clipboard-check) {
    background: linear-gradient(135deg, #8B5CF6, #7C3AED);
}

/* Alternative method using data attributes (more reliable) */
/* Add data-type attribute to badges in HTML and use these styles instead */

.resource-badge[data-type="video"] {
    background: linear-gradient(135deg, #FF0000, #CC0000);
}

.resource-badge[data-type="book"] {
    background: linear-gradient(135deg, #3B82F6, #1D4ED8);
}

.resource-badge[data-type="notes"] {
    background: linear-gradient(135deg, #F97316, #EA580C);
}

.resource-badge[data-type="tests"] {
    background: linear-gradient(135deg, #8B5CF6, #7C3AED);
}

    /* Resources Section */
    .resources-section {
        background: var(--bg-secondary);
        border-radius: 24px;
        padding: 48px 32px;
        margin-top: 60px;
        border: 1px solid var(--border-color);
    }

    .resource-types-grid {
        display: grid;
        /* grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); */
        grid-template-columns: repeat(3, 1fr);
      grid-template-rows: repeat(2, 1fr);
        gap: 20px;
        margin-top: 40px;
    }

    
.resource-type-card {
    background: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 12px; /* Reduced from 16px */
    padding: 20px 16px; /* Reduced from 28px 20px */
    text-align: center;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.resource-type-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-color);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

    .resource-type-card:hover::before {
        transform: scaleX(1);
    }

    .resource-type-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-color);
    }

    .resource-type-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.5rem;
        color: white;
        transition: all 0.3s ease;
    }

    .resource-type-card:hover .resource-type-icon {
        transform: scale(1.1);
        box-shadow: var(--shadow-md);
    }

    .resource-type-card h4 {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .resource-type-card p {
        font-size: 0.875rem;
        color: var(--text-secondary);
        margin: 0;
    }

    /* Loading States */
    .loading {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-secondary);
    }

    .loading-icon {
        width: 60px;
        height: 60px;
        border: 4px solid var(--bg-tertiary);
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    .loading h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .error-state {
        text-align: center;
        padding: 60px 20px;
        background: var(--bg-primary);
        border-radius: 16px;
        border: 1px solid var(--border-color);
    }

    .error-icon {
        width: 60px;
        height: 60px;
        background: var(--error-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: white;
        font-size: 1.5rem;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        border: none;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: var(--shadow-sm);
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-light);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary {
        background: var(--bg-tertiary);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--bg-secondary);
        border-color: var(--primary-color);
    }

    /* Animations */
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        body {
            padding-top: 70px;
        }
        
        .header {
            height: 70px;
        }
        
        .board-hero {
            padding: 40px 0 30px;
        }

        .hero-stats {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .stat-item {
            padding: 12px;
        }

        .main-content {
            padding: 40px 0;
            margin-top: -20px;
            border-radius: 20px 20px 0 0;
        }

        .subjects-grid,
        .resource-types-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .section-header {
            margin-bottom: 32px;
        }

        .resources-section {
            padding: 32px 20px;
            margin-top: 40px;
        }

        .class-selector {
            margin-bottom: 40px;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 0 16px;
        }

        .hero-stats {
            grid-template-columns: 1fr;
        }

        .board-title {
            font-size: 2rem;
        }

        .select-button,
        .select-option {
            padding: 16px 20px;
        }

        .subject-card,
        .resource-type-card {
            padding: 20px;
        }
    }

    /* Dark mode support (optional) */
    @media (prefers-color-scheme: dark) {
        :root {
            --text-primary: #F9FAFB;
            --text-secondary: #D1D5DB;
            --bg-primary: #111827;
            --bg-secondary: #1F2937;
            --bg-tertiary: #374151;
            --border-color: #374151;
        }
    }
</style>

<div class="board-container">
    <!-- Hero Section -->
    <section class="board-hero">
        <div class="container">
            <nav class="breadcrumb">
                <a href="index.php">
                    <i class="fas fa-home"></i>
                    Home
                </a>
                <span class="breadcrumb-separator">
                    <i class="fas fa-chevron-right"></i>
                </span>
                <span><?= htmlspecialchars($board['board_name']) ?></span>
            </nav>
            
            <div class="hero-content">
                <h1 class="board-title"><?= htmlspecialchars($board['board_name']) ?></h1>
                <p class="board-subtitle">
                    Comprehensive study materials and resources for <?= htmlspecialchars($board['board_name']) ?> students
                </p>
                

            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Class Selection Section -->
            <section class="class-selection-section">
                <div class="section-header">
                    <h2 class="section-title">Select Your Class</h2>
                    <p class="section-subtitle">Choose your academic class to explore subjects and study materials</p>
                </div>
                
                <div class="class-selector">
                    <div class="custom-select">
                        <button class="select-button" id="classSelectButton" onclick="toggleClassDropdown()">
                            <span id="selectedClassText">Choose your class level</span>
                            <i class="fas fa-chevron-down select-icon"></i>
                        </button>
                        <div class="select-dropdown" id="classDropdown">
                            <?php foreach($classes as $class): ?>
                                <div class="select-option" onclick="selectClass(<?= $class['id'] ?>, '<?= htmlspecialchars($class['class_name']) ?>')">
                                    <?= htmlspecialchars($class['class_name']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Subjects Section -->
                <div class="subjects-section" id="subjectsSection">
                    <div class="section-header">
                        <h3 class="section-title">Choose Your Subject</h3>
                        <p class="section-subtitle">Select a subject to access comprehensive study materials</p>
                    </div>
                    <div class="subjects-grid" id="subjectsGrid">
                        <!-- Subjects will be loaded here dynamically -->
                    </div>
                </div>

                <!-- Resources Section -->
                <div class="resources-section" id="resourcesSection">
                    <div class="section-header">
                        <h3 class="section-title">Choose Resource Type</h3>
                        <p class="section-subtitle">Select the type of study material you need</p>
                    </div>
                    <div class="resource-types-grid">
                        <a href="#" class="resource-type-card" onclick="redirectToResources('notes')">
                            <div class="resource-type-icon">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <h4>Study Notes</h4>
                            <p>Comprehensive notes and summaries</p>
                        </a>
                        <a href="#" class="resource-type-card" onclick="redirectToResources('pdf')">
                            <div class="resource-type-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h4>Textbooks</h4>
                            <p>Digital textbooks and references</p>
                        </a>
                        <a href="#" class="resource-type-card" onclick="redirectToResources('test')">
                            <div class="resource-type-icon">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <h4>Past Papers</h4>
                            <p>Previous examination papers</p>
                        </a>
                        <a href="#" class="resource-type-card" onclick="redirectToResources('video')">
                            <div class="resource-type-icon">
                                <i class="fas fa-play-circle"></i>
                            </div>
                            <h4>Video Lectures</h4>
                            <p>Interactive video lessons</p>
                        </a>
                        <a href="#" class="resource-type-card" onclick="redirectToResources('test')">
                            <div class="resource-type-icon">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <h4>Guess Papers</h4>
                            <p>Predicted exam questions</p>
                        </a>
                        <a href="#" class="resource-type-card" onclick="redirectToResources('online-test')">
                            <div class="resource-type-icon">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <h4>Online Tests</h4>
                            <p>Interactive assessments</p>
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
// Modern JavaScript for the redesigned board page

// Global state variables
let selectedBoardId = <?= $board_id ?>;
let selectedBoardName = '<?= htmlspecialchars($board['board_name']) ?>';
let selectedClassId = '';
let selectedClassName = '';
let selectedSubjectId = '';
let selectedSubjectName = '';
let isClassDropdownOpen = false;

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎉 Board page loaded successfully!');
    console.log(`📚 Board: ${selectedBoardName} (ID: ${selectedBoardId})`);
    
    // Add smooth animations
    animatePageElements();
    
    // Test AJAX endpoint
    testAjaxEndpoint();
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const classSelector = document.querySelector('.custom-select');
        if (!classSelector.contains(event.target)) {
            closeClassDropdown();
        }
    });
});

// Animate page elements on load
function animatePageElements() {
    const elements = [
        '.board-hero',
        '.class-selection-section',
    ];
    
    elements.forEach((selector, index) => {
        const element = document.querySelector(selector);
        if (element) {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                element.style.transition = 'all 0.6s ease';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 200);
        }
    });
}

// Toggle class dropdown
function toggleClassDropdown() {
    const button = document.getElementById('classSelectButton');
    const dropdown = document.getElementById('classDropdown');
    
    if (isClassDropdownOpen) {
        closeClassDropdown();
    } else {
        openClassDropdown();
    }
}

// Open class dropdown
function openClassDropdown() {
    const button = document.getElementById('classSelectButton');
    const dropdown = document.getElementById('classDropdown');
    
    button.classList.add('active');
    dropdown.classList.add('active');
    isClassDropdownOpen = true;
}

// Close class dropdown
function closeClassDropdown() {
    const button = document.getElementById('classSelectButton');
    const dropdown = document.getElementById('classDropdown');
    
    button.classList.remove('active');
    dropdown.classList.remove('active');
    isClassDropdownOpen = false;
}

// Select a class
function selectClass(classId, className) {
    selectedClassId = classId;
    selectedClassName = className;
    
    // Update button text
    document.getElementById('selectedClassText').textContent = className;
    
    // Close dropdown
    closeClassDropdown();
    
    // Load subjects
    loadSubjects(classId);
    
    console.log(`✅ Selected class: ${className} (ID: ${classId})`);
}

// Load subjects for selected class
function loadSubjects(classId) {
    console.log(`🔍 Loading subjects for class ID: ${classId}`);
    
    if (!classId) {
        hideSection('subjectsSection');
        hideSection('resourcesSection');
        return;
    }

    // Show loading state
    showSubjectsLoading();
    showSection('subjectsSection');
    hideSection('resourcesSection');

    // Scroll to subjects section
    setTimeout(() => {
        document.getElementById('subjectsSection').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }, 300);

    // Fetch subjects
    fetch(`ajax/get_subjects.php?class_id=${encodeURIComponent(classId)}`)
        .then(response => {
            console.log(`📡 Response status: ${response.status}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.text();
        })
        .then(text => {
            console.log('📥 Raw response received');
            
            try {
                const data = JSON.parse(text);
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                displaySubjects(data, classId);
                
            } catch (jsonError) {
                console.error('❌ JSON parse error:', jsonError);
                throw new Error('Invalid response from server');
            }
        })
        .catch(error => {
            console.error('❌ Error loading subjects:', error);
            showSubjectsError(classId, error.message);
        });
}

// Show loading state for subjects
function showSubjectsLoading() {
    const subjectsGrid = document.getElementById('subjectsGrid');
    subjectsGrid.innerHTML = `
        <div class="loading">
            <div class="loading-icon"></div>
            <h3>Loading Subjects</h3>
            <p>Please wait while we fetch the subjects for ${selectedClassName}...</p>
        </div>
    `;
}

// Display subjects
function displaySubjects(subjects, classId) {
    const subjectsGrid = document.getElementById('subjectsGrid');
    
    if (!Array.isArray(subjects)) {
        console.error('❌ Subjects is not an array:', subjects);
        showSubjectsError(classId, 'Invalid data format received');
        return;
    }
    
    if (subjects.length === 0) {
        showNoSubjects(classId);
        return;
    }

    // Generate subjects HTML
    const subjectsHTML = subjects.map((subject, index) => {
        const subjectIcons = {
            'English': 'fas fa-language',
            'Mathematics': 'fas fa-calculator',
            'Physics': 'fas fa-atom',
            'Chemistry': 'fas fa-flask',
            'Biology': 'fas fa-dna',
            'Urdu': 'fas fa-book',
            'Pakistan Studies': 'fas fa-flag',
            'Islamiat': 'fas fa-mosque',
            'Science': 'fas fa-microscope',
            'Social Studies': 'fas fa-globe-americas',
            'Computer Science': 'fas fa-laptop-code',
            'Computer': 'fas fa-desktop'
        };
        
        const icon = subjectIcons[subject.subject_name] || 'fas fa-book-open';

        return `
            <div class="subject-card" 
                 onclick="selectSubject(${subject.id}, '${subject.subject_name.replace(/'/g, "\\'")}')">
                <div class="subject-icon">
                    <i class="${icon}"></i>
                </div>
                <h3>${subject.subject_name}</h3>
                <p>Comprehensive study materials and resources for ${subject.subject_name}</p>
                <div class="subject-resources">
                    <span class="resource-badge">
                        <i class="fas fa-video"></i> Videos
                    </span>
                    <span class="resource-badge">
                        <i class="fas fa-book"></i> Books
                    </span>
                    <span class="resource-badge">
                        <i class="fas fa-sticky-note"></i> Notes
                    </span>
                    <span class="resource-badge">
                        <i class="fas fa-clipboard-check"></i> Tests
                    </span>
                </div>
            </div>
        `;
    }).join('');

    subjectsGrid.innerHTML = subjectsHTML;
    
    // Animate cards
    const cards = subjectsGrid.querySelectorAll('.subject-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.4s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    console.log(`✅ Successfully loaded ${subjects.length} subjects`);
}

// Show error state for subjects
function showSubjectsError(classId, errorMessage) {
    const subjectsGrid = document.getElementById('subjectsGrid');
    subjectsGrid.innerHTML = `
        <div class="error-state">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3>Unable to Load Subjects</h3>
            <p style="color: var(--text-secondary); margin-bottom: 24px;">
                ${errorMessage}. Please try again or contact support if the problem persists.
            </p>
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <button class="btn btn-primary" onclick="loadSubjects(${classId})">
                    <i class="fas fa-redo"></i> Try Again
                </button>
                <button class="btn btn-secondary" onclick="window.location.reload()">
                    <i class="fas fa-sync"></i> Refresh Page
                </button>
            </div>
            <details style="margin-top: 24px; text-align: left; background: var(--bg-secondary); padding: 16px; border-radius: 8px;">
                <summary style="cursor: pointer; font-weight: 600; margin-bottom: 12px;">Debug Information</summary>
                <div style="font-size: 0.875rem; color: var(--text-secondary); line-height: 1.6;">
                    <p><strong>Class ID:</strong> ${classId}</p>
                    <p><strong>Board ID:</strong> ${selectedBoardId}</p>
                    <p><strong>Expected Endpoint:</strong> ajax/get_subjects.php</p>
                    <p><strong>Current URL:</strong> ${window.location.href}</p>
                    <p><strong>Time:</strong> ${new Date().toLocaleString()}</p>
                </div>
            </details>
        </div>
    `;
}

// Show no subjects state
function showNoSubjects(classId) {
    const subjectsGrid = document.getElementById('subjectsGrid');
    subjectsGrid.innerHTML = `
        <div class="error-state">
            <div class="error-icon" style="background: var(--warning-color);">
                <i class="fas fa-info-circle"></i>
            </div>
            <h3>No Subjects Available</h3>
            <p style="color: var(--text-secondary); margin-bottom: 24px;">
                No subjects are currently available for ${selectedClassName}. 
                Please check back later or contact support.
            </p>
            <button class="btn btn-primary" onclick="loadSubjects(${classId})">
                <i class="fas fa-redo"></i> Try Again
            </button>
        </div>
    `;
}

// Select a subject
function selectSubject(subjectId, subjectName) {
    selectedSubjectId = subjectId;
    selectedSubjectName = subjectName;

    console.log(`✅ Selected subject: ${subjectName} (ID: ${subjectId})`);

    // Show resources section
    showSection('resourcesSection');
    
    // Update resources section title
    const resourcesTitle = document.querySelector('#resourcesSection .section-title');
    if (resourcesTitle) {
        resourcesTitle.textContent = `${subjectName} Resources`;
    }
    
    const resourcesSubtitle = document.querySelector('#resourcesSection .section-subtitle');
    if (resourcesSubtitle) {
        resourcesSubtitle.textContent = `Choose the type of study material you need for ${subjectName}`;
    }
    
    // Scroll to resources section
    setTimeout(() => {
        document.getElementById('resourcesSection').scrollIntoView({ 
            behavior: 'smooth',
            block: 'start'
        });
    }, 300);
    
    // Add highlight animation to selected subject card
    const subjectCards = document.querySelectorAll('.subject-card');
    subjectCards.forEach(card => {
        card.classList.remove('selected');
    });
    
    // Find and highlight the selected card
    event.currentTarget.classList.add('selected');
}

// Show section with animation
function showSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.classList.add('active');
    }
}

// Hide section
function hideSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.classList.remove('active');
    }
}

// Redirect to resources page
function redirectToResources(resourceType) {
    if (!selectedClassId || !selectedSubjectId) {
        alert('Please select a class and subject first');
        return;
    }

    const params = new URLSearchParams({
        board_id: selectedBoardId,
        board_name: selectedBoardName,
        class_id: selectedClassId,
        class_name: selectedClassName,
        subject_id: selectedSubjectId,
        subject_name: selectedSubjectName,
        type: resourceType
    });

    console.log(`🚀 Redirecting to resources: ${resourceType}`);
    console.log(`📋 Parameters: ${params.toString()}`);
    
    window.location.href = `resources.php?${params.toString()}`;
}

// Test AJAX endpoint
function testAjaxEndpoint() {
    console.log('🧪 Testing AJAX endpoint...');
    
    fetch('ajax/get_subjects.php?class_id=1') // Test with a sample class ID
        .then(response => {
            console.log(`✅ AJAX endpoint test - Status: ${response.status}`);
            console.log(`📋 Content-Type: ${response.headers.get('content-type')}`);
            
            if (response.ok) {
                return response.text();
            } else {
                throw new Error(`HTTP ${response.status}`);
            }
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                console.log('✅ AJAX endpoint working! Sample data received.');
            } catch (e) {
                console.error('❌ AJAX endpoint returned invalid JSON');
            }
        })
        .catch(error => {
            console.error('❌ AJAX endpoint test failed:', error.message);
            console.log('💡 Please ensure:');
            console.log('   • ajax/get_subjects.php file exists');
            console.log('   • config/db.php file exists');
            console.log('   • Web server is running');
            console.log('   • File permissions are correct');
        });
}

// Add CSS for selected subject card
const additionalStyles = `
    .subject-card.selected {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(0, 2, 122, 0.1);
        transform: translateY(-8px);
    }
    
    .subject-card.selected::before {
        transform: scaleX(1);
    }
`;

// Inject additional styles
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);
</script>

<?php include 'includes/footer.php'; ?>