city_resident_id_card_management_system/: Root directory for the project.

├── admin/: Directory for admin-related files.
│   ├── admin_dashboard.php: Dashboard for admin functionalities.
│   ├── admin_register_resident.php: Page for admin to register residents.
│   ├── admin_manage_moderators.php: Page for admin to manage moderator accounts.
│   ├── admin_manage_kebele_moderators.php: Page for admin to manage kebeleModerator accounts.
│   └── admin_manage_components.php: Page for admin to manage city components (regions, zones, etc.).
│   
├── moderator/: Directory for moderator-related files.
│   ├── moderator_dashboard.php: Dashboard for moderator functionalities.
│   ├── moderator_manage_residents.php: Page for moderators to manage resident registrations.
│   ├── moderator_approve_resident.php: Page for moderators to approve resident registrations.
│   ├── moderator_manage_kebele_moderators.php: Page for moderators to manage kebele moderators within their assigned city.
│   └── moderator_manage_kebele.php: Page for moderators to manage kebele  within their assigned city.
│   
├── kebeleModerator/: Directory for kebele-level moderator (kebeleModerator) related files.
│   ├── kebeleModerator_dashboard.php: Dashboard for kebeleModerator functionalities.
│   ├── kebeleModerator_manage_residents.php: Page for kebeleModerators to manage resident registrations within their assigned kebeles.
│   └── kebeleModerator_approve_resident.php: Page for kebeleModerators to approve or reject resident registrations within their assigned kebeles.
│   
├── resident/: Directory for resident-related files.
│   ├── resident_dashboard.php: Dashboard for resident functionalities.
│   ├── resident_register.php: Page for residents to register for ID cards.
│   └── resident_view_id_card.php: Page for residents to view their ID cards.
│   
├── includes/: Directory for common includes.
│   ├── db_connection.php: PHP file for establishing database connection.
│   ├── header.php: Header file to be included in all pages.
│   └── footer.php: Footer file to be included in all pages.
│   
├── css/: Directory for CSS files.
│   └── style.css: Main stylesheet for the project.
│   
├── js/: Directory for JavaScript files.
│   └── script.js: Main JavaScript file for the project.
│   
├── images/: Directory for image files.
│   
├── index.php
├── login.php 
├── logout.php
│   
└── db/: Directory for database-related files.
    └── city_resident_id_card_management.sql: SQL file for database setup and population.









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