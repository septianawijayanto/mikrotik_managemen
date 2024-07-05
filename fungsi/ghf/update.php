<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    if ($id == 0) {
        die("Invalid ID.");
    }

    $old_profile_name = $_POST['old_profile_name']; // The current profile name before the update
    $new_profile_name = $_POST['profile_name']; // The new profile name to be updated
    $shared_users = intval($_POST['shared_users']);
    $rate_limit = $_POST['rate_limit'];
    $session_timeout = $_POST['session_timeout'];

    $API = new RouterosAPI();

    if ($API->connect($host, $login, $password, $port, $timeout)) {
        // Fetch profile details to get the correct MikroTik ID
        $profiles = $API->comm("/ip/hotspot/user/profile/print", array("?name" => $old_profile_name));

        if (empty($profiles)) {
            echo "Profile not found in MikroTik.<br>";
        } else {
            $mikrotik_id = $profiles[0]['.id'];

            // Update profile in MikroTik
            $response = $API->comm("/ip/hotspot/user/profile/set", array(
                ".id" => $mikrotik_id, // Use correct MikroTik ID
                "name" => $new_profile_name,
                "shared-users" => $shared_users,
                "rate-limit" => $rate_limit,
                "session-timeout" => $session_timeout
            ));

            if (isset($response['!trap'])) {
                echo "Failed to update profile in MikroTik: " . $response['!trap'][0]['message'] . "<br>";
            } else {
                echo "Profile updated in MikroTik.<br>";

                // Update profile in the database
                $stmt = $conn->prepare("UPDATE tm_profil SET profile_name = ?, shared_users = ?, rate_limit = ?, session_timeout = ? WHERE id = ?");
                if (!$stmt) {
                    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                }
                $stmt->bind_param("sissi", $new_profile_name, $shared_users, $rate_limit, $session_timeout, $id);

                if ($stmt->execute()) {
                    echo "Profile updated successfully in the database.<br>";
                } else {
                    echo "Failed to update profile in the database: " . $stmt->error . "<br>";
                }

                $stmt->close();
            }
        }

        $API->disconnect();
    } else {
        echo 'Not Connected to Mikrotik.';
    }
}
