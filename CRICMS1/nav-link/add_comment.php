<?php
// Handle comment submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate input (you may need to enhance this validation)
    $author = $_POST['author'] ?? '';
    $content = $_POST['content'] ?? '';

    // Assuming you're storing comments in a database
    // Replace these with your actual database connection and table name
    include '../includes/db_connection.php';

    // Prepare and execute SQL statement to insert the comment into the database
    $sql = "INSERT INTO comments (author, content) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $author, $content);

    if ($stmt->execute() === TRUE) {
        // Comment added successfully
        // You can customize the response as needed
        echo "<div class='comment'>";
        echo "<div class='comment-author'>$author</div>";
        echo "<div class='comment-content'>$content</div>";
        echo "</div>";
    } else {
        // Error occurred while adding comment
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    // Handle invalid request method
    echo "Invalid request method";
}
