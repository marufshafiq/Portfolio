<?php
header('Content-Type: application/json');
require_once '../config/db.php';

// Check if user is logged in as admin
session_start();
if (!isset($_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get the request method
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Get single project
            $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($project);
        } else {
            // Get all projects
            $stmt = $pdo->query("SELECT * FROM projects ORDER BY id DESC");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($projects);
        }
        break;

    case 'POST':
        // Create new project
        try {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $technologies = $_POST['technologies'];
            $github_link = $_POST['github_link'];
            $live_link = $_POST['live_link'];
            
            // Handle image upload
            $image_path = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . '.' . $file_extension;
                $target_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                    $image_path = 'uploads/' . $file_name;
                }
            }

            $stmt = $pdo->prepare("INSERT INTO projects (title, description, technologies, github_link, live_link, image_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $technologies, $github_link, $live_link, $image_path]);
            
            echo json_encode(['success' => true, 'message' => 'Project created successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error creating project: ' . $e->getMessage()]);
        }
        break;

    case 'PUT':
        // Update project
        try {
            parse_str(file_get_contents("php://input"), $_PUT);
            $id = $_PUT['project_id'];
            $title = $_PUT['title'];
            $description = $_PUT['description'];
            $technologies = $_PUT['technologies'];
            $github_link = $_PUT['github_link'];
            $live_link = $_PUT['live_link'];

            $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, technologies = ?, github_link = ?, live_link = ? WHERE id = ?");
            $stmt->execute([$title, $description, $technologies, $github_link, $live_link, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Project updated successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error updating project: ' . $e->getMessage()]);
        }
        break;

    case 'DELETE':
        // Delete project
        try {
            $id = $_GET['id'];
            
            // Get the image path before deleting
            $stmt = $pdo->prepare("SELECT image_path FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Delete the image file if it exists
            if ($project && $project['image_path']) {
                $image_path = '../' . $project['image_path'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            
            // Delete the project record
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
            $stmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'Project deleted successfully']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting project: ' . $e->getMessage()]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        break;
}
?>
