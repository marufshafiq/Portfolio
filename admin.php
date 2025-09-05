<?php
session_start();

// Check if user is logged in
if(!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Projects</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .project-form {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .projects-list {
            margin-top: 30px;
        }
        .project-item {
            background: #fff;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .project-actions {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php
    require_once 'config/db.php';
    require_once 'includes/ProjectManager.php';
    
    $projectManager = new ProjectManager($conn);
    $projects = $projectManager->getAllProjects();
    ?>

    <div class="admin-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Manage Projects</h1>
            <div>
                <span style="margin-right: 10px;">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-color-2" style="text-decoration: none;">Logout</a>
            </div>
        </div>
        
        <!-- Add Project Form -->
        <div class="project-form">
            <h2>Add New Project</h2>
            <form action="handle_project.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                
                <div class="form-group">
                    <label for="title">Project Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="image">Project Image</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>
                
                <div class="form-group">
                    <label for="github_link">GitHub Link</label>
                    <input type="url" id="github_link" name="github_link" required>
                </div>
                
                <div class="form-group">
                    <label for="demo_link">Demo Link</label>
                    <input type="url" id="demo_link" name="demo_link" required>
                </div>
                
                <button type="submit" class="btn btn-color-1">Add Project</button>
            </form>
        </div>
        
        <!-- Projects List -->
        <div class="projects-list">
            <h2>Current Projects</h2>
            <?php foreach ($projects as $project): ?>
                <div class="project-item">
                    <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                    <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" style="max-width: 200px;">
                    
                    <form action="handle_project.php" method="POST" style="display: inline-block;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $project['id']; ?>">
                        <button type="submit" class="btn btn-color-2" onclick="return confirm('Are you sure you want to delete this project?')">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
