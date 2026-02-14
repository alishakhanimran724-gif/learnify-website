<?php
// ===== FIXED DOWNLOAD HANDLER =====
// File: download.php

// Start output buffering to prevent header issues
ob_start();

try {
    include 'config/db.php';
} catch (Exception $e) {
    showError('Database Error', 'Unable to connect to the database. Please try again later.', 'fas fa-database', '#ef4444');
    exit;
}

// Get and validate resource ID
$resource_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

if (!$resource_id || empty($resource_id)) {
    showError('Invalid Request', 'No resource ID provided. Please go back and try again.', 'fas fa-exclamation-triangle', '#f59e0b');
    exit;
}

try {
    // Get resource details
    $stmt = $pdo->prepare("SELECT * FROM subject_details WHERE id = ? LIMIT 1");
    $stmt->execute([$resource_id]);
    $resource = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    showError('Database Error', 'Error retrieving resource information. Please try again.', 'fas fa-database', '#ef4444');
    exit;
}

if (!$resource) {
    showError('Resource Not Found', 'The requested resource could not be found. It may have been moved or deleted.', 'fas fa-search', '#f59e0b');
    exit;
}

// Handle different resource types
if ($resource['type'] === 'video') {
    // For videos, redirect to YouTube or video link
    if (!empty($resource['video_link'])) {
        // Clean output buffer before redirect
        ob_end_clean();
        header('Location: ' . $resource['video_link']);
        exit;
    } else {
        showError('Video Link Not Available', 'The video link for this resource is not available at the moment.', 'fas fa-video', '#ef4444');
        exit;
    }
} else {
    // For non-video resources, handle file download
    if (empty($resource['file'])) {
        showError('File Not Available', 'No downloadable file is available for this resource.', 'fas fa-exclamation-triangle', '#f59e0b');
        exit;
    }

    $file_path = trim($resource['file']);
    
    // Security check: ensure file path doesn't contain directory traversal
    if (strpos($file_path, '../') !== false || strpos($file_path, '..\\') !== false) {
        showError('Security Error', 'Invalid file path detected.', 'fas fa-shield-alt', '#ef4444');
        exit;
    }
    
    // Check if file exists
    if (!file_exists($file_path) || !is_readable($file_path)) {
        showError('File Not Found', 'The requested file could not be found on the server or is not accessible.', 'fas fa-file-excel', '#f59e0b');
        exit;
    }

    // Generate a clean filename
    $file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
    $clean_title = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $resource['heading']);
    $clean_title = preg_replace('/\s+/', '_', trim($clean_title));
    $clean_title = substr($clean_title, 0, 100); // Limit filename length
    $file_name = $clean_title . '.' . $file_extension;

    // Get file info
    $file_size = filesize($file_path);
    $mime_type = getMimeType($file_path);

    // Clear any output buffers
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Set headers for file download
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . addslashes($file_name) . '"');
    header('Content-Length: ' . $file_size);
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Expires: 0');
    
    // Security headers
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');

    // Disable execution time limit for large files
    set_time_limit(0);

    // Increase memory limit if needed
    ini_set('memory_limit', '256M');

    // Output file in chunks to handle large files efficiently
    $handle = fopen($file_path, 'rb');
    if ($handle === false) {
        showError('File Read Error', 'Unable to read the requested file. Please contact support.', 'fas fa-exclamation-triangle', '#ef4444');
        exit;
    }

    // Read and output file in chunks (8KB chunks for better performance)
    $chunk_size = 8192;
    while (!feof($handle) && connection_status() == CONNECTION_NORMAL) {
        $chunk = fread($handle, $chunk_size);
        if ($chunk === false) {
            break;
        }
        echo $chunk;
        flush();
        
        // Check if client disconnected
        if (connection_aborted()) {
            break;
        }
    }
    
    fclose($handle);
    exit;
}

// Function to determine MIME type
function getMimeType($file_path) {
    $extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
    
    $mime_types = [
        // Documents
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'txt' => 'text/plain',
        'rtf' => 'application/rtf',
        'csv' => 'text/csv',
        
        // Archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        '7z' => 'application/x-7z-compressed',
        'tar' => 'application/x-tar',
        'gz' => 'application/gzip',
        
        // Images
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        
        // Videos
        'mp4' => 'video/mp4',
        'avi' => 'video/x-msvideo',
        'mov' => 'video/quicktime',
        'wmv' => 'video/x-ms-wmv',
        'flv' => 'video/x-flv',
        'webm' => 'video/webm',
        
        // Audio
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'aac' => 'audio/aac',
        'flac' => 'audio/flac',
        
        // Other
        'json' => 'application/json',
        'xml' => 'application/xml',
        'html' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript'
    ];
    
    return $mime_types[$extension] ?? 'application/octet-stream';
}

