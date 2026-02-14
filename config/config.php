<?php
// Database configuration
$host = 'localhost';
$dbname = 'learnify';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper function to get boards
function getBoards($pdo) {
    $stmt = $pdo->query("SELECT * FROM boards ORDER BY board_name");
    return $stmt->fetchAll();
}

// Helper function to get classes
function getClasses($pdo) {
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY class_name");
    return $stmt->fetchAll();
}

// Helper function to get subjects by class
function getSubjectsByClass($pdo, $classId) {
    $stmt = $pdo->prepare("SELECT * FROM subjects WHERE class_id = ? ORDER BY subject_name");
    $stmt->execute([$classId]);
    return $stmt->fetchAll();
}

// Helper function to get districts by board
function getDistrictsByBoard($pdo, $boardName) {
    $stmt = $pdo->prepare("SELECT * FROM districts WHERE board = ? ORDER BY district_name");
    $stmt->execute([$boardName]);
    return $stmt->fetchAll();
}

// Helper function to get subject details/resources
function getSubjectDetails($pdo, $classId, $subjectId, $type = null, $districtId = null) {
    $sql = "SELECT sd.*, s.subject_name, c.class_name, d.district_name 
            FROM subject_details sd 
            LEFT JOIN subjects s ON sd.subject_id = s.id
            LEFT JOIN classes c ON sd.class_id = c.id
            LEFT JOIN districts d ON sd.district_id = d.id
            WHERE sd.class_id = ? AND sd.subject_id = ?";
    
    $params = [$classId, $subjectId];
    
    if ($type) {
        $sql .= " AND sd.type = ?";
        $params[] = $type;
    }
    
    if ($districtId) {
        $sql .= " AND sd.district_id = ?";
        $params[] = $districtId;
    }
    
    $sql .= " ORDER BY sd.id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Helper function to search resources
function searchResources($pdo, $classId = null, $board = null, $subjectId = null, $type = null, $keywords = null) {
    $sql = "SELECT sd.*, s.subject_name, c.class_name, d.district_name, d.board 
            FROM subject_details sd 
            LEFT JOIN subjects s ON sd.subject_id = s.id
            LEFT JOIN classes c ON sd.class_id = c.id
            LEFT JOIN districts d ON sd.district_id = d.id
            WHERE 1=1";
    
    $params = [];
    
    if ($classId) {
        $sql .= " AND sd.class_id = ?";
        $params[] = $classId;
    }
    
    if ($board) {
        $sql .= " AND d.board = ?";
        $params[] = $board;
    }
    
    if ($subjectId) {
        $sql .= " AND sd.subject_id = ?";
        $params[] = $subjectId;
    }
    
    if ($type) {
        $sql .= " AND sd.type = ?";
        $params[] = $type;
    }
    
    if ($keywords) {
        $sql .= " AND (sd.keywords LIKE ? OR sd.heading LIKE ? OR sd.description LIKE ?)";
        $keywordParam = "%$keywords%";
        $params[] = $keywordParam;
        $params[] = $keywordParam;
        $params[] = $keywordParam;
    }
    
    $sql .= " ORDER BY sd.id DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Helper function to get resource stats
function getResourceStats($pdo) {
    $stats = [];
    
    // Total students (placeholder - you might want to add a students table)
    $stats['students'] = '10,000+';
    
    // Total video lectures
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM subject_details WHERE type = 'video'");
    $stats['videos'] = $stmt->fetch()['count'];
    
    // Total PDF books
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM subject_details WHERE type = 'books'");
    $stats['books'] = $stmt->fetch()['count'];
    
    // Total boards
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM boards");
    $stats['boards'] = $stmt->fetch()['count'];
    
    return $stats;
}
?>