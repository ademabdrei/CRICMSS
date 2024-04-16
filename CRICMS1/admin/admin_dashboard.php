<?php
// Start the session
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    // Redirect non-admin users to the login page or a different page
    header('Location: ../login.php');
    exit;
}

// Connect to the database
require_once '../includes/db_connection.php';
include '../includes/header.php';

// Fetch data for the admin dashboard
$total_residents_query = "SELECT COUNT(*) AS total_from_residents FROM residents";
$total_residents_result = $conn->query($total_residents_query);
$total_residents = $total_residents_result->fetch_assoc()['total_from_residents'];

$total_approved_residents_query = "SELECT COUNT(*) AS total_from_residents FROM residents WHERE status = 'approved'";
$total_approved_residents_result = $conn->query($total_approved_residents_query);
$total_approved_residents = $total_approved_residents_result->fetch_assoc()['total_from_residents'];

$total_pending_residents_query = "SELECT COUNT(*) AS total_from_residents FROM residents WHERE status = 'pending'";
$total_pending_residents_result = $conn->query($total_pending_residents_query);
$total_pending_residents = $total_pending_residents_result->fetch_assoc()['total_from_residents'];

$total_disapproved_residents_query = "SELECT COUNT(*) AS total_from_residents FROM residents WHERE status = 'disapproved'";
$total_disapproved_residents_result = $conn->query($total_disapproved_residents_query);
$total_disapproved_residents = $total_disapproved_residents_result->fetch_assoc()['total_from_residents'];

$total_moderators_query = "SELECT COUNT(*) AS total_from_moderator FROM moderator";
$total_moderators_result = $conn->query($total_moderators_query);
$total_moderators = $total_moderators_result->fetch_assoc()['total_from_moderator'];

$total_kebele_moderators_query = "SELECT COUNT(*) AS total_from_kebeleModerator FROM kebeleModerator";
$total_kebele_moderators_result = $conn->query($total_kebele_moderators_query);
$total_kebele_moderators = $total_kebele_moderators_result->fetch_assoc()['total_from_kebeleModerator'];

$total_events_query = "SELECT COUNT(*) AS total_from_events FROM events";
$total_events_result = $conn->query($total_events_query);
$total_events = $total_events_result->fetch_assoc()['total_from_events'];

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - City Resident ID Card Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Residents</h5>
                        <p class="card-text display-4"><?php echo $total_residents; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Approved Residents</h5>
                        <p class="card-text display-4"><?php echo $total_approved_residents; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Pending Residents</h5>
                        <p class="card-text display-4"><?php echo $total_pending_residents; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h5 class="card-title">Disapproved Residents</h5>
                        <p class="card-text display-4"><?php echo $total_disapproved_residents; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Total Moderators</h5>
                        <p class="card-text display-4"><?php echo $total_moderators; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-secondary">
                    <div class="card-body">
                        <h5 class="card-title">Total Kebele Moderators</h5>
                        <p class="card-text display-4"><?php echo $total_kebele_moderators; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-white bg-dark">
                    <div class="card-body">
                        <h5 class="card-title">Total Events</h5>
                        <p class="card-text display-4"><?php echo $total_events; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Admin Actions</h5>
                        <div class="d-flex justify-content-around">
                            <a href="admin_register_resident.php" class="btn btn-primary">Register Resident</a>
                            <a href="admin_manage_users.php" class="btn btn-success">Manage Users</a>
                            <a href="admin_manage_components.php" class="btn btn-info">Manage City Components</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php include '../includes/footer.php'; ?>
</body>
</html>