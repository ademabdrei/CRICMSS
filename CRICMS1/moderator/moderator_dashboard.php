<?php
session_start();
include '../includes/header.php';

require_once '../includes/db_connection.php'; // Assuming you have a database connection file

// Check if the user is logged in as a moderator
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'moderator') {
    // Redirect non-moderator users to the login page or a different page
    header('Location: ../login.php');
    exit;
}

// Fetch the list of pending resident registrations
$pending_registrations_query = "SELECT r.resident_id, r.username, r.full_name, r.status, k.kebele_name, c.city_name 
                                FROM residents r
                                JOIN kebele k ON r.kebele_id = k.kebele_id
                                JOIN city c ON r.city_id = c.city_id
                                WHERE r.status = 'pending'";
$pending_registrations_result = $conn->query($pending_registrations_query);

// Fetch the list of approved resident registrations
$approved_registrations_query = "SELECT r.resident_id, r.username, r.full_name, r.status, k.kebele_name, c.city_name
                                 FROM residents r
                                 JOIN kebele k ON r.kebele_id = k.kebele_id
                                 JOIN city c ON r.city_id = c.city_id
                                 WHERE r.status = 'approved'";
$approved_registrations_result = $conn->query($approved_registrations_query);

// Fetch the list of disapproved resident registrations
$disapproved_registrations_query = "SELECT r.resident_id, r.username, r.full_name, r.status, k.kebele_name, c.city_name
                                    FROM residents r
                                    JOIN kebele k ON r.kebele_id = k.kebele_id
                                    JOIN city c ON r.city_id = c.city_id
                                    WHERE r.status = 'disapproved'";
$disapproved_registrations_result = $conn->query($disapproved_registrations_query);

// Fetch the list of kebele moderators
$kebele_moderators_query = "SELECT km.kebeleModerator_id, km.username, k.kebele_name, c.city_name 
                            FROM kebeleModerator km
                            JOIN kebele k ON km.kebele_id = k.kebele_id
                            JOIN city c ON k.city_id = c.city_id";
$kebele_moderators_result = $conn->query($kebele_moderators_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderator Dashboard</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-header h5 {
            margin-bottom: 0;
        }
        .card-body {
            max-height: 400px;
            overflow-y: auto;
        }
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
        <h1 class="mb-4">Moderator Dashboard</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Pending Registrations</h5>
                        <span class="badge bg-warning"><?php echo $pending_registrations_result->num_rows; ?></span>
                    </div>
                    <div class="card-body">
                        <?php while ($row = $pending_registrations_result->fetch_assoc()) : ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6><?php echo $row['username']; ?></h6>
                                    <p class="mb-0"><?php echo $row['full_name']; ?></p>
                                    <p class="mb-0"><?php echo $row['kebele_name']; ?>, <?php echo $row['city_name']; ?></p>
                                </div>
                                <div>
                                    <a href="moderator_approve_resident.php?id=<?php echo $row['resident_id']; ?>" class="btn btn-success btn-sm mr-2">Approve</a>
                                    <a href="moderator_disapprove_resident.php?id=<?php echo $row['resident_id']; ?>" class="btn btn-danger btn-sm">Disapprove</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Approved Registrations</h5>
                        <span class="badge bg-success"><?php echo $approved_registrations_result->num_rows; ?></span>
                    </div>
                    <div class="card-body">
                        <?php while ($row = $approved_registrations_result->fetch_assoc()) : ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6><?php echo $row['username']; ?></h6>
                                    <p class="mb-0"><?php echo $row['full_name']; ?></p>
                                    <p class="mb-0"><?php echo $row['kebele_name']; ?>, <?php echo $row['city_name']; ?></p>
                                </div>
                                <div>
                                    <span class="status-pill approved">Approved</span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Disapproved Registrations</h5>
                        <span class="badge bg-danger"><?php echo $disapproved_registrations_result->num_rows; ?></span>
                    </div>
                    <div class="card-body">
                        <?php while ($row = $disapproved_registrations_result->fetch_assoc()) : ?>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6><?php echo $row['username']; ?></h6>
                                    <p class="mb-0"><?php echo $row['full_name']; ?></p>
                                    <p class="mb-0"><?php echo $row['kebele_name']; ?>, <?php echo $row['city_name']; ?></p>
                                </div>
                                <div>
                                    <span class="status-pill disapproved">Disapproved</span>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Kebele Moderators</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Kebele</th>
                                    <th>City</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $kebele_moderators_result->fetch_assoc()) : ?>
                                    <tr>
                                        <td><?php echo $row['username']; ?></td>
                                        <td><?php echo $row['kebele_name']; ?></td>
                                        <td><?php echo $row['city_name']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Registration Status</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="registrationChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/chart.js"></script>
    <script>
        // Create a chart to display the registration status
        var ctx = document.getElementById('registrationChart').getContext('2d');
        var registrationChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Approved', 'Disapproved'],
                datasets: [{
                    label: 'Registration Status',
                    data: [
                        <?php echo $pending_registrations_result->num_rows; ?>,
                        <?php echo $approved_registrations_result->num_rows; ?>,
                        <?php echo $disapproved_registrations_result->num_rows; ?>
                    ],
                    backgroundColor: [
                        '#ffc107',
                        '#28a745',
                        '#dc3545'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Registration Status'
                    }
                }
            }
        });
    </script>
</body>
</html>
