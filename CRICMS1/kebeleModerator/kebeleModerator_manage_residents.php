<?php
// Include the database connection file
require_once '../includes/db_connection.php';

// Check if the user is logged in as a kebele moderator
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'kebeleModerator') {
    // Redirect non-moderator users to the login page or a different page
    header('Location: ../login.php');
    exit;
}

// Fetch kebele moderator information
$moderator_id = $_SESSION['user_id']; // Assuming moderator_id is stored in the session
$kebele_id = $_SESSION['kebele_id']; // Assuming kebele_id is also stored in the session
$username = $_SESSION['username']; // Assuming username is stored in the session
$kebele_name = $_SESSION['kebele_name']; // Assuming kebele_name is stored in the session

// Define error message variable
$error_message = '';
$success_message = '';

// Page title
$page_title = "Manage Residents";

// Fetch residents within the moderator's kebele
$residents_query = "SELECT r.*, rg.region_name, z.zone_name, wo.woreda_name, ci.city_name, k.kebele_name 
                    FROM residents r
                    JOIN region rg ON r.region_id = rg.region_id
                    JOIN zone z ON r.zone_id = z.zone_id
                    JOIN woreda wo ON r.woreda_id = wo.woreda_id
                    JOIN city ci ON r.city_id = ci.city_id
                    JOIN kebele k ON r.kebele_id = k.kebele_id
                    WHERE r.kebele_id = ?";
