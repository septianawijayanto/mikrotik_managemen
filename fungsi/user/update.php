<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    if ($id == 0) {
        die("Invalid ID.");
    }

    $old_username = $_POST['old_username']; // The current profile name before the update
    $username = htmlspecialchars($_POST['username']);
    $user_password = htmlspecialchars($_POST['password']); // Rename variabel untuk password form
    $profile = $_POST['profile'];
    $limit_uptime = $_POST['limit_uptime'];

    $API = new RouterosAPI();

    if ($API->connect($host, $login, $password, $port, $timeout)) {
        // Fetch profile details to get the correct MikroTik ID
        $user = $API->comm("/ip/hotspot/user/print", array("?name" => $old_username));

        if (empty($user)) {
            echo "Profile not found in MikroTik.<br>";
        } else {
            $mikrotik_id = $user[0]['.id'];

            // Update user hotspot in MikroTik
            $response = $API->comm("/ip/hotspot/user/set", array(
                ".id" => $mikrotik_id, // Use correct MikroTik ID
                "name" => $username,
                "password" => $user_password, // Gunakan variabel yang benar
                "profile" => $profile,
                "limit-uptime" => $limit_uptime,
            ));

            if (isset($response['!trap'])) {
                echo "Failed to update user hotspot in MikroTik: " . $response['!trap'][0]['message'] . "<br>";
            } else {
                echo "User hotspot updated in MikroTik.<br>";

                // Update user hotspot in the database
                $stmt = $conn->prepare("UPDATE tm_user SET name = ?, password = ?, profile = ?, time_limit = ? WHERE id = ?");
                if (!$stmt) {
                    echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
                }
                $stmt->bind_param("sissi", $username, $user_password, $profile, $limit_uptime, $id);

                if ($stmt->execute()) {
                    echo "User hotspot updated successfully in the database.<br>";
                } else {
                    echo "Failed to update user hotspot in the database: " . $stmt->error . "<br>";
                }

                $stmt->close();
            }
        }

        $API->disconnect();
    } else {
        echo 'Not Connected to Mikrotik.';
    }
}
