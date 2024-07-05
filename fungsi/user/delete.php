<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch profile name from the database
    $result = $conn->query("SELECT name FROM tm_user WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $API = new RouterosAPI();

        if ($API->connect($host, $login, $password, $port, $timeout)) {
            // Remove profile from Mikrotik
            $API->comm("/ip/hotspot/user/remove", array(
                "numbers" => $name
            ));

            // Remove profile from the database
            if ($conn->query("DELETE FROM tm_user WHERE id = $id") === TRUE) {
                echo "Profile deleted successfully.";
            } else {
                echo "Error deleting profile: " . $conn->error;
            }

            $API->disconnect();
        } else {
            echo 'Not Connected';
        }
    } else {
        echo "Profile not found.";
    }
}

$conn->close();
