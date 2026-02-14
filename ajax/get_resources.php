<?php
// ===== AJAX RESOURCES LOADER =====
// File: ajax/get_resources.php
header('Content-Type: application/json');

// Include database connection
include '../config/db.php';

try {
    $class_id = $_GET['class_id'] ?? '';
    $board_id = $_GET['board_id'] ?? '';
    $subject_id = $_GET['subject_id'] ?? '';
    $type = $_GET['type'] ?? '';

    $query = "SELECT r.*, s.subject_name, c.class_name, b.board_name 
              FROM resources r 
              LEFT JOIN subjects s ON r.subject_id = s.id
              LEFT JOIN classes c ON r.class_id = c.id
              LEFT JOIN boards b ON r.board_id = b.id
              WHERE r.status = 'active'";

    $params = [];

    if ($class_id) {
        $query .= " AND r.class_id = ?";
        $params[] = $class_id;
    }

    if ($board_id) {
        $query .= " AND r.board_id = ?";
        $params[] = $board_id;
    }

    if ($subject_id) {
        $query .= " AND r.subject_id = ?";
        $params[] = $subject_id;
    }

    if ($type) {
        $query .= " AND r.type = ?";
        $params[] = $type;
    }

    $query .= " ORDER BY r.created_at DESC LIMIT 50";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sanitize output
    foreach ($resources as &$resource) {
        $resource['heading'] = htmlspecialchars($resource['heading'] ?? '');
        $resource['description'] = htmlspecialchars($resource['description'] ?? '');
        $resource['subject_name'] = htmlspecialchars($resource['subject_name'] ?? '');
        $resource['class_name'] = htmlspecialchars($resource['class_name'] ?? '');
        $resource['board_name'] = htmlspecialchars($resource['board_name'] ?? '');
        $resource['video_link'] = htmlspecialchars($resource['video_link'] ?? '');
        $resource['front_image'] = htmlspecialchars($resource['front_image'] ?? '');
    }

    echo json_encode($resources);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>