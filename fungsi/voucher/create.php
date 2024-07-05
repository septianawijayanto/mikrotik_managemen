<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');

function generateRandomString($length = 8)
{
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $profile = $_POST['profile'];
    $voucher_count = intval($_POST['voucher_count']);
    $limit_uptime = $_POST['limit_uptime']; // Dapatkan limit uptime dari form
    $API = new RouterosAPI();
    $response = "";

    if ($API->connect($host, $login, $password, $port, $timeout)) {
        $stmt = $conn->prepare("INSERT INTO tm_user (kode, name, password, profile, time_limit,type) VALUES (?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            die("Failed to prepare statement: " . $conn->error);
        }

        for ($i = 0; $i < $voucher_count; $i++) {
            $username = generateRandomString();


            $API->comm("/ip/hotspot/user/add", array(
                "name" => $username,
                "password" => $username,
                "profile" => $profile,
                "limit-uptime" => $limit_uptime, // Tambahkan limit uptime
            ));
            $type = "voucher";
            $stmt->bind_param("ssssss", $kodeVoucher, $username, $username, $profile, $limit_uptime, $type);

            if ($stmt->execute()) {
                $response .= "Voucher Created: $username with profile $profile and limit uptime $limit_uptime<br>";
            } else {
                $response .= "Failed to add voucher $username: " . $stmt->error . "<br>";
            }
        }

        $stmt->close();
        $API->disconnect();
    } else {
        $response .= 'Not Connected';
    }

    echo $response;
}