// Function to show error pages with modern styling
function showError($title, $message, $icon, $color) {
    // Clear any output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    // Start new output buffer for error page
    ob_start();
    
    try {
        include 'includes/header.php';
    } catch (Exception $e) {
        // If header fails, create a minimal HTML structure
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Error - Download</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"></head><body>';
    }
    ?>
    
    <style>
        /* Error Page Styling */
        :root {
            --primary-color: #00027A;
            --error-color: <?= $color ?>;
            --text-color: #1f2937;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-light);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        
        .error-page {
            min-height: calc(100vh - 120px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #1e40af 0%, #3730a3 50%, #1e3a8a 100%);
            position: relative;
            overflow: hidden;
        }
        
        /* Animated background elements */
        .error-page::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s linear infinite;
            pointer-events: none;
        }
        
        .error-page::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 70%, rgba(255, 107, 53, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 30%, rgba(16, 185, 129, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }
        
        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
        
        .error-container {
            position: relative;
            z-index: 10;
            max-width: 600px;
            width: 100%;
        }
        
        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem;
            text-align: center;
            box-shadow: var(--shadow-xl);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--error-color), var(--primary-color));
            border-radius: 24px 24px 0 0;
        }
        
        .error-icon-wrapper {
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem;
            background: linear-gradient(135deg, var(--error-color), <?= $color ?>dd);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-lg);
            position: relative;
        }
        
        .error-icon-wrapper::after {
            content: '';
            position: absolute;
            width: 120px;
            height: 120px;
            border: 2px solid var(--error-color);
            border-radius: 50%;
            opacity: 0.3;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.3; }
            50% { transform: scale(1.1); opacity: 0.1; }
            100% { transform: scale(1.2); opacity: 0; }
        }
        
        .error-icon {
            font-size: 2.5rem;
            color: white;
            position: relative;
            z-index: 2;
        }
        
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .error-message {
            font-size: 1.125rem;
            color: #6b7280;
            margin-bottom: 2.5rem;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 0.875rem 2rem;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        
        .action-btn:hover::before {
            left: 100%;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), #1e40af);
            color: white;
            box-shadow: var(--shadow-lg);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #1e40af, var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 25px 35px -5px rgb(0 0 0 / 0.15), 0 10px 15px -8px rgb(0 0 0 / 0.1);
        }
        
        .btn-secondary {
            background: white;
            color: var(--text-color);
            border: 2px solid #e5e7eb;
            box-shadow: var(--shadow-lg);
        }
        
        .btn-secondary:hover {
            background: #f9fafb;
            border-color: var(--primary-color);
            transform: translateY(-2px);
            color: var(--primary-color);
        }
        
        .btn-icon {
            font-size: 1rem;
            transition: transform 0.3s ease;
        }
        
        .action-btn:hover .btn-icon {
            transform: translateX(-2px);
        }
        
        .btn-secondary:hover .btn-icon {
            transform: rotate(360deg);
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .error-page {
                padding: 1rem;
                min-height: calc(100vh - 80px);
            }
            
            .error-card {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
            
            .error-title {
                font-size: 1.75rem;
            }
            
            .error-message {
                font-size: 1rem;
            }
            
            .error-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .action-btn {
                width: 100%;
                max-width: 280px;
                justify-content: center;
            }
            
            .error-icon-wrapper {
                width: 80px;
                height: 80px;
                margin-bottom: 1.5rem;
            }
            
            .error-icon {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 480px) {
            .error-card {
                padding: 1.5rem 1rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
            
            .error-icon-wrapper {
                width: 70px;
                height: 70px;
            }
            
            .error-icon {
                font-size: 1.75rem;
            }
        }
        
        /* Animation for error card */
        .error-card {
            animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* Focus styles for accessibility */
        .action-btn:focus {
            outline: 3px solid rgba(59, 130, 246, 0.5);
            outline-offset: 2px;
        }
        
        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .error-card {
                background: white;
                border: 3px solid var(--text-color);
            }
            
            .action-btn {
                border: 2px solid;
            }
        }
        
        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            .error-card,
            .action-btn,
            .error-icon-wrapper::after {
                animation: none !important;
            }
            
            .action-btn,
            .btn-icon {
                transition: none !important;
            }
        }
        
        /* Print styles */
        @media print {
            .error-page {
                background: white !important;
                color: black !important;
            }
            
            .error-card {
                box-shadow: none;
                border: 2px solid black;
            }
            
            .error-actions {
                display: none;
            }
        }
    </style>
    
    <div class="error-page">
        <div class="error-container">
            <div class="error-card">
                <div class="error-icon-wrapper">
                    <i class="<?= htmlspecialchars($icon) ?> error-icon"></i>
                </div>
                <h1 class="error-title"><?= htmlspecialchars($title) ?></h1>
                <p class="error-message"><?= htmlspecialchars($message) ?></p>
                <div class="error-actions">
                    <button onclick="history.back()" class="action-btn btn-primary">
                        <i class="fas fa-arrow-left btn-icon"></i>
                        Go Back
                    </button>
                    <a href="index.php" class="action-btn btn-secondary">
                        <i class="fas fa-home btn-icon"></i>
                        Home Page
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Add interactivity to error page
        document.addEventListener('DOMContentLoaded', function() {
            // Add click sound effect (optional)
            const buttons = document.querySelectorAll('.action-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            });
            
            // Add keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' || e.key === 'Backspace') {
                    history.back();
                } else if (e.key === 'Home') {
                    window.location.href = 'index.php';
                }
            });
            
            // Auto-focus on primary action button for accessibility
            const primaryBtn = document.querySelector('.btn-primary');
            if (primaryBtn) {
                primaryBtn.focus();
            }
            
            console.log('Error page loaded with enhanced accessibility and interactions');
        });
    </script>
    
    <?php
    try {
        include 'includes/footer.php';
    } catch (Exception $e) {
        echo '</body></html>';
    }
    
    // Output the error page and terminate
    ob_end_flush();
    exit;
}

// Log download attempts (optional - for analytics)
function logDownload($resource_id, $success = true) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO download_logs (resource_id, ip_address, user_agent, success, download_time) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([
            $resource_id,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            $success ? 1 : 0
        ]);
    } catch (Exception $e) {
        // Silently fail logging - don't break download process
        error_log("Download logging failed: " . $e->getMessage());
    }
}

// Optional: Log this download attempt
// Uncomment the line below if you have a download_logs table
// logDownload($resource_id, true);

?>