<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Modern Blog</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        /* Custom CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            padding-top: 20px;
        }

        .post {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .post:hover {
            transform: translateY(-5px);
        }

        .post h2 {
            margin-top: 0;
            color: #007bff;
        }

        .post-meta {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .post-content {
            margin-top: 10px;
        }

        .tags {
            margin-top: 10px;
        }

        .tag {
            background-color: #007bff;
            color: #fff;
            padding: 3px 6px;
            border-radius: 3px;
            margin-right: 5px;
        }

        .comment-section {
            margin-top: 30px;
        }

        .comment {
            background-color: #f0f0f0;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .comment-author {
            font-weight: bold;
        }

        .comment-content {
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Welcome to My Modern Blog</h1>
        <div class="row" id="post-container">
            <!-- Posts will be dynamically loaded here -->
        </div>
        <div id="load-more-container" class="text-center">
            <button id="load-more-btn" class="btn btn-primary">Load More</button>
        </div>
    </div>

    <!-- Modal for adding comments (hidden by default) -->
    <div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Add Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="comment-form">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="comment-author" class="form-label">Name:</label>
                            <input type="text" class="form-control" id="comment-author" name="author">
                        </div>
                        <div class="mb-3">
                            <label for="comment-content" class="form-label">Comment:</label>
                            <textarea class="form-control" id="comment-content" name="content" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            var page = 1;

            // Function to load more posts via AJAX
            function loadMorePosts() {
                $.ajax({
                    url: 'get_posts.php', // Replace 'get_posts.php' with your server-side script to retrieve posts
                    method: 'GET',
                    data: { page: page },
                    success: function (response) {
                        $('#post-container').append(response);
                        page++;
                    }
                });
            }

            // Load more posts initially
            loadMorePosts();

            // Load more posts when clicking the 'Load More' button
            $('#load-more-btn').click(function () {
                loadMorePosts();
            });

            // Submit comment form via AJAX
            $('#comment-form').submit(function (event) {
                event.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: 'add_comment.php', // Replace 'add_comment.php' with your server-side script to handle comment submission
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        // Display the newly added comment
                        $('#commentModal').modal('hide');
                        $('#comment-content').val(''); // Clear comment content
                        $('#post-comments').append(response); // Assuming 'post-comments' is the ID of the comments container for a specific post
                    }
                });
            });
        });
    </script>
</body>

</html>
