<?php

$servername = "localhost";
$username = "root"; // Your MySQL username
$password = ""; // Your MySQL password
$dbname = "CR";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db($dbname);

// SQL code for table creation
$sql = "
-- Table: admin
CREATE TABLE IF NOT EXISTS admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Table: region
CREATE TABLE IF NOT EXISTS region (
    region_id INT AUTO_INCREMENT PRIMARY KEY,
    region_name VARCHAR(50) NOT NULL UNIQUE
);

-- Table: zone
CREATE TABLE IF NOT EXISTS zone (
    zone_id INT AUTO_INCREMENT PRIMARY KEY,
    zone_name VARCHAR(50) NOT NULL,
    region_id INT,
    FOREIGN KEY (region_id) REFERENCES region(region_id) ON DELETE CASCADE
);

-- Table: woreda
CREATE TABLE IF NOT EXISTS woreda (
    woreda_id INT AUTO_INCREMENT PRIMARY KEY,
    woreda_name VARCHAR(50) NOT NULL,
    zone_id INT,
    FOREIGN KEY (zone_id) REFERENCES zone(zone_id) ON DELETE CASCADE
);

-- Table: city
CREATE TABLE IF NOT EXISTS city (
    city_id INT AUTO_INCREMENT PRIMARY KEY,
    city_name VARCHAR(50) NOT NULL UNIQUE,
    woreda_id INT,
    FOREIGN KEY (woreda_id) REFERENCES woreda(woreda_id) ON DELETE CASCADE
);

-- Table: kebele
CREATE TABLE IF NOT EXISTS kebele (
    kebele_id INT AUTO_INCREMENT PRIMARY KEY,
    kebele_name VARCHAR(50) NOT NULL,
    city_id INT,
    FOREIGN KEY (city_id) REFERENCES city(city_id) ON DELETE CASCADE
);

-- Table: moderator
CREATE TABLE IF NOT EXISTS moderator (
    moderator_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    city_id INT,
    FOREIGN KEY (city_id) REFERENCES city(city_id) ON DELETE CASCADE
);

-- Table: kebeleModerator
CREATE TABLE IF NOT EXISTS kebeleModerator (
    kebeleModerator_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    moderator_id INT,
    kebele_id INT,
    FOREIGN KEY (moderator_id) REFERENCES moderator(moderator_id) ON DELETE CASCADE,
    FOREIGN KEY (kebele_id) REFERENCES kebele(kebele_id) ON DELETE CASCADE
);

-- Table: residents
CREATE TABLE IF NOT EXISTS residents (
    resident_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    image VARCHAR(255),
    region_id INT,
    zone_id INT,
    woreda_id INT,
    city_id INT,
    kebele_id INT,
    status ENUM('pending', 'approved', 'disapproved') DEFAULT 'pending',
    FOREIGN KEY (region_id) REFERENCES region(region_id) ON DELETE CASCADE,
    FOREIGN KEY (zone_id) REFERENCES zone(zone_id) ON DELETE CASCADE,
    FOREIGN KEY (woreda_id) REFERENCES woreda(woreda_id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES city(city_id) ON DELETE CASCADE,
    FOREIGN KEY (kebele_id) REFERENCES kebele(kebele_id) ON DELETE CASCADE
);

-- Table: events
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(100) NOT NULL,
    event_description TEXT,
    event_date DATE
);
";

// Execute SQL query
if ($conn->multi_query($sql) === TRUE) {

    // Fetch all results to clear the buffer
    while ($conn->next_result()) {
        if (!$conn->more_results()) {
            break;
        }
    }
    
    // Insert default admin username and password
    $admin_username = "adem";
    $admin_password = password_hash("adem123", PASSWORD_DEFAULT);

    // Check if the default admin already exists
    $check_sql = "SELECT COUNT(*) as count FROM admin WHERE username = '$admin_username'";
    $result = $conn->query($check_sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $admin_count = $row['count'];
        if ($admin_count > 0) {
        } else {
            // Insert default admin username and password
            $insert_sql = "INSERT INTO admin (username, password) VALUES ('$admin_username', '$admin_password')";

            if ($conn->query($insert_sql) === TRUE) {
            } else {
                echo "Error inserting default admin: " . $conn->error;
            }
        }
        $result->free();
    } else {
        echo "Error checking default admin: " . $conn->error;
    }
} else {
    echo "Error creating tables: " . $conn->error;
}

