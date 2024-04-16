<?php
// Include your database connection file
include '../includes/db_connection.php';

// Pagination settings
$postsPerPage = 5; // Number of posts per page

// Calculate pagination parameters
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $postsPerPage;

// Query to retrieve posts from the database
$sql = "SELECT * FROM posts ORDER BY id DESC LIMIT $postsPerPage OFFSET $offset";
$result = mysqli_query($conn, $sql);

// Check if there are posts
if (mysqli_num_rows($result) > 0) {
    // Loop through each post and generate HTML markup
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<div class='post'>";
        echo "<h2>{$row['title']}</h2>";
        echo "<div class='post-meta'>Posted on {$row['date']} by {$row['author']}</div>";
        echo "<div class='post-content'>{$row['content']}</div>";
        echo "<div class='tags'>Tags: ";
        // Assuming tags are stored as comma-separated values in the database
        $tags = explode(',', $row['tags']);
        foreach ($tags as $tag) {
            echo "<span class='tag'>$tag</span>";
        }
        echo "</div>";
        echo "</div>";
    }

    // Free result set
    mysqli_free_result($result);
} else {
    // No posts found
    echo "<p>No posts found.</p>";
}

// Close database connection
mysqli_close($conn);
?>
