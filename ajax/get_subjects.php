<?php
// File: ajax/get_subjects.php
// FIXED VERSION - No more stress! 😊

// Clear any output that might interfere
if (ob_get_level()) {
    ob_end_clean();
}

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response

// Include database connection
$db_connected = false;
$db_paths = [
    '../config/db.php',
    './config/db.php',
    'config/db.php'
];

foreach ($db_paths as $path) {
    if (file_exists($path)) {
        try {
            include_once $path;
            if (isset($pdo)) {
                $db_connected = true;
                break;
            }
        } catch (Exception $e) {
            continue;
        }
    }
}

// Check database connection
if (!$db_connected || !isset($pdo)) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Validate input
if (!isset($_GET['class_id']) || empty($_GET['class_id'])) {
    echo json_encode(['error' => 'Class ID is required']);
    exit;
}

$class_id = filter_var($_GET['class_id'], FILTER_VALIDATE_INT);
if ($class_id === false || $class_id <= 0) {
    echo json_encode(['error' => 'Invalid class ID']);
    exit;
}

try {
    // Test database connection
    $pdo->query("SELECT 1");
    
    // FIXED: Remove the status filter since your table doesn't have this column
    // Original query was looking for 'status' column which doesn't exist in your subjects table
    $stmt = $pdo->prepare("SELECT id, subject_name FROM subjects WHERE class_id = ? ORDER BY subject_name ASC");
    $stmt->execute([$class_id]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return subjects (even if empty array)
    echo json_encode($subjects);
    
} catch (PDOException $e) {
    // Log the actual error for debugging
    error_log("Database error in get_subjects.php: " . $e->getMessage());
    
    // Return user-friendly error
    echo json_encode([
        'error' => 'Failed to load subjects',
        'debug' => 'Check browser console and server logs for details'
    ]);
} catch (Exception $e) {
    error_log("General error in get_subjects.php: " . $e->getMessage());
    echo json_encode(['error' => 'An unexpected error occurred']);
}
?>