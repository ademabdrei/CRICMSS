<?php
session_start();
require_once '../includes/db_connection.php'; // Assuming you have a database connection file

// Check if the user is logged in as a moderator
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'moderator') {
    // Redirect non-moderator users to the login page or a different page
    header('Location: ../login.php');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $resident_id = isset($_POST['resident_id']) ? $_POST['resident_id'] : null;

    switch ($action) {
        case 'add':
            // Handle new resident registration
            $username = $_POST['username'];
            $full_name = $_POST['full_name'];
            $kebele_id = $_POST['kebele_id'];
            $city_id = $_POST['city_id'];
            $status = 'pending';

            $insert_query = "INSERT INTO residents (username, full_name, kebele_id, city_id, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param('ssiis', $username, $full_name, $kebele_id, $city_id, $status);
            $stmt->execute();
            $stmt->close();
            break;

        case 'edit':
            // Handle editing an existing resident registration
            $username = $_POST['username'];
            $full_name = $_POST['full_name'];
            $kebele_id = $_POST['kebele_id'];
            $city_id = $_POST['city_id'];
            $status = $_POST['status'];

            $update_query = "UPDATE residents SET username = ?, full_name = ?, kebele_id = ?, city_id = ?, status = ? WHERE resident_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('ssiiis', $username, $full_name, $kebele_id, $city_id, $status, $resident_id);
            $stmt->execute();
            $stmt->close();
            break;

        case 'delete':
            // Handle deleting a resident registration
            $delete_query = "DELETE FROM residents WHERE resident_id = ?";
            $stmt = $conn->prepare($delete_query);
            $stmt->bind_param('i', $resident_id);
            $stmt->execute();
            $stmt->close();
            break;

        case 'approve':
            // Handle approving a resident registration
            $update_query = "UPDATE residents SET status = 'approved' WHERE resident_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('i', $resident_id);
            $stmt->execute();
            $stmt->close();
            break;

        case 'disapprove':
            // Handle disapproving a resident registration
            $update_query = "UPDATE residents SET status = 'disapproved' WHERE resident_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('i', $resident_id);
            $stmt->execute();
            $stmt->close();
            break;
    }

    // Redirect back to the page after the action
    header('Location: moderator_manage_residents.php');
    exit;
}

// Fetch the list of all resident registrations
$registrations_query = "SELECT r.resident_id, r.username, r.full_name, r.status, k.kebele_name, c.city_name 
                        FROM residents r
                        JOIN kebele k ON r.kebele_id = k.kebele_id
                        JOIN city c ON r.city_id = c.city_id
                        ORDER BY r.status";
