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

// Define error message variable
$error_message = '';
$success_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if action is set
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'add_kebele_moderator':
                // Input validation
                $username = $_POST['username'] ?? '';
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $kebele_id = $_POST['kebele_id'] ?? '';

                if (!empty($username) && !empty($password) && !empty($kebele_id)) {
                    // Check if the kebele moderator with the same username already exists
                    $checkUsernameQuery = "SELECT * FROM kebeleModerator WHERE username = ?";
                    $checkUsernameStmt = $conn->prepare($checkUsernameQuery);
                    $checkUsernameStmt->bind_param('s', $username);
                    $checkUsernameStmt->execute();
                    $existingModerator = $checkUsernameStmt->get_result()->fetch_assoc();

                    if (!$existingModerator) {
                        // Add kebele moderator logic here, insert into the 'kebeleModerator' table
                        $insertModeratorQuery = "INSERT INTO kebeleModerator (username, password, moderator_id, kebele_id) VALUES (?, ?, ?, ?)";
                        $insertModeratorStmt = $conn->prepare($insertModeratorQuery);
                        $insertModeratorStmt->bind_param('ssii', $username, $password, $_SESSION['user_id'], $kebele_id);
                        if ($insertModeratorStmt->execute()) {
                            $success_message = "Kebele moderator added successfully.";
                        } else {
                            // Handle database insertion error
                            $error_message = "Failed to add kebele moderator. Please try again later.";
                        }
                    } else {
                        // Handle existing username error
                        $error_message = "Username already exists. Please choose a different username.";
                    }
                } else {
                    // Handle invalid input
                    $error_message = "Please fill out all the fields.";
                }
                break;

            case 'edit_kebele_moderator':
                // Input validation
                $username = $_POST['username'] ?? '';
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $kebele_id = $_POST['kebele_id'] ?? '';
                $moderator_id = $_POST['moderator_id'] ?? '';

                if (!empty($username) && !empty($password) && !empty($kebele_id) && !empty($moderator_id)) {
                    // Edit kebele moderator logic here, update the 'kebeleModerator' table
                    $updateModeratorQuery = "UPDATE kebeleModerator SET username = ?, password = ?, kebele_id = ? WHERE kebeleModerator_id = ?";
                    $updateModeratorStmt = $conn->prepare($updateModeratorQuery);
                    $updateModeratorStmt->bind_param('ssii', $username, $password, $kebele_id, $moderator_id);
                    if ($updateModeratorStmt->execute()) {
                        $success_message = "Kebele moderator updated successfully.";
                    } else {
                        // Handle database update error
                        $error_message = "Failed to edit kebele moderator. Please try again later.";
                    }
                } else {
                    // Handle invalid input
                    $error_message = "Please fill out all the fields.";
                }
                break;

            case 'delete_kebele_moderator':
                // Input validation
                $moderator_id = $_POST['moderator_id'] ?? '';

                if (!empty($moderator_id)) {
                    // Delete kebele moderator logic here, delete from the 'kebeleModerator' table
                    $deleteModeratorQuery = "DELETE FROM kebeleModerator WHERE kebeleModerator_id = ?";
                    $deleteModeratorStmt = $conn->prepare($deleteModeratorQuery);
                    $deleteModeratorStmt->bind_param('i', $moderator_id);
                    if ($deleteModeratorStmt->execute()) {
                        $success_message = "Kebele moderator deleted successfully.";
                    } else {
                        // Handle database deletion error
                        $error_message = "Failed to delete kebele moderator. Please try again later.";
                    }
                } else {
                    // Handle invalid input
                    $error_message = "Invalid kebele moderator ID.";
                }
                break;

            default:
                // Invalid action
                break;
        }
    }
}

// Fetch the list of kebele moderators and their assigned kebeles for the current moderator's city
$getModeratorsQuery = "
    SELECT km.kebeleModerator_id, km.username, k.kebele_name, c.city_name
    FROM kebeleModerator km
    LEFT JOIN kebele k ON km.kebele_id = k.kebele_id
    LEFT JOIN city c ON k.city_id = c.city_id
    WHERE km.moderator_id = ?
    ORDER BY km.username
";
$getModeratorsStmt = $conn->prepare($getModeratorsQuery);
$getModeratorsStmt->bind_param('i', $_SESSION['user_id']);
$getModeratorsStmt->execute();
$moderatorsResult = $getModeratorsStmt->get_result();

// Include the header file
include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Kebele Moderators</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container my-5">
        <h1>Manage Kebele Moderators</h1>

        <!-- Error Message -->
        <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <!-- Add Kebele Moderator Modal -->
        <div class="modal fade" id="addModeratorModal" tabindex="-1" aria-labelledby="addModeratorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModeratorModalLabel">Add Kebele Moderator</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="action" value="add_kebele_moderator">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="kebele_id" class="form-label">Kebele</label>
                                <select class="form-select" id="kebele_id" name="kebele_id" required>
                                    <option value="" disabled selected>Select Kebele</option>
                                    <?php
                                    // Fetch kebeles within moderator's assigned city from the database
                                    $getKebelesQuery = "
                                SELECT k.kebele_id, k.kebele_name
                                FROM kebele k
                                INNER JOIN city c ON k.city_id = c.city_id
                                INNER JOIN moderator m ON c.city_id = m.city_id
                                WHERE m.moderator_id = ?
                            ";
                                    $getKebelesStmt = $conn->prepare($getKebelesQuery);
                                    $getKebelesStmt->bind_param('i', $_SESSION['user_id']);
                                    $getKebelesStmt->execute();
                                    $kebelesResult = $getKebelesStmt->get_result();

                                    // Loop through kebeles and display as options
                                    while ($kebele = $kebelesResult->fetch_assoc()) {
                                        echo "<option value='" . $kebele['kebele_id'] . "'>" . $kebele['kebele_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Kebele Moderator</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Edit Kebele Moderator Modal -->
        <div class="modal fade" id="editModeratorModal" tabindex="-1" aria-labelledby="editModeratorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModeratorModalLabel">Edit Kebele Moderator</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="action" value="edit_kebele_moderator">
                            <input type="hidden" name="moderator_id" id="edit_kebele_moderator_id">
                            <div class="mb-3">
                                <label for="edit_username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="edit_username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="edit_password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_kebele_id" class="form-label">Kebele</label>

                                <select class="form-select" id="kebele_id" name="kebele_id" required>
                                    <option value="" disabled selected>Select Kebele</option>
                                    <?php
                                    // Fetch kebeles within moderator's assigned city from the database
                                    $getKebelesQuery = "
                                SELECT k.kebele_id, k.kebele_name
                                FROM kebele k
                                INNER JOIN city c ON k.city_id = c.city_id
                                INNER JOIN moderator m ON c.city_id = m.city_id
                                WHERE m.moderator_id = ?
                            ";
                                    $getKebelesStmt = $conn->prepare($getKebelesQuery);
                                    $getKebelesStmt->bind_param('i', $_SESSION['user_id']);
                                    $getKebelesStmt->execute();
                                    $kebelesResult = $getKebelesStmt->get_result();

                                    // Loop through kebeles and display as options
                                    while ($kebele = $kebelesResult->fetch_assoc()) {
                                        echo "<option value='" . $kebele['kebele_id'] . "'>" . $kebele['kebele_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="deleteModeratorModal" tabindex="-1" aria-labelledby="deleteModeratorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModeratorModalLabel">Delete Kebele Moderator</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this kebele moderator?</p>
                    </div>
                    <div class="modal-footer">
                        <form id="deleteModeratorForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="action" value="delete_kebele_moderator">
                            <input type="hidden" id="deleteModeratorId" name="moderator_id">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Button trigger modals -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModeratorModal">
            Add Kebele Moderator
        </button>

        <h2>Kebele Moderators</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Kebele</th>
                    <th>City</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $moderatorsResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['kebele_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['city_name']); ?></td>
                        <td>
                            <button type="button" class="btn btn-primary editBtn" data-bs-toggle="modal" data-bs-target="#editModeratorModal" data-id="<?php echo $row['kebeleModerator_id']; ?>" data-username="<?php echo htmlspecialchars($row['username']); ?>" data-kebele="<?php echo htmlspecialchars($row['kebele_name']); ?>" data-city="<?php echo htmlspecialchars($row['city_name']); ?>">Edit</button>
                            <button type="button" class="btn btn-danger deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteModeratorModal" data-id="<?php echo $row['kebeleModerator_id']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript code for handling edit and delete modals
        document.addEventListener('DOMContentLoaded', function() {
            // Edit Kebele Moderator Modal
            var editModals = document.querySelectorAll('.editBtn');
            editModals.forEach(function(editModal) {
                editModal.addEventListener('click', function() {
                    var moderatorId = this.getAttribute('data-id');
                    var username = this.getAttribute('data-username');
                    var kebele = this.getAttribute('data-kebele');
                    var city = this.getAttribute('data-city');

                    // Set values in the edit moderator modal
                    document.getElementById('edit_kebele_moderator_id').value = moderatorId;
                    document.getElementById('edit_username').value = username;
                    document.getElementById('edit_kebele_id').value = kebele;
                });
            });

            // Delete Kebele Moderator Modal
            var deleteModals = document.querySelectorAll('.deleteBtn');
            deleteModals.forEach(function(deleteModal) {
                deleteModal.addEventListener('click', function() {
                    var moderatorId = this.getAttribute('data-id');

                    // Set moderator id in the delete moderator modal form
                    document.getElementById('deleteModeratorId').value = moderatorId;
                });
            });
        });
    </script>
</body>

</html>