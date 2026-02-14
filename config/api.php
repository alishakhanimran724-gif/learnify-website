<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_boards':
        $boards = getBoards($pdo);
        echo json_encode(['success' => true, 'data' => $boards]);
        break;
        
    case 'get_classes':
        $classes = getClasses($pdo);
        echo json_encode(['success' => true, 'data' => $classes]);
        break;
        
    case 'get_subjects':
        $classId = $_GET['class_id'] ?? null;
        if (!$classId) {
            echo json_encode(['success' => false, 'message' => 'Class ID required']);
            break;
        }
        $subjects = getSubjectsByClass($pdo, $classId);
        echo json_encode(['success' => true, 'data' => $subjects]);
        break;
        
    case 'get_districts':
        $board = $_GET['board'] ?? null;
        if (!$board) {
            echo json_encode(['success' => false, 'message' => 'Board name required']);
            break;
        }
        $districts = getDistrictsByBoard($pdo, $board);
        echo json_encode(['success' => true, 'data' => $districts]);
        break;
        
    case 'get_resources':
        $classId = $_GET['class_id'] ?? null;
        $subjectId = $_GET['subject_id'] ?? null;
        $type = $_GET['type'] ?? null;
        $districtId = $_GET['district_id'] ?? null;
        
        if (!$classId || !$subjectId) {
            echo json_encode(['success' => false, 'message' => 'Class ID and Subject ID required']);
            break;
        }
        
        $resources = getSubjectDetails($pdo, $classId, $subjectId, $type, $districtId);
        echo json_encode(['success' => true, 'data' => $resources]);
        break;
        
    case 'search':
        $classId = $_GET['class_id'] ?? null;
        $board = $_GET['board'] ?? null;
        $subjectId = $_GET['subject_id'] ?? null;
        $type = $_GET['type'] ?? null;
        $keywords = $_GET['keywords'] ?? null;
        
        $results = searchResources($pdo, $classId, $board, $subjectId, $type, $keywords);
        echo json_encode(['success' => true, 'data' => $results]);
        break;
        
    case 'get_stats':
        $stats = getResourceStats($pdo);
        echo json_encode(['success' => true, 'data' => $stats]);
        break;
        
    case 'get_board_resources':
        $board = $_GET['board'] ?? null;
        if (!$board) {
            echo json_encode(['success' => false, 'message' => 'Board name required']);
            break;
        }
        
        // Get all resources for a specific board
        $sql = "SELECT sd.*, s.subject_name, c.class_name, d.district_name 
                FROM subject_details sd 
                LEFT JOIN subjects s ON sd.subject_id = s.id
                LEFT JOIN classes c ON sd.class_id = c.id
                LEFT JOIN districts d ON sd.district_id = d.id
                WHERE d.board = ? OR sd.district_id IS NULL
                ORDER BY c.class_name, s.subject_name, sd.type";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$board]);
        $resources = $stmt->fetchAll();
        
        // Group by class and subject
        $grouped = [];
        foreach ($resources as $resource) {
            $className = $resource['class_name'];
            $subjectName = $resource['subject_name'];
            
            if (!isset($grouped[$className])) {
                $grouped[$className] = [];
            }
            if (!isset($grouped[$className][$subjectName])) {
                $grouped[$className][$subjectName] = [];
            }
            
            $grouped[$className][$subjectName][] = $resource;
        }
        
        echo json_encode(['success' => true, 'data' => $grouped]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>