$registrations_result = $conn->query($registrations_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Resident Registrations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        .status-pill {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            border-radius: 0.25rem;
        }
        .status-pill.pending {
            background-color: #ffc107;
            color: #333;
        }
        .status-pill.approved {
            background-color: #28a745;
            color: #fff;
        }
        .status-pill.disapproved {
            background-color: #dc3545;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <h1 class="mb-4">Manage Resident Registrations</h1>

        <!-- Add new resident registration -->
        <div class="mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addResidentModal">
                Add New Resident
            </button>
        </div>

        <!-- Resident registration table -->
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Kebele</th>
                    <th>City</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $registrations_result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo $row['kebele_name']; ?></td>
                        <td><?php echo $row['city_name']; ?></td>
                        <td>
                            <span class="status-pill <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'pending') : ?>
                                <button type="button" class="btn btn-success btn-sm mr-2" onclick="editResident(<?php echo $row['resident_id']; ?>)">
                                    Approve
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="disapproveResident(<?php echo $row['resident_id']; ?>)">
                                    Disapprove
                                </button>
                            <?php elseif ($row['status'] == 'approved' || $row['status'] == 'disapproved') : ?>
                                <button type="button" class="btn btn-primary btn-sm mr-2" onclick="editResident(<?php echo $row['resident_id']; ?>)">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteResident(<?php echo $row['resident_id']; ?>)">
                                    Delete
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

<!-- Add new resident modal -->
<div class="modal fade" id="addResidentModal" tabindex="-1" aria-labelledby="addResidentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addResidentModalLabel">Add New Resident</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="kebele_id" class="form-label">Kebele</label>
                            <select class="form-select" id="kebele_id" name="kebele_id" required>
                                <option value="">Select Kebele</option>
                                <?php
                                $kebele_query = "SELECT kebele_id, kebele_name FROM kebele";
                                $kebele_result = $conn->query($kebele_query);
                                while ($kebele_row = $kebele_result->fetch_assoc()) {
                                    echo "<option value='{$kebele_row['kebele_id']}'>{$kebele_row['kebele_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="city_id" class="form-label">City</label>
                            <select class="form-select" id="city_id" name="city_id" required>
                                <option value="">Select City</option>
                                <?php
                                $city_query = "SELECT city_id, city_name FROM city";
                                $city_result = $conn->query($city_query);
                                while ($city_row = $city_result->fetch_assoc()) {
                                    echo "<option value='{$city_row['city_id']}'>{$city_row['city_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <input type="hidden" name="action" value="add">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Resident</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit resident modal -->
    <div class="modal fade" id="editResidentModal" tabindex="-1" aria-labelledby="editResidentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editResidentModalLabel">Edit Resident</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" id="edit_resident_id" name="resident_id">
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="edit_full_name" name="full_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_kebele_id" class="form-label">Kebele</label>
                            <select class="form-select" id="edit_kebele_id" name="kebele_id" required>
                                <option value="">Select Kebele</option>
                                <?php
                                $kebele_query = "SELECT kebele_id, kebele_name FROM kebele";
                                $kebele_result = $conn->query($kebele_query);
                                while ($kebele_row = $kebele_result->fetch_assoc()) {
                                    echo "<option value='{$kebele_row['kebele_id']}'>{$kebele_row['kebele_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_city_id" class="form-label">City</label>
                            <select class="form-select" id="edit_city_id" name="city_id" required>
                                <option value="">Select City</option>
                                <?php
                                $city_query = "SELECT city_id, city_name FROM city";
                                $city_result = $conn->query($city_query);
                                while ($city_row = $city_result->fetch_assoc()) {
                                    echo "<option value='{$city_row['city_id']}'>{$city_row['city_name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_status" class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="disapproved">Disapproved</option>
                            </select>
                        </div>
                        <input type="hidden" name="action" value="edit">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete confirmation modal -->
    <div class="modal fade" id="deleteResidentModal" tabindex="-1" aria-labelledby="deleteResidentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteResidentModalLabel">Delete Resident</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this resident?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="post" class="d-inline">
                        <input type="hidden" id="delete_resident_id" name="resident_id">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                </div>
                </div>
                    </div>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.min.js"></script>
<script>
    function editResident(residentId) {
        // Fetch the resident details and populate the edit modal
        fetch(`fetch_resident_details.php?resident_id=${residentId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_resident_id').value = data.resident_id;
                document.getElementById('edit_username').value = data.username;
                document.getElementById('edit_full_name').value = data.full_name;
                document.getElementById('edit_kebele_id').value = data.kebele_id;
                document.getElementById('edit_city_id').value = data.city_id;
                document.getElementById('edit_status').value = data.status;
                $('#editResidentModal').modal('show');
            })
            .catch(error => {
                console.error('Error fetching resident details:', error);
            });
    }

    function disapproveResident(residentId) {
        // Confirm the disapproval and submit the form
        if (confirm('Are you sure you want to disapprove this resident?')) {
            document.getElementById('delete_resident_id').value = residentId;
            document.querySelector('#deleteResidentModal form').submit();
        }
    }

    function deleteResident(residentId) {
        // Confirm the deletion and submit the form
        if (confirm('Are you sure you want to delete this resident?')) {
            document.getElementById('delete_resident_id').value = residentId;
            document.querySelector('#deleteResidentModal form').submit();
        }
    }
</script>
</body>
</html>



                