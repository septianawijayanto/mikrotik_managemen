<?php
require('../config/device.php');
require('../config/config.php');
include('../routeros_api.class.php');
function generateRandomString($length = 8)
{
    return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', $length)), 0, $length);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $profile = $_POST['profile'];
    $voucher_count = intval($_POST['voucher_count']);
    $limit_uptime = $_POST['limit_uptime']; // Dapatkan limit uptime dari form
    $API = new RouterosAPI();

    if ($API->connect($host, $login, $password, $port, $timeout)) {
        for ($i = 0; $i < $voucher_count; $i++) {
            $username = generateRandomString();
            $API->comm("/ip/hotspot/user/add", array(
                "name" => $username,
                "password" => $username,
                "profile" => $profile,
                "limit-uptime" => $limit_uptime, // Tambahkan limit uptime
            ));

            echo 'Voucher Created: ' . $username . '/' . $username . ' with profile ' . $profile . '<br>';
        }

        $API->disconnect();
    } else {
        echo 'Not Connected';
    }
}