$stmt = $conn->prepare($residents_query);
$stmt->bind_param("i", $kebele_id);
$stmt->execute();
$residents_result = $stmt->get_result();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if action is set
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        switch ($action) {
            case 'add_resident':
                // Input validation and handling
                $username = $_POST['username'] ?? '';
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $full_name = $_POST['full_name'] ?? '';
                $image = $_POST['image'] ?? '';

                // Retrieve region, zone, woreda, city, and kebele IDs from session
                $region_id = $_SESSION['region_id'] ?? '';
                $zone_id = $_SESSION['zone_id'] ?? '';
                $woreda_id = $_SESSION['woreda_id'] ?? '';
                $city_id = $_SESSION['city_id'] ?? '';
                $kebele_id = $_SESSION['kebele_id'] ?? '';
                $status = $_POST['status'] ?? 'pending';

                if (!empty($username) && !empty($password) && !empty($full_name) && !empty($region_id) && !empty($zone_id) && !empty($woreda_id) && !empty($city_id) && !empty($kebele_id)) {
                    // Check if the resident with the same username already exists
                    $checkUsernameQuery = "SELECT * FROM residents WHERE username = ?";
                    $checkUsernameStmt = $conn->prepare($checkUsernameQuery);
                    $checkUsernameStmt->bind_param('s', $username);
                    $checkUsernameStmt->execute();
                    $existingResident = $checkUsernameStmt->get_result()->fetch_assoc();

                    if (!$existingResident) {
                        // Add resident logic here, insert into the 'residents' table
                        $insertResidentQuery = "INSERT INTO residents (username, password, full_name, image, region_id, zone_id, woreda_id, city_id, kebele_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                        $insertResidentStmt = $conn->prepare($insertResidentQuery);
                        $insertResidentStmt->bind_param('ssssiiiiis', $username, $password, $full_name, $image, $region_id, $zone_id, $woreda_id, $city_id, $kebele_id, $status);
                        if ($insertResidentStmt->execute()) {
                            $success_message = "Resident added successfully.";
                            header('Location: kebeleModerator_manage_residents.php');
                            exit;
                        } else {
                            // Handle database insertion error
                            $error_message = "Failed to add resident. Please try again later.";
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

           // Edit Resident Action
case 'edit_resident':
    // Input validation and handling
    $resident_id = $_POST['resident_id'] ?? '';
    $username = $_POST['username'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $image = $_POST['image'] ?? '';
    $status = $_POST['status'] ?? 'pending';

    if (!empty($resident_id) && !empty($username) && !empty($full_name)) {
        // Check if a new password is provided
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            // Edit resident with password update
            $updateResidentQuery = "UPDATE residents SET username = ?, password = ?, full_name = ?, image = ?, status = ? WHERE resident_id = ?";
            $updateResidentStmt = $conn->prepare($updateResidentQuery);
            $updateResidentStmt->bind_param('sssssi', $username, $password, $full_name, $image, $status, $resident_id);
        } else {
            // Edit resident without password update
            $updateResidentQuery = "UPDATE residents SET username = ?, full_name = ?, image = ?, status = ? WHERE resident_id = ?";
            $updateResidentStmt = $conn->prepare($updateResidentQuery);
            $updateResidentStmt->bind_param('ssssi', $username, $full_name, $image, $status, $resident_id);
        }

        if ($updateResidentStmt->execute()) {
            $success_message = "Resident updated successfully.";
            // Refresh page to show updated resident list
            header('Location: kebeleModerator_manage_residents.php');
            exit;
        } else {
            // Handle database update error
            $error_message = "Failed to edit resident. Please try again later.";
        }
    } else {
        // Handle invalid input
        $error_message = "Please fill out all the required fields.";
    }
    break;


            case 'delete_resident':
                // Input validation and handling
                $resident_id = $_POST['resident_id'] ?? '';

                if (!empty($resident_id)) {
                    // Delete resident logic here, delete from the 'residents' table
                    $deleteResidentQuery = "DELETE FROM residents WHERE resident_id = ?";
                    $deleteResidentStmt = $conn->prepare($deleteResidentQuery);
                    $deleteResidentStmt->bind_param('i', $resident_id);
                    if ($deleteResidentStmt->execute()) {
                        $success_message = "Resident deleted successfully.";
                    } else {
                        // Handle database deletion error
                        $error_message = "Failed to delete resident. Please try again later.";
                    }
                } else {
                    // Handle invalid input
                    $error_message = "Invalid resident ID.";
                }
                break;

            default:
                // Invalid action
                break;
        }
    }
}

// Check if search input is set in the $_POST array
if (isset($_POST['search_input'])) {
    $search_input = '%' . $_POST['search_input'] . '%'; // Assuming the search input is sent via POST
    // Fetch residents within the moderator's kebele based on search input
    $search_query = "SELECT r.*, rg.region_name, z.zone_name, wo.woreda_name, ci.city_name, k.kebele_name 
                    FROM residents r
                    JOIN region rg ON r.region_id = rg.region_id
                    JOIN zone z ON r.zone_id = z.zone_id
                    JOIN woreda wo ON r.woreda_id = wo.woreda_id
                    JOIN city ci ON r.city_id = ci.city_id
                    JOIN kebele k ON r.kebele_id = k.kebele_id
                    WHERE r.kebele_id = ? AND r.full_name LIKE ?";
    $stmt = $conn->prepare($search_query);
    $stmt->bind_param("is", $kebele_id, $search_input);
    $stmt->execute();
    $residents_result = $stmt->get_result();
} else {
    // If search input is not set, fetch all residents
    $residents_query = "SELECT r.*, rg.region_name, z.zone_name, wo.woreda_name, ci.city_name, k.kebele_name 
                    FROM residents r
                    JOIN region rg ON r.region_id = rg.region_id
                    JOIN zone z ON r.zone_id = z.zone_id
                    JOIN woreda wo ON r.woreda_id = wo.woreda_id
                    JOIN city ci ON r.city_id = ci.city_id
                    JOIN kebele k ON r.kebele_id = k.kebele_id
                    WHERE r.kebele_id = ?";
    $stmt = $conn->prepare($residents_query);
    $stmt->bind_param("i", $kebele_id);
    $stmt->execute();
    $residents_result = $stmt->get_result();
}

include '../includes/header.php'
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container my-5">
        <h1><?php echo $page_title; ?></h1>

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

        <!-- Add Resident Modal -->
        <div class="modal fade" id="addResidentModal" tabindex="-1" aria-labelledby="addResidentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addResidentModalLabel">Add Resident</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Add resident form -->
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="action" value="add_resident">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image</label>
                                <input type="text" class="form-control" id="image" name="image">
                            </div>
                            <button type="submit" class="btn btn-primary">Add Resident</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

<!-- Edit Resident Modal -->
<div class="modal fade" id="editResidentModal" tabindex="-1" aria-labelledby="editResidentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editResidentModalLabel">Edit Resident</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Edit resident form -->
                <form id="editResidentForm" method="POST">
                    <input type="hidden" id="editResidentId" name="resident_id">
                    <div class="mb-3">
                        <label for="editResidentUsername" class="form-label">Username</label>
                        <input type="text" class="form-control" id="editResidentUsername" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="editResidentPassword" class="form-label">Password</label>
                        <input type="password" class="form-control" id="editResidentPassword" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="editResidentFullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="editResidentFullName" name="full_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editResidentImage" class="form-label">Image</label>
                        <input type="text" class="form-control" id="editResidentImage" name="image">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>





        <!-- Delete Resident Modal -->
        <div class="modal fade" id="deleteResidentModal" tabindex="-1" aria-labelledby="deleteResidentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteResidentModalLabel">Delete Resident</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Delete confirmation message -->
                        <p>Are you sure you want to delete this resident?</p>
                    </div>
                    <div class="modal-footer">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                            <input type="hidden" name="action" value="delete_resident">
                            <input type="hidden" id="deleteResidentId" name="resident_id">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Resident Button -->
        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addResidentModal">
            Add Resident
        </button>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="searchForm">
            <!-- Search input field and button -->
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search residents by full name" id="searchInput" name="search_input">
                <button class="btn btn-outline-secondary" type="submit" id="searchButton">Search</button>
            </div>
        </form>

        <!-- Resident Table -->
        <h2>Residents</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <!-- Table headings -->
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Region</th>
                    <th>Zone</th>
                    <th>Woreda</th>
                    <th>City</th>
                    <th>Kebele</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through residents and display in table rows -->
                <?php while ($row = $residents_result->fetch_assoc()) : ?>
                    <tr>
                        <!-- Table data for each resident -->
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['region_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['zone_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['woreda_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['city_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['kebele_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <!-- Buttons for editing and deleting residents -->
                            <button type="button" class="btn btn-primary editBtn" data-bs-toggle="modal" data-bs-target="#editResidentModal" data-resident-id="<?php echo $row['resident_id']; ?>" data-resident-username="<?php echo htmlspecialchars($row['username']); ?>" data-resident-fullname="<?php echo htmlspecialchars($row['full_name']); ?>" data-resident-image="<?php echo htmlspecialchars($row['image']); ?>" data-resident-status="<?php echo htmlspecialchars($row['status']); ?>">Edit</button>
                            <button type="button" class="btn btn-danger deleteBtn" data-bs-toggle="modal" data-bs-target="#deleteResidentModal" data-resident-id="<?php echo $row['resident_id']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- JavaScript and Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    // JavaScript code for handling edit and delete actions
    document.addEventListener('DOMContentLoaded', function() {
        // Edit Resident Action
        var editButtons = document.querySelectorAll('.editBtn');
        editButtons.forEach(function(editButton) {
            editButton.addEventListener('click', function() {
                // Get resident details to populate the form fields
                var residentId = editButton.dataset.residentId;
                var residentUsername = editButton.dataset.residentUsername;
                var residentFullName = editButton.dataset.residentFullname;
                var residentImage = editButton.dataset.residentImage;
                var residentStatus = editButton.dataset.residentStatus;

                // Populate the modal form fields with resident details
                document.getElementById('editResidentId').value = residentId;
                document.getElementById('editResidentUsername').value = residentUsername;
                document.getElementById('editResidentFullName').value = residentFullName;
                document.getElementById('editResidentImage').value = residentImage;
                document.getElementById('editResidentStatus').value = residentStatus;

                // Show the modal
                var editResidentModal = new bootstrap.Modal(document.getElementById('editResidentModal'));
                editResidentModal.show();
            });
        });

        // Delete Resident Action
        var deleteButtons = document.querySelectorAll('.deleteBtn');
        deleteButtons.forEach(function(deleteButton) {
            deleteButton.addEventListener('click', function() {
                // Get resident ID to be deleted
                var residentId = deleteButton.dataset.residentId;
                // Populate the hidden input with resident ID
                document.getElementById('deleteResidentId').value = residentId;
                // Show the modal
                var deleteResidentModal = new bootstrap.Modal(document.getElementById('deleteResidentModal'));
                deleteResidentModal.show();
            });
        });

        // Form submission for editing resident
        var editResidentForm = document.getElementById('editResidentForm');
        editResidentForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting normally
            var formData = new FormData(editResidentForm);
            // Add action parameter
            formData.append('action', 'edit_resident');

            // Send the edited data to the server using XMLHttpRequest
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // Handle success response
                        console.log(xhr.responseText);
                        // Close the modal or display a success message
                        var editResidentModal = bootstrap.Modal.getInstance(document.getElementById('editResidentModal'));
                        editResidentModal.hide();
                        // Reload the page or update the resident list
                        window.location.reload();
                    } else {
                        // Handle error
                        console.error(xhr.status);
                        // Display an error message to the user
                    }
                }
            };
            xhr.open('POST', '<?php echo $_SERVER['PHP_SELF']; ?>', true);
            xhr.send(formData);
        });
    });
</script>

    <script>
        // JavaScript code for handling search action
        document.getElementById('searchButton').addEventListener('click', function() {
            var searchInput = document.getElementById('searchInput').value;
            // You may add client-side validation for the search input here
            // Submit the form
            document.getElementById('searchForm').submit();
        });
    </script>
</body>

</html>