<?php
// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Resident ID Card Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/style../.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="index.php">City Resident ID Card Management System</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="about.php">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="contact.php">Contact</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../nav-link/faq.php">FAQ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../nav-link/blog.php">Blog</a>
                        </li>
                        <?php if (isset($_SESSION["user_type"])) { ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="badge badge-pill badge-info"><?php echo ucfirst($_SESSION["user_type"]); ?></span> <?php echo $_SESSION["username"]; ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <?php if ($_SESSION["user_type"] == "admin") { ?>
                                        <a class="dropdown-item" href="../admin/admin_dashboard.php">Dashboard</a>
                                        <a class="dropdown-item" href="../admin/admin_register_resident.php">Register Resident</a>
                                        <a class="dropdown-item" href="../admin/admin_manage_moderators.php">Manage Moderators</a>
                                        <a class="dropdown-item" href="../admin/admin_manage_kebele_moderators.php">Manage kebeleModerator</a>
                                        <a class="dropdown-item" href="../admin/admin_manage_users.php">Manage User</a>
                                        <a class="dropdown-item" href="../admin/admin_manage_components.php">Manage Components</a>
                                        <a class="dropdown-item" href="notifications.php">Notifications</a>
                                        <a class="dropdown-item" href="profile.php">Profile</a>
                                        <a class="dropdown-item" href="../logout.php">Logout</a>
                                    <?php } elseif ($_SESSION["user_type"] == "moderator") { ?>
                                        <a class="dropdown-item" href="../moderator/moderator_dashboard.php">Dashboard</a>
                                        <a class="dropdown-item" href="../moderator/moderator_manage_residents.php">Manage Residents</a>
                                        <a class="dropdown-item" href="../moderator/moderator_approve_resident.php">Approve Residents</a>
                                        <a class="dropdown-item" href="../moderator/moderator_manage_kebele_moderators.php">Manage Kebele Moderators</a>
                                        <a class="dropdown-item" href="../moderator/moderator_manage_kebele.php">Manage Kebele</a>
                                        <a class="dropdown-item" href="notifications.php">Notifications</a>
                                        <a class="dropdown-item" href="profile.php">Profile</a>
                                        <a class="dropdown-item" href="../logout.php">Logout</a>
                                    <?php } elseif ($_SESSION["user_type"] == "kebeleModerator") { ?>
                                        <a class="dropdown-item" href="../kebeleModerator/kebeleModerator_dashboard.php">Dashboard</a>
                                        <a class="dropdown-item" href="../kebeleModerator/kebeleModerator_manage_residents.php">Manage Residents</a>
                                        <a class="dropdown-item" href="../kebeleModerator/kebeleModerator_approve_resident.php">Approve Residents</a>
                                        <a class="dropdown-item" href="notifications.php">Notifications</a>
                                        <a class="dropdown-item" href="profile.php">Profile</a>
                                        <a class="dropdown-item" href="../logout.php">Logout</a>
                                    <?php } elseif ($_SESSION["user_type"] == "resident") { ?>
                                        <a class="dropdown-item" href="../resident/resident_dashboard.php">Dashboard</a>
                                        <a class="dropdown-item" href="../resident/resident_view_id_card.php">View ID Card</a>
                                        <a class="dropdown-item" href="resident/notifications.php">Notifications</a>
                                        <a class="dropdown-item" href="resident/profile.php">Profile</a>
                                        <a class="dropdown-item" href="../logout.php">Logout</a>
                                    <?php } ?>
                                </div>
                            </li>
                        <?php } else { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Login</a>

                                
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="resident/resident_register.php">Register</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap JavaScript dependencies -->
    <script src="../js/jquery-3.5.1.slim.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>

</html>