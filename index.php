<?php 
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Fetch posts
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);

// Close connection at the end of the page
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Real-Time Community Bulletin Board</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome Icons -->
  <style>
   body {
  background: linear-gradient(135deg, #f4f6f9, #e0e8f9); /* Soft gradient background */
  font-family: 'Arial', sans-serif;
}

.container {
  background: #ffffff; /* White background for container */
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 30px;
}

.post-card {
  background-color: #f9f9f9; /* Light background for post cards */
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  padding: 20px;
  transition: transform 0.3s ease;
  margin-bottom: 20px; /* Space between posts */
}

.post-card:hover {
  transform: translateY(-10px);
  background-color: #ffe4b5; /* Soft highlight background color on hover */
}

.btn-primary, .btn-danger, .btn-warning {
  border-radius: 25px;
  transition: transform 0.3s ease;
  background-color: #FF6347; /* Button background color */
}

.btn-primary:hover, .btn-danger:hover, .btn-warning:hover {
  transform: scale(1.1);
  background-color: #ff4500; /* Darker button color on hover */
}

.navbar {
  background: linear-gradient(45deg, #FF7F50, #FF6347);
  color: white;
  border-radius: 10px 10px 0 0;
}

.navbar-brand {
  font-size: 24px;
  font-weight: bold;
  position: relative;
  display: inline-block;
  overflow: hidden;
}

.navbar-brand::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.2);
  z-index: -1;
  transition: transform 0.3s ease;
  transform: scaleX(0);
  transform-origin: bottom right;
}

.navbar-brand:hover::before {
  transform: scaleX(1);
  transform-origin: bottom left;
}

.navbar-nav .nav-item {
  margin-left: 15px;
}

.nav-link {
  color: white !important;
  font-size: 18px;
  font-weight: bold;
  transition: color 0.3s ease, text-shadow 0.3s ease;
}

.nav-link:hover {
  color: #FFD700 !important;
  text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
}

.logout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px 20px;
  font-size: 16px;
  background-color: #FF6347;
  color: white;
  border-radius: 25px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.logout-btn i {
  margin-right: 8px;
}

h1, h4, p {
  transition: transform 0.3s ease, color 0.3s ease, text-shadow 0.3s ease;
}

h1:hover, h4:hover, p:hover {
  color: #FF6347;
  text-shadow: 0 0 15px rgba(255, 99, 71, 0.8), 0 0 30px rgba(255, 99, 71, 0.8);
  transform: scale(1.05);
}

.post-card h5:hover {
  color: #FF6347;
  text-shadow: 0 0 15px rgba(255, 99, 71, 0.8);
  transform: scale(1.1);
}

.post-card p:hover {
  color: #FFD700;
  text-shadow: 0 0 10px rgba(255, 215, 0, 0.7);
}

.post-card img:hover {
  transform: scale(1.05);
  transition: transform 0.3s ease;
}



  </style>
</head>
<body>
  <!-- Enhanced Navigation Bar with Project Name and Logout Button -->
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Real-Time Community Bulletin Board</a>
      <div class="d-flex ms-auto">
        <a href="logout.php" class="btn logout-btn">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </div>
    </div>
  </nav>

  <div class="container mt-5 p-4">
    <h1 class="text-center mb-4">Real-Time Community Bulletin Board</h1>

    <!-- Create Post Section -->
    <div id="createPostSection">
      <h4>Create a Post</h4>
      <form action="submit_post.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <input type="text" class="form-control" name="title" placeholder="Title" required>
        </div>
        <div class="mb-3">
          <select class="form-select" name="category" required>
            <option value="Events">Events</option>
            <option value="News">News</option>
            <option value="Jobs">Jobs</option>
            <option value="Others">Others</option>
          </select>
        </div>
        <div class="mb-3">
          <textarea class="form-control" name="content" rows="3" placeholder="Write your post..." required></textarea>
        </div>
        <div class="mb-3">
          <label for="attachment" class="form-label">Attach Image/File:</label>
          <input class="form-control" type="file" name="attachment">
        </div>
        <button type="submit" class="btn btn-primary w-100">Post</button>
      </form>
    </div>

    <!-- Posts Section -->
    <div id="postsSection">
      <h4 class="text-center">Posts</h4>
      <div id="postsContainer" class="row g-4">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($post = $result->fetch_assoc()): ?>
            <div class="col-md-4 post-card">
              <h5><?php echo htmlspecialchars($post['title']); ?></h5>
              <p><strong>Category:</strong> <?php echo htmlspecialchars($post['category']); ?></p>
              <p><?php echo htmlspecialchars($post['content']); ?></p>
              <?php if (!empty($post['attachment'])): ?>
                <img src="<?php echo htmlspecialchars($post['attachment']); ?>" class="img-fluid" alt="Attachment">
              <?php endif; ?>
              
              <!-- Delete Button -->
             <!-- Delete Button -->
<form action="delete_post.php" method="POST" class="mt-3">
    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
    <button type="submit" class="btn btn-danger w-100">Delete Post</button>
</form>
<!-- Like Button -->
<form action="like_post.php" method="POST" class="mt-2">
    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
    <button type="submit" class="btn btn-warning w-100">
        <i class="fas fa-thumbs-up"></i> Like (<?php echo $post['likes']; ?>)
    </button>
</form>

<!-- Edit Button (Modal Trigger) -->
<button class="btn btn-primary w-100 mt-2" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $post['id']; ?>">
    <i class="fas fa-edit"></i> Edit
</button>

<!-- Modal for Editing Post -->
<div class="modal fade" id="editModal<?php echo $post['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $post['id']; ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel<?php echo $post['id']; ?>">Edit Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="edit_post.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="3" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="text-center">No posts available.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
