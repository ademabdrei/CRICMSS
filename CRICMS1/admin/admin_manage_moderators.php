<?php
session_start();

// Redirect to login page if user is not logged in as admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Include the database connection file
require_once '../includes/db_connection.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if action is set
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'add_moderator':
                $username = $_POST['username'] ?? '';
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $city_id = $_POST['city_id'] ?? '';
                if (!empty($username) && !empty($password) && !empty($city_id)) {
                    // Add moderator logic here, insert into the 'moderator' table
                    $insertModeratorQuery = "INSERT INTO moderator (username, password, city_id) VALUES (?, ?, ?)";
                    $insertModeratorStmt = $conn->prepare($insertModeratorQuery);
                    $insertModeratorStmt->bind_param('ssi', $username, $password, $city_id);
                    $insertModeratorStmt->execute();

                    header('Location: admin_manage_moderators.php');
                    exit;
                }
                break;

            case 'edit_moderator':
                $username = $_POST['username'] ?? '';
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $city_id = $_POST['city_id'] ?? '';
                $moderator_id = $_POST['moderator_id'] ?? '';

                if (!empty($username) && !empty($password) && !empty($city_id) && !empty($moderator_id)) {
                    // Edit moderator logic here, update the 'moderator' table
                    $updateModeratorQuery = "UPDATE moderator SET username = ?, password = ?, city_id = ? WHERE moderator_id = ?";
                    $updateModeratorStmt = $conn->prepare($updateModeratorQuery);
                    $updateModeratorStmt->bind_param('ssii', $username, $password, $city_id, $moderator_id);
                    $updateModeratorStmt->execute();

                    header('Location: admin_manage_moderators.php');
                    exit;
                }
                break;

            case 'delete_moderator':
                $moderator_id = $_POST['moderator_id'] ?? '';

                if (!empty($moderator_id)) {
                    // Delete moderator logic here, delete from the 'moderator' table
                    $deleteModeratorQuery = "DELETE FROM moderator WHERE moderator_id = ?";
                    $deleteModeratorStmt = $conn->prepare($deleteModeratorQuery);
                    $deleteModeratorStmt->bind_param('i', $moderator_id);
                    $deleteModeratorStmt->execute();

                    header('Location: admin_manage_moderators.php');
                    exit;
                }
                break;

            default:
                // Invalid action
                break;
        }
    }
}

// Fetch the list of moderators and their assigned cities
$getModeratorsQuery = "
    SELECT m.moderator_id, m.username, c.city_name
    FROM moderator m
    LEFT JOIN city c ON m.city_id = c.city_id
    ORDER BY m.username
";
$getModeratorsStmt = $conn->prepare($getModeratorsQuery);
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
    <title>Admin - Manage Moderators</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container my-5">
        <h1>Manage Moderators</h1>

<!-- Add Moderator Modal -->
<div class="modal fade" id="addModeratorModal" tabindex="-1" aria-labelledby="addModeratorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModeratorModalLabel">Add Moderator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <input type="hidden" name="action" value="add_moderator">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="city_id" class="form-label">City</label>
                        <select class="form-select" id="city_id" name="city_id" required>
                            <option value="" disabled selected>Select City</option>
                            <?php
                            // Fetch cities from the database
                            $getCitiesQuery = "SELECT city_id, city_name FROM city";
                            $getCitiesStmt = $conn->prepare($getCitiesQuery);
                            $getCitiesStmt->execute();
                            $citiesResult = $getCitiesStmt->get_result();

                            // Loop through cities and display as options
                            while ($city = $citiesResult->fetch_assoc()) {
                                echo "<option value='" . $city['city_id'] . "'>" . $city['city_name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Moderator</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Moderator Modal -->
<div class="modal fade" id="editModeratorModal" tabindex="-1" aria-labelledby="editModeratorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModeratorModalLabel">Edit Moderator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <input type="hidden" name="action" value="edit_moderator">
                    <input type="hidden" name="moderator_id" id="edit_moderator_id">
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="edit_password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_city_id" class="form-label">City</label>
                        <select class="form-select" id="edit_city_id" name="city_id" required>
                            <option value="" disabled>Select City</option>
                            <?php
                            // Fetch cities from the database and populate the options
                            $getCitiesQuery = "SELECT city_id, city_name FROM city";
                            $getCitiesStmt = $conn->prepare($getCitiesQuery);
                            $getCitiesStmt->execute();
                            $citiesResult = $getCitiesStmt->get_result();
                            while ($city = $citiesResult->fetch_assoc()) {
                                echo "<option value='" . $city['city_id'] . "'>" . $city['city_name'] . "</option>";
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


        <!-- Delete Moderator Confirmation Modal -->
        <div class="modal fade" id="deleteModeratorModal" tabindex="-1" aria-labelledby="deleteModeratorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModeratorModalLabel">Delete Moderator</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this moderator?</p>
                    </div>
                    <div class="modal-footer">
                        <form id="deleteModeratorForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="action" value="delete_moderator">
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
            Add Moderator
        </button>

        <h2>Moderators</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>City</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $moderatorsResult->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['city_name']); ?></td>
                        <td>
                            <button type="button" class="btn btn-primary editBtn" data-bs-toggle="modal" data-bs-target="#editModeratorModal" data-id="<?php echo $row['moderator_id']; ?>" data-username="<?php echo htmlspecialchars($row['username']); ?>" data-city="<?php echo htmlspecialchars($row['city_name']); ?>">Edit</button>
                            <button type="button" class="btn btn-danger deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteModeratorModal" data-id="<?php echo $row['moderator_id']; ?>">Delete</button>
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
            // Edit Moderator Modal
            var editModals = document.querySelectorAll('.editBtn');
            editModals.forEach(function(editModal) {
                editModal.addEventListener('click', function() {
                    var moderatorId = this.getAttribute('data-id');
                    var username = this.getAttribute('data-username');
                    var city = this.getAttribute('data-city');

                    // Set values in the edit moderator modal
                    document.getElementById('edit_moderator_id').value = moderatorId;
                    document.getElementById('edit_username').value = username;
                    document.getElementById('edit_city_id').value = city;
                });
            });

            // Delete Moderator Modal
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
