<?php
// Database connection
$host = 'localhost';
$dbname = 'community_board';
$username = 'root'; // Adjust based on your database setup
$password = ''; // Adjust based on your database setup

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $content = $_POST['content'];
    $attachment = '';

    // Handle file upload
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $attachment = 'uploads/' . $_FILES['attachment']['name'];
        move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment);
    }

    // Insert post into database
    $sql = "INSERT INTO posts (title, category, content, attachment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssss', $title, $category, $content, $attachment);

    if ($stmt->execute()) {
        echo "Post created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
