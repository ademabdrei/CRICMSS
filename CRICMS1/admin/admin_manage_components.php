<?php
// Start the session
session_start();

// Check if the user is logged in as an admin
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    // Redirect non-admin users to the login page or a different page
    header('Location: ../login.php');
    exit;
}

// Include database connection file
require_once '../includes/db_connection.php';

// Function to fetch regions from the database
function getRegions($conn)
{
    $sql = "SELECT * FROM region";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to add a new region
function addRegion($conn, $regionName)
{
    $sql = "INSERT INTO region (region_name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $regionName);
    return $stmt->execute();
}

// Function to edit a region
function editRegion($conn, $regionId, $regionName)
{
    $sql = "UPDATE region SET region_name = ? WHERE region_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $regionName, $regionId);
    return $stmt->execute();
}

// Function to delete a region
function deleteRegion($conn, $regionId)
{
    $sql = "DELETE FROM region WHERE region_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $regionId);
    return $stmt->execute();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_region'])) {
        $regionName = $_POST['region_name'];
        addRegion($conn, $regionName);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } elseif (isset($_POST['edit_region'])) {
        $regionId = $_POST['edit_region_id'];
        $regionName = $_POST['edit_region_name'];
        editRegion($conn, $regionId, $regionName);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } elseif (isset($_POST['delete_region'])) {
        $regionId = $_POST['delete_region_id'];
        deleteRegion($conn, $regionId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

// Function to get all zones
function getZones($conn)
{
    $sql = "SELECT * FROM zone";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to add a new zone
function addZone($conn, $zoneName, $regionId)
{
    $sql = "INSERT INTO zone (zone_name, region_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $zoneName, $regionId);
    return $stmt->execute();
}

// Function to edit a zone
function editZone($conn, $zoneId, $zoneName, $regionId)
{
    $sql = "UPDATE zone SET zone_name = ?, region_id = ? WHERE zone_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $zoneName, $regionId, $zoneId);
    return $stmt->execute();
}

// Function to delete a zone
function deleteZone($conn, $zoneId)
{
    $sql = "DELETE FROM zone WHERE zone_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $zoneId);
    return $stmt->execute();
}
// Function to get region name by region ID
function getRegionName($conn, $regionId)
{
    $sql = "SELECT region_name FROM region WHERE region_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $regionId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['region_name'];
    } else {
        return "Region Not Found";
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_zone'])) {
        $zoneName = $_POST['zone_name'];
        $regionId = $_POST['region_id']; // Assuming you have a form field for selecting the region
        addZone($conn, $zoneName, $regionId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } elseif (isset($_POST['edit_zone'])) {
        $zoneId = $_POST['edit_zone_id'];
        $zoneName = $_POST['edit_zone_name'];
        $regionId = $_POST['edit_zone_region_id']; // Assuming you have a form field for selecting the region
        editZone($conn, $zoneId, $zoneName, $regionId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } elseif (isset($_POST['delete_zone'])) {
        $zoneId = $_POST['delete_zone_id'];
        deleteZone($conn, $zoneId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

// Function to get all woredas
function getWoredas($conn)
{
    $sql = "SELECT * FROM woreda";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to add a new woreda
function addWoreda($conn, $woredaName, $zoneId)
{
    $sql = "INSERT INTO woreda (woreda_name, zone_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $woredaName, $zoneId);
    return $stmt->execute();
}

// Function to edit a woreda
function editWoreda($conn, $woredaId, $woredaName, $zoneId)
{
    $sql = "UPDATE woreda SET woreda_name = ?, zone_id = ? WHERE woreda_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $woredaName, $zoneId, $woredaId);
    return $stmt->execute();
}

// Function to delete a woreda
function deleteWoreda($conn, $woredaId)
{
    $sql = "DELETE FROM woreda WHERE woreda_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $woredaId);
    return $stmt->execute();
}
// Function to get zone name by zone ID
function getZoneName($conn, $zoneId)
{
    $sql = "SELECT zone_name FROM zone WHERE zone_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $zoneId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['zone_name'];
    } else {
        return "Zone Not Found";
    }
}


// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_woreda'])) {
        $woredaName = $_POST['woreda_name'];
        $zoneId = $_POST['zone_id']; // Assuming you have a form field for selecting the zone
        addWoreda($conn, $woredaName, $zoneId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } elseif (isset($_POST['edit_woreda'])) {
        $woredaId = $_POST['edit_woreda_id'];
        $woredaName = $_POST['edit_woreda_name'];
        $zoneId = $_POST['edit_woreda_zone_id']; // Assuming you have a form field for selecting the zone
        editWoreda($conn, $woredaId, $woredaName, $zoneId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } elseif (isset($_POST['delete_woreda'])) {
        $woredaId = $_POST['delete_woreda_id'];
        deleteWoreda($conn, $woredaId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}






// Function to get all cities
function getCities($conn)
{
    $sql = "SELECT * FROM city";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to add a new city
function addCity($conn, $cityName, $woredaId)
{
    $sql = "INSERT INTO city (city_name, woreda_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $cityName, $woredaId);
    return $stmt->execute();
}

// Function to edit a city
function editCity($conn, $cityId, $cityName, $woredaId)
{
    $sql = "UPDATE city SET city_name = ?, woreda_id = ? WHERE city_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $cityName, $woredaId, $cityId);
    return $stmt->execute();
}

// Function to delete a city
function deleteCity($conn, $cityId)
{
    $sql = "DELETE FROM city WHERE city_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cityId);
    return $stmt->execute();
}

// Function to get woreda name by woreda ID
function getWoredaName($conn, $woredaId)
{
    $sql = "SELECT woreda_name FROM woreda WHERE woreda_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $woredaId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['woreda_name'];
    } else {
        return "Woreda Not Found";
    }
}

// Handle form submissions for cities
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_city'])) {
        $cityName = $_POST['city_name'];
        $woredaId = $_POST['woreda_id']; // Assuming you have a form field for selecting the woreda
        addCity($conn, $cityName, $woredaId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } elseif (isset($_POST['edit_city'])) {
        $cityId = $_POST['edit_city_id'];
        $cityName = $_POST['edit_city_name'];
        $woredaId = $_POST['edit_city_woreda_id']; // Assuming you have a form field for selecting the woreda
        editCity($conn, $cityId, $cityName, $woredaId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } elseif (isset($_POST['delete_city'])) {
        $cityId = $_POST['delete_city_id'];
        deleteCity($conn, $cityId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}


// Function to get all kebeles
function getKebeles($conn)
{
    $sql = "SELECT * FROM kebele";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to add a new kebele
function addKebele($conn, $kebeleName, $cityId)
{
    $sql = "INSERT INTO kebele (kebele_name, city_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $kebeleName, $cityId);
    return $stmt->execute();
}

// Function to edit a kebele
function editKebele($conn, $kebeleId, $kebeleName, $cityId)
{
    $sql = "UPDATE kebele SET kebele_name = ?, city_id = ? WHERE kebele_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $kebeleName, $cityId, $kebeleId);
    return $stmt->execute();
}

// Function to delete a kebele
function deleteKebele($conn, $kebeleId)
{
    $sql = "DELETE FROM kebele WHERE kebele_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $kebeleId);
    return $stmt->execute();
}

// Function to get city name by city ID
function getCityName($conn, $cityId)
{
    $sql = "SELECT city_name FROM city WHERE city_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cityId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['city_name'];
    } else {
        return "City Not Found";
    }
}

// Handle form submissions for kebeles
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_kebele'])) {
        $kebeleName = $_POST['kebele_name'];
        $cityId = $_POST['city_id']; // Assuming you have a form field for selecting the city
        addKebele($conn, $kebeleName, $cityId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } elseif (isset($_POST['edit_kebele'])) {
        $kebeleId = $_POST['edit_kebele_id'];
        $kebeleName = $_POST['edit_kebele_name'];
        $cityId = $_POST['edit_kebele_city_id']; // Assuming you have a form field for selecting the city
        editKebele($conn, $kebeleId, $kebeleName, $cityId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    } elseif (isset($_POST['delete_kebele'])) {
        $kebeleId = $_POST['delete_kebele_id'];
        deleteKebele($conn, $kebeleId);
        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manage Components - City Resident ID Card Management System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <div class="container mt-5">
        <h2 class="mb-4">Manage City Components</h2>
        <ul class="nav nav-tabs mb-4" id="componentTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="region-tab" data-toggle="tab" href="#region" role="tab" aria-controls="region" aria-selected="true">Regions</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="zone-tab" data-toggle="tab" href="#zone" role="tab" aria-controls="zone" aria-selected="false">Zones</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="woreda-tab" data-toggle="tab" href="#woreda" role="tab" aria-controls="woreda" aria-selected="false">Woredas</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="city-tab" data-toggle="tab" href="#city" role="tab" aria-controls="city" aria-selected="false">Cities</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="kebele-tab" data-toggle="tab" href="#kebele" role="tab" aria-controls="kebele" aria-selected="false">Kebeles</a>
            </li>
        </ul>
        
        <!-- Tab panes -->
        <div class="tab-content">

            <div class="tab-pane fade show active" id="region" role="tabpanel" aria-labelledby="region-tab">
                <div class="container mt-4">
                    <h4 class="mb-3">Region Management</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Add New Region
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                        <div class="form-group">
                                            <label for="regionName">Region Name:</label>
                                            <input type="text" name="region_name" class="form-control" id="regionName" placeholder="Enter Region Name" required>
                                        </div>
                                        <button type="submit" name="add_region" class="btn btn-primary">Add Region</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Existing Regions
                                </div>
                                <div class="card-body">
                                    <ul class="list-group" id="regionList">
                                        <!-- PHP loop to populate region list -->
                                        <?php $regions = getRegions($conn);
                                        foreach ($regions as $region) : ?>
                                            <li class="list-group-item">
                                                <?php echo $region['region_name']; ?>
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="edit_region_id" value="<?php echo $region['region_id']; ?>">
                                                    <input type="text" name="edit_region_name" class="form-control d-inline" value="<?php echo $region['region_name']; ?>" required>
                                                    <button type="submit" name="edit_region" class="btn btn-sm btn-primary ml-2">Edit</button>
                                                </form>
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="delete_region_id" value="<?php echo $region['region_id']; ?>">
                                                    <button type="submit" name="delete_region" class="btn btn-sm btn-danger ml-2">Delete</button>
                                                </form>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="zone" role="tabpanel" aria-labelledby="zone-tab">
                <div class="container mt-4">
                    <h4 class="mb-3">Zone Management</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Add New Zone
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                        <div class="form-group">
                                            <label for="zoneName">Zone Name:</label>
                                            <input type="text" name="zone_name" class="form-control" id="zoneName" placeholder="Enter Zone Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="zoneRegion">Select Region:</label>
                                            <select name="region_id" class="form-control" id="zoneRegion" required>
                                                <option value="">Select Region</option>
                                                <!-- PHP loop to populate region options -->
                                                <?php
                                                $regions = getRegions($conn);
                                                foreach ($regions as $region) {
                                                    echo "<option value='{$region['region_id']}'>{$region['region_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" name="add_zone" class="btn btn-primary">Add Zone</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Existing Zones
                                </div>
                                <div class="card-body">
                                    <ul class="list-group" id="zoneList">
                                        <!-- PHP loop to populate zone list -->
                                        <?php
                                        $zones = getZones($conn);
                                        foreach ($zones as $zone) : ?>
                                            <li class="list-group-item">
                                                <?php echo $zone['zone_name']; ?> (Region: <?php echo getRegionName($conn, $zone['region_id']); ?>)
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="edit_zone_id" value="<?php echo $zone['zone_id']; ?>">
                                                    <input type="text" name="edit_zone_name" class="form-control d-inline" value="<?php echo $zone['zone_name']; ?>" required>
                                                    <select name="edit_zone_region_id" class="form-control d-inline" required>
                                                        <?php
                                                        foreach ($regions as $region) {
                                                            $selected = ($region['region_id'] == $zone['region_id']) ? "selected" : "";
                                                            echo "<option value='{$region['region_id']}' $selected>{$region['region_name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <button type="submit" name="edit_zone" class="btn btn-sm btn-primary ml-2">Edit</button>
                                                </form>
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="delete_zone_id" value="<?php echo $zone['zone_id']; ?>">
                                                    <button type="submit" name="delete_zone" class="btn btn-sm btn-danger ml-2">Delete</button>
                                                </form>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="woreda" role="tabpanel" aria-labelledby="woreda-tab">
                <div class="container mt-4">
                    <h4 class="mb-3">Woreda Management</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Add New Woreda
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                        <div class="form-group">
                                            <label for="woredaName">Woreda Name:</label>
                                            <input type="text" name="woreda_name" class="form-control" id="woredaName" placeholder="Enter Woreda Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="woredaZone">Select Zone:</label>
                                            <select name="zone_id" class="form-control" id="woredaZone" required>
                                                <option value="">Select Zone</option>
                                                <!-- PHP loop to populate zone options -->
                                                <?php
                                                $zones = getZones($conn);
                                                foreach ($zones as $zone) {
                                                    echo "<option value='{$zone['zone_id']}'>{$zone['zone_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" name="add_woreda" class="btn btn-primary">Add Woreda</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Existing Woredas
                                </div>
                                <div class="card-body">
                                    <ul class="list-group" id="woredaList">
                                        <!-- PHP loop to populate woreda list -->
                                        <?php
                                        // Function to get all woredas

                                        $woredas = getWoredas($conn);
                                        foreach ($woredas as $woreda) : ?>
                                            <li class="list-group-item">
                                                <?php echo $woreda['woreda_name']; ?> (Zone: <?php echo getZoneName($conn, $woreda['zone_id']); ?>)
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="edit_woreda_id" value="<?php echo $woreda['woreda_id']; ?>">
                                                    <input type="text" name="edit_woreda_name" class="form-control d-inline" value="<?php echo $woreda['woreda_name']; ?>" required>
                                                    <select name="edit_woreda_zone_id" class="form-control d-inline" required>
                                                        <?php
                                                        foreach ($zones as $zone) {
                                                            $selected = ($zone['zone_id'] == $woreda['zone_id']) ? "selected" : "";
                                                            echo "<option value='{$zone['zone_id']}' $selected>{$zone['zone_name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <button type="submit" name="edit_woreda" class="btn btn-sm btn-primary ml-2">Edit</button>
                                                </form>
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="delete_woreda_id" value="<?php echo $woreda['woreda_id']; ?>">
                                                    <button type="submit" name="delete_woreda" class="btn btn-sm btn-danger ml-2">Delete</button>
                                                </form>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="city" role="tabpanel" aria-labelledby="city-tab">
                <div class="container mt-4">
                    <h4 class="mb-3">City Management</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Add New City
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                        <div class="form-group">
                                            <label for="cityName">City Name:</label>
                                            <input type="text" name="city_name" class="form-control" id="cityName" placeholder="Enter City Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="cityWoreda">Select Woreda:</label>
                                            <select name="woreda_id" class="form-control" id="cityWoreda" required>
                                                <option value="">Select Woreda</option>
                                                <!-- PHP loop to populate woreda options -->
                                                <?php
                                                $woredas = getWoredas($conn);
                                                foreach ($woredas as $woreda) {
                                                    echo "<option value='{$woreda['woreda_id']}'>{$woreda['woreda_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" name="add_city" class="btn btn-primary">Add City</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Existing Cities
                                </div>
                                <div class="card-body">
                                    <ul class="list-group" id="cityList">
                                        <!-- PHP loop to populate city list -->
                                        <?php
                                        // Function to get all cities
                                        $cities = getCities($conn);
                                        foreach ($cities as $city) : ?>
                                            <li class="list-group-item">
                                                <?php echo $city['city_name']; ?> (Woreda: <?php echo getWoredaName($conn, $city['woreda_id']); ?>)
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="edit_city_id" value="<?php echo $city['city_id']; ?>">
                                                    <input type="text" name="edit_city_name" class="form-control d-inline" value="<?php echo $city['city_name']; ?>" required>
                                                    <select name="edit_city_woreda_id" class="form-control d-inline" required>
                                                        <?php
                                                        foreach ($woredas as $woreda) {
                                                            $selected = ($woreda['woreda_id'] == $city['woreda_id']) ? "selected" : "";
                                                            echo "<option value='{$woreda['woreda_id']}' $selected>{$woreda['woreda_name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <button type="submit" name="edit_city" class="btn btn-sm btn-primary ml-2">Edit</button>
                                                </form>
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="delete_city_id" value="<?php echo $city['city_id']; ?>">
                                                    <button type="submit" name="delete_city" class="btn btn-sm btn-danger ml-2">Delete</button>
                                                </form>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="kebele" role="tabpanel" aria-labelledby="kebele-tab">
                <div class="container mt-4">
                    <h4 class="mb-3">Kebele Management</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Add New Kebele
                                </div>
                                <div class="card-body">
                                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                                        <div class="form-group">
                                            <label for="kebeleName">Kebele Name:</label>
                                            <input type="text" name="kebele_name" class="form-control" id="kebeleName" placeholder="Enter Kebele Name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="kebeleCity">Select City:</label>
                                            <select name="city_id" class="form-control" id="kebeleCity" required>
                                                <option value="">Select City</option>
                                                <!-- PHP loop to populate city options -->
                                                <?php
                                                $cities = getCities($conn);
                                                foreach ($cities as $city) {
                                                    echo "<option value='{$city['city_id']}'>{$city['city_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" name="add_kebele" class="btn btn-primary">Add Kebele</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    Existing Kebeles
                                </div>
                                <div class="card-body">
                                    <ul class="list-group" id="kebeleList">
                                        <!-- PHP loop to populate kebele list -->
                                        <?php
                                        // Function to get all kebeles

                                        $kebeles = getKebeles($conn);
                                        foreach ($kebeles as $kebele) : ?>
                                            <li class="list-group-item">
                                                <?php echo $kebele['kebele_name']; ?> (City: <?php echo getCityName($conn, $kebele['city_id']); ?>)
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="edit_kebele_id" value="<?php echo $kebele['kebele_id']; ?>">
                                                    <input type="text" name="edit_kebele_name" class="form-control d-inline" value="<?php echo $kebele['kebele_name']; ?>" required>
                                                    <select name="edit_kebele_city_id" class="form-control d-inline" required>
                                                        <?php
                                                        foreach ($cities as $city) {
                                                            $selected = ($city['city_id'] == $kebele['city_id']) ? "selected" : "";
                                                            echo "<option value='{$city['city_id']}' $selected>{$city['city_name']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                    <button type="submit" name="edit_kebele" class="btn btn-sm btn-primary ml-2">Edit</button>
                                                </form>
                                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="d-inline">
                                                    <input type="hidden" name="delete_kebele_id" value="<?php echo $kebele['kebele_id']; ?>">
                                                    <button type="submit" name="delete_kebele" class="btn btn-sm btn-danger ml-2">Delete</button>
                                                </form>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle component type selection in the add modal
            $('#component-type').on('change', function() {
                var componentType = $(this).val();
                if (componentType === 'zone' || componentType === 'woreda' || componentType === 'city' || componentType === 'kebele') {
                    $('#parent-component-container').show();
                    if (componentType === 'zone') {
                        $('#parent-component').html('<option value="">Select a region</option><?php foreach ($regions as $region) : ?><option value="<?php echo $region['region_id']; ?>"><?php echo $region['region_name']; ?></option><?php endforeach; ?>');
                    } else if (componentType === 'woreda') {
                        $('#parent-component').html('<option value="">Select a zone</option><?php foreach ($zones as $zone) : ?><option value="<?php echo $zone['zone_id']; ?>"><?php echo $zone['zone_name']; ?></option><?php endforeach; ?>');
                    } else if (componentType === 'city') {
                        $('#parent-component').html('<option value="">Select a woreda</option><?php foreach ($woredas as $woreda) : ?><option value="<?php echo $woreda['woreda_id']; ?>"><?php echo $woreda['woreda_name']; ?></option><?php endforeach; ?>');
                    } else if (componentType === 'kebele') {
                        $('#parent-component').html('<option value="">Select a city</option><?php foreach ($cities as $city) : ?><option value="<?php echo $city['city_id']; ?>"><?php echo $city['city_name']; ?></option><?php endforeach; ?>');
                    }
                } else {
                    $('#parent-component-container').hide();
                }
            });

            // Handle edit button click
            $('.edit-component').on('click', function() {
                var componentType = $(this).data('component-type');
                var componentId = $(this).data('component-id');
                var componentName = $(this).data('component-name');

                $('#edit-component-type').val(componentType);
                $('#edit-component-name').val(componentName);
                $('#edit-component-id').val(componentId);

                $('#editComponentModal').modal('show');
            });

            // Handle delete button click
            $('.delete-component').on('click', function() {
                var componentType = $(this).data('component-type');
                var componentId = $(this).data('component-id');

                $('#delete-component-type').val(componentType);
                $('#delete-component-id').val(componentId);

                $('#deleteComponentModal').modal('show');
            });
        });
    </script>
</body>

</html>