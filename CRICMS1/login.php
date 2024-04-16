<?php
// Include database connection file
include_once 'includes/db_connection.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $user_type = $_POST["user_type"];

    // Validate user type
    if ($user_type === "admin") {
        $sql = "SELECT * FROM admin WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Set session variables and redirect to admin dashboard
                session_start();
                $_SESSION["user_id"] = $row["admin_id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_type"] = "admin";
                header("Location: admin/admin_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } elseif ($user_type === "moderator") {
        $sql = "SELECT m.moderator_id, m.username, m.password, m.city_id, c.city_name 
                FROM moderator m
                JOIN city c ON m.city_id = c.city_id
                WHERE m.username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Set session variables and redirect to moderator dashboard
                session_start();
                $_SESSION["user_id"] = $row["moderator_id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_type"] = "moderator";
                $_SESSION["city_id"] = $row["city_id"];
                $_SESSION["city_name"] = $row["city_name"];

                header("Location: moderator/moderator_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } elseif ($user_type === "kebeleModerator") {
        $sql = "SELECT km.kebeleModerator_id, m.username, m.password, k.kebele_id, k.kebele_name
                FROM kebeleModerator km
                JOIN moderator m ON km.moderator_id = m.moderator_id
                JOIN kebele k ON km.kebele_id = k.kebele_id
                WHERE m.username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Set session variables and redirect to kebele moderator dashboard
                session_start();
                $_SESSION["user_id"] = $row["kebeleModerator_id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_type"] = "kebeleModerator";
                $_SESSION["kebele_id"] = $row["kebele_id"];
                $_SESSION["kebele_name"] = $row["kebele_name"];

                // Retrieve city, woreda, zone, and region IDs for the kebele moderator's kebele
                $kebele_id = $row["kebele_id"];
                $getKebeleInfoQuery = "SELECT c.city_id, w.woreda_id, z.zone_id, r.region_id 
                                       FROM kebele k
                                       JOIN city c ON k.city_id = c.city_id
                                       JOIN woreda w ON c.woreda_id = w.woreda_id
                                       JOIN zone z ON w.zone_id = z.zone_id
                                       JOIN region r ON z.region_id = r.region_id
                                       WHERE k.kebele_id = ?";
                $stmt = $conn->prepare($getKebeleInfoQuery);
                $stmt->bind_param("i", $kebele_id);
                $stmt->execute();
                $kebeleInfoResult = $stmt->get_result();

                if ($kebeleInfoResult->num_rows > 0) {
                    $kebeleInfo = $kebeleInfoResult->fetch_assoc();
                    $_SESSION["city_id"] = $kebeleInfo["city_id"];
                    $_SESSION["woreda_id"] = $kebeleInfo["woreda_id"];
                    $_SESSION["zone_id"] = $kebeleInfo["zone_id"];
                    $_SESSION["region_id"] = $kebeleInfo["region_id"];
                }

                header("Location: kebeleModerator/kebeleModerator_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } elseif ($user_type === "resident") {
        $sql = "SELECT * FROM residents WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                // Set session variables and redirect to resident dashboard
                session_start();
                $_SESSION["user_id"] = $row["resident_id"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["user_type"] = "resident";
                header("Location: resident/resident_dashboard.php");
                exit;
            } else {
                $error_message = "Invalid username or password.";
            }
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid user type.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - City Resident ID Card Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-center">Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error_message)) { ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error_message; ?>
                            </div>
                        <?php } ?>
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="user_type">User Type</label>
                                <select class="form-control" id="user_type" name="user_type" required>
                                    <option value="">Select User Type</option>
                                    <option value="admin">Admin</option>
                                    <option value="moderator">Moderator</option>
                                    <option value="kebeleModerator">Kebele Moderator</option>
                                    <option value="resident">Resident</option>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>