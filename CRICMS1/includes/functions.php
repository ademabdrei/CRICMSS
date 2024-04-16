<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}











// Debug output
echo "<pre>";
print_r($moderatorsResult->fetch_all(MYSQLI_ASSOC));
echo "</pre>";