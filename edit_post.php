<?php
session_start();
$host = 'localhost';
$dbname = 'community_board';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['title'], $_POST['content'])) {
    $post_id = intval($_POST['post_id']);
    $title = $_POST['title'];
    $content = $_POST['content'];
    
    $sql = "UPDATE posts SET title = ?, content = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $title, $content, $post_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: index.php"); // Redirect back to the main page
exit;
?>
