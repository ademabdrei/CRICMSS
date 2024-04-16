<?php
session_start();
require_once '../includes/db_connection.php'; // Assuming you have a database connection file
include '../includes/header.php';

// Check if the user is logged in as a moderator
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'moderator') {
    // Redirect non-moderator users to the login page or a different page
    header('Location: ../login.php');
    exit;
}

// Fetch the list of all resident registrations
$registrations_query = "SELECT r.resident_id, r.username, r.full_name, r.status, k.kebele_name, c.city_name 
                        FROM residents r
                        JOIN kebele k ON r.kebele_id = k.kebele_id
                        JOIN city c ON r.city_id = c.city_id
                        ORDER BY r.status";
$registrations_result = $conn->query($registrations_query);

// Handle approval or disapproval of a resident registration
if (isset($_POST['action']) && isset($_POST['resident_id'])) {
    $resident_id = $_POST['resident_id'];
    $action = $_POST['action'];

    if ($action == 'approve') {
        $update_query = "UPDATE residents SET status = 'approved' WHERE resident_id = ?";
    } elseif ($action == 'disapprove') {
        $update_query = "UPDATE residents SET status = 'disapproved' WHERE resident_id = ?";
    }

    $stmt = $conn->prepare($update_query);
    $stmt->bind_param('i', $resident_id);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the page after updating the status
    header('Location: moderator_manage_residents.php');
    exit;
}
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
                                <form method="post" class="d-inline-block">
                                    <input type="hidden" name="resident_id" value="<?php echo $row['resident_id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success btn-sm mr-2">Approve</button>
                                </form>
                                <form method="post" class="d-inline-block">
                                    <input type="hidden" name="resident_id" value="<?php echo $row['resident_id']; ?>">
                                    <input type="hidden" name="action" value="disapprove">
                                    <button type="submit" class="btn btn-danger btn-sm">Disapprove</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.min.js"></script>
</body>
</html>