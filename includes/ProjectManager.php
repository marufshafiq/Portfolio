<?php
require_once 'config/db.php';

class ProjectManager {
    private $conn;

    public function __construct($conn) {
        if (!$conn) {
            throw new Exception("Database connection is required");
        }
        $this->conn = $conn;
    }

    public function getAllProjects() {
        try {
            if (!$this->conn) {
                return [];
            }
            $stmt = $this->conn->query("SELECT * FROM projects ORDER BY id DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching projects: " . $e->getMessage());
            return [];
        }
    }

    public function addProject($title, $image, $github_link, $demo_link) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO projects (title, image, github_link, demo_link) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$title, $image, $github_link, $demo_link]);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updateProject($id, $title, $image, $github_link, $demo_link) {
        try {
            $stmt = $this->conn->prepare("UPDATE projects SET title = ?, image = ?, github_link = ?, demo_link = ? WHERE id = ?");
            return $stmt->execute([$title, $image, $github_link, $demo_link, $id]);
        } catch(PDOException $e) {
            return false;
        }
    }

    public function deleteProject($id) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM projects WHERE id = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            return false;
        }
    }
}
?>
