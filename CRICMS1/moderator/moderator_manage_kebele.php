<?php
// Include the database connection file
require_once '../includes/db_connection.php';

// Check if the user is logged in as a moderator
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'moderator') {
    // Redirect non-moderator users to the login page or a different page
    header('Location: ../login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if action is set
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'add_kebele':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Check if the kebele name is provided
                    if (isset($_POST['kebele_name']) && !empty($_POST['kebele_name'])) {
                        $kebele_name = htmlspecialchars(trim($_POST['kebele_name'])); // Sanitize and trim input
                        
                        // Check if the kebele name is unique
                        $checkKebeleQuery = "SELECT COUNT(*) AS count FROM kebele WHERE kebele_name = ?";
                        $checkKebeleStmt = $conn->prepare($checkKebeleQuery);
                        $checkKebeleStmt->bind_param('s', $kebele_name);
                        $checkKebeleStmt->execute();
                        $checkKebeleResult = $checkKebeleStmt->get_result();
                        $row = $checkKebeleResult->fetch_assoc();
                        if ($row['count'] > 0) {
                            // Kebele name already exists, display error message
                            $error_message = "A kebele with the same name already exists.";
                        } else {
                            // Insert the new kebele into the database
                            $insertKebeleQuery = "INSERT INTO kebele (kebele_name, city_id) VALUES (?, ?)";
                            $insertKebeleStmt = $conn->prepare($insertKebeleQuery);
                            $insertKebeleStmt->bind_param('si', $kebele_name, $_SESSION['city_id']); // Fetching city_id from session
                            if ($insertKebeleStmt->execute()) {
                                // Redirect back to the page with success message
                                header('Location: moderator_manage_kebele.php?success=add');
                                exit;
                            } else {
                                // Handle database insertion error
                                $error_message = "Failed to add kebele. Please try again later.";
                            }
                        }
                    } else {
                        // Handle case where kebele name is not provided
                        $error_message = "Please provide a kebele name.";
                    }
                }
                break;

            case 'edit_kebele':
                // Handle editing kebele
                if (isset($_POST['kebele_id'], $_POST['kebele_name']) && !empty($_POST['kebele_id']) && !empty($_POST['kebele_name'])) {
                    $kebele_id = $_POST['kebele_id'];
                    $kebele_name = htmlspecialchars(trim($_POST['kebele_name'])); // Sanitize and trim input

                    // Check if the new kebele name is unique
                    $checkKebeleQuery = "SELECT COUNT(*) AS count FROM kebele WHERE kebele_name = ? AND kebele_id != ?";
                    $checkKebeleStmt = $conn->prepare($checkKebeleQuery);
                    $checkKebeleStmt->bind_param('si', $kebele_name, $kebele_id);
                    $checkKebeleStmt->execute();
                    $checkKebeleResult = $checkKebeleStmt->get_result();
                    $row = $checkKebeleResult->fetch_assoc();
                    if ($row['count'] > 0) {
                        // Kebele name already exists, display error message
                        $error_message = "A kebele with the same name already exists.";
                    } else {
                        // Update the kebele in the database
                        $updateKebeleQuery = "UPDATE kebele SET kebele_name = ? WHERE kebele_id = ?";
                        $updateKebeleStmt = $conn->prepare($updateKebeleQuery);
                        $updateKebeleStmt->bind_param('si', $kebele_name, $kebele_id);
                        if ($updateKebeleStmt->execute()) {
                            // Redirect back to the page with success message
                            header('Location: moderator_manage_kebele.php?success=edit');
                            exit;
                        } else {
                            // Handle database update error
                            $error_message = "Failed to edit kebele. Please try again later.";
                        }
                    }
                } else {
                    // Handle case where kebele ID or name is not provided
                    $error_message = "Invalid kebele ID or name.";
                }
                break;

            case 'delete_kebele':
                // Handle deleting kebele
                if (isset($_POST['kebele_id']) && !empty($_POST['kebele_id'])) {
                    $kebele_id = $_POST['kebele_id'];

                    // Delete the kebele from the database
                    $deleteKebeleQuery = "DELETE FROM kebele WHERE kebele_id = ?";
                    $deleteKebeleStmt = $conn->prepare($deleteKebeleQuery);
                    $deleteKebeleStmt->bind_param('i', $kebele_id);
                    if ($deleteKebeleStmt->execute()) {
                        // Redirect back to the page with success message
                        header('Location: moderator_manage_kebele.php?success=delete');
                        exit;
                    } else {
                        // Handle database deletion error
                        $error_message = "Failed to delete kebele. Please try again later.";
                    }
                } else {
                    // Handle case where kebele ID is not provided
                    $error_message = "Invalid kebele ID.";
                }
                break;

            default:
                // Invalid action
                break;
        }
    }
}

