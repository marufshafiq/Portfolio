<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../config/db.php';

try {
    $stmt = $pdo->query("SELECT id, title, description, technologies, github_link, live_link, image_path FROM projects ORDER BY id DESC");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the projects data
    $formatted_projects = array_map(function($project) {
        // Convert technologies string to array
        $project['technologies'] = array_map('trim', explode(',', $project['technologies']));
        return $project;
    }, $projects);
    
    echo json_encode(['success' => true, 'data' => $formatted_projects]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error fetching projects']);
}
?>
