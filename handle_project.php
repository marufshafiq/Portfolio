<?php
require_once 'config/db.php';
require_once 'includes/ProjectManager.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectManager = new ProjectManager($conn);
    
    // Handle file upload
    $target_dir = "assets/project-images/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $image = '';
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $target_file;
        }
    }

    $title = $_POST['title'] ?? '';
    $github_link = $_POST['github_link'] ?? '';
    $demo_link = $_POST['demo_link'] ?? '';

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $projectManager->addProject($title, $image, $github_link, $demo_link);
                break;
            case 'update':
                if (isset($_POST['id'])) {
                    $projectManager->updateProject($_POST['id'], $title, $image, $github_link, $demo_link);
                }
                break;
            case 'delete':
                if (isset($_POST['id'])) {
                    $projectManager->deleteProject($_POST['id']);
                }
                break;
        }
    }

    header('Location: index.php#projects');
    exit;
}
?>
