<?php
// Database connection
$host = 'localhost';
$dbname = 'community_board';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a post_id was provided
if (isset($_POST['post_id']) && !empty($_POST['post_id'])) {
    $post_id = (int)$_POST['post_id']; // Get the post ID from the form

    // Prepare and execute the delete query
    $sql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
        // Redirect back to the posts page after successful deletion
        header("Location: index.php"); // Change index.php to your page name if needed
        exit;
    } else {
        echo "Error deleting post: " . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
