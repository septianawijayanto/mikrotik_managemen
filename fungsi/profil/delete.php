<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch profile name from the database
    $result = $conn->query("SELECT profile_name FROM tm_profil WHERE id = $id");
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $profile_name = $row['profile_name'];

        $API = new RouterosAPI();

        if ($API->connect($host, $login, $password, $port, $timeout)) {
            // Remove profile from Mikrotik
            $API->comm("/ip/hotspot/user/profile/remove", array(
                "numbers" => $profile_name
            ));

            // Remove profile from the database
            if ($conn->query("DELETE FROM tm_profil WHERE id = $id") === TRUE) {
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
