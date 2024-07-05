<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $user_password = htmlspecialchars($_POST['password']); // Rename variabel untuk password form
    $profile = $_POST['profile'];
    $limit_uptime = $_POST['limit_uptime'];
    $API = new RouterosAPI();

    // Connect to MikroTik
    if ($API->connect($host, $login, $password, $port, $timeout)) {
        // Check if the user already exists in MikroTik
        $users = $API->comm("/ip/hotspot/user/print");
        $user_exists = false;
        foreach ($users as $user) {
            if ($user['name'] == $username) {
                $user_exists = true;
                break;
            }
        }

        if ($user_exists) {
            echo 'User already exists in MikroTik: ' . $username . '<br>';
        } else {
            // Add the new user to MikroTik
            $API->comm("/ip/hotspot/user/add", array(
                "name" => $username,
                "password" => $user_password, // Gunakan variabel yang benar
                "profile" => $profile,
                "limit-uptime" => $limit_uptime,
            ));
            echo 'User Created in MikroTik: ' . $username . '<br>';
        }

        // Disconnect from MikroTik
        $API->disconnect();
    } else {
        echo 'Not Connected to MikroTik<br>';
    }

    // Check for existing user in the database
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tm_user WHERE name = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo 'User already exists in the database: ' . $username . '<br>';
    } else {
        // Insert user data into the database
        $type = "user";
        $stmt = $conn->prepare("INSERT INTO tm_user (kode, name, password, profile,time_limit,type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $kodeUser, $username, $user_password, $profile, $limit_uptime, $type);
        if ($stmt->execute()) {
            echo "User saved to database<br>";
        } else {
            echo "Failed to save user to database: " . $stmt->error . '<br>';
        }
        $stmt->close();
    }
}
