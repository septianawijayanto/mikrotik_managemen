<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $profile_name = $_POST['profile_name'];
    $shared_users = intval($_POST['shared_users']);
    $rate_limit = $_POST['rate_limit'];
    $session_timeout = $_POST['session_timeout'];

    $API = new RouterosAPI();

    // Connect to MikroTik
    if ($API->connect($host, $login, $password, $port, $timeout)) {
        // Check if the profile already exists in MikroTik
        $profiles = $API->comm("/ip/hotspot/user/profile/print");
        $profile_exists = false;
        foreach ($profiles as $profile) {
            if ($profile['name'] == $profile_name) {
                $profile_exists = true;
                break;
            }
        }

        if ($profile_exists) {
            echo 'Profile already exists in MikroTik: ' . $profile_name . '<br>';
        } else {
            // Add the new profile to MikroTik
            $API->comm("/ip/hotspot/user/profile/add", array(
                "name" => $profile_name,
                "shared-users" => $shared_users,
                "rate-limit" => $rate_limit,
                "session-timeout" => $session_timeout
            ));
            echo 'Profile Created in MikroTik: ' . $profile_name . '<br>';
        }

        // Disconnect from MikroTik
        $API->disconnect();
    } else {
        echo 'Not Connected to MikroTik<br>';
    }

    // Check for existing profile in the database
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tm_profil WHERE profile_name = ?");
    $stmt->bind_param("s", $profile_name);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo 'Profile already exists in the database: ' . $profile_name . '<br>';
    } else {
        // Insert profile data into the database
        $stmt = $conn->prepare("INSERT INTO tm_profil (profile_name, shared_users, rate_limit, session_timeout) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $profile_name, $shared_users, $rate_limit, $session_timeout);
        if ($stmt->execute()) {
            echo "Profile saved to database<br>";
        } else {
            echo "Failed to save profile to database: " . $stmt->error . '<br>';
        }
        $stmt->close();
    }
}