// Fetch kebeles within moderator's assigned city from the database
$getKebelesQuery = "
    SELECT kebele_id, kebele_name
    FROM kebele
    WHERE city_id = ?
";
$getKebelesStmt = $conn->prepare($getKebelesQuery);
$getKebelesStmt->bind_param('i', $_SESSION['city_id']); // Fetching city_id from session
$getKebelesStmt->execute();
$kebelesResult = $getKebelesStmt->get_result();

// Include the header file
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator - Manage Kebeles</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container my-5">
        <h1>Manage Kebeles</h1>
        <!-- Error Message -->
        <?php if (isset($error_message)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if (isset($_GET['success'])) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo ucfirst($_GET['success']); ?> operation was successful.
            </div>
        <?php endif; ?>

        <!-- Add Kebele Modal -->
        <div class="modal fade" id="addKebeleModal" tabindex="-1" aria-labelledby="addKebeleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addKebeleModalLabel">Add Kebele</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="action" value="add_kebele">
                            <div class="mb-3">
                                <label for="kebele_name" class="form-label">Kebele Name</label>
                                <input type="text" class="form-control" id="kebele_name" name="kebele_name" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Kebele</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Kebele Modal -->
        <div class="modal fade" id="editKebeleModal" tabindex="-1" aria-labelledby="editKebeleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editKebeleModalLabel">Edit Kebele</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="action" value="edit_kebele">
                            <input type="hidden" id="edit_kebele_id" name="kebele_id">
                            <div class="mb-3">
                                <label for="edit_kebele_name" class="form-label">Kebele Name</label>
                                <input type="text" class="form-control" id="edit_kebele_name" name="kebele_name">
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Kebele Modal -->
        <div class="modal fade" id="deleteKebeleModal" tabindex="-1" aria-labelledby="deleteKebeleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteKebeleModalLabel">Delete Kebele</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="action" value="delete_kebele">
                            <input type="hidden" id="delete_kebele_id" name="kebele_id">
                            <p>Are you sure you want to delete this kebele?</p>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addKebeleModal">
            Add Kebele
        </button>

        <!-- Kebele List -->
        <h2>Kebele List</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $kebelesResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['kebele_name']); ?></td>
                        <td>
                            <button type="button" class="btn btn-primary editKebeleBtn" data-bs-toggle="modal" data-bs-target="#editKebeleModal" data-id="<?php echo $row['kebele_id']; ?>" data-name="<?php echo htmlspecialchars($row['kebele_name']); ?>">Edit</button>
                            <button type="button" class="btn btn-danger deleteKebeleBtn" data-bs-toggle="modal" data-bs-target="#deleteKebeleModal" data-id="<?php echo $row['kebele_id']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
   <!-- JavaScript code for handling edit and delete modals -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Kebele Modal
        var editKebeleModal = new bootstrap.Modal(document.getElementById('editKebeleModal'));

        var editModals = document.querySelectorAll('.editKebeleBtn');
        editModals.forEach(function(editModal) {
            editModal.addEventListener('click', function() {
                var kebeleId = this.getAttribute('data-id');
                var kebeleName = this.getAttribute('data-name');

                // Set values in the edit kebele modal
                document.getElementById('edit_kebele_id').value = kebeleId;
                document.getElementById('edit_kebele_name').value = kebeleName;

                // Show the edit kebele modal
                editKebeleModal.show();
            });
        });

        // Delete Kebele Modal
        var deleteKebeleModal = new bootstrap.Modal(document.getElementById('deleteKebeleModal'));

        var deleteModals = document.querySelectorAll('.deleteKebeleBtn');
        deleteModals.forEach(function(deleteModal) {
            deleteModal.addEventListener('click', function() {
                var kebeleId = this.getAttribute('data-id');

                // Set value in the delete kebele modal form
                document.getElementById('delete_kebele_id').value = kebeleId;

                // Show the delete kebele modal
                deleteKebeleModal.show();
            });
        });
    });
</script>
</body>

</html>
