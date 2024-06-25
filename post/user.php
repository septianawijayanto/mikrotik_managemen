<?php
require('../config/device.php');
require('../config/config.php');
include('../routeros_api.class.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $user_password = htmlspecialchars($_POST['password']); // Rename variabel untuk password form
    $profile = $_POST['profile'];
    $API = new RouterosAPI();
    if ($API->connect($host, $login, $password, $port, $timeout)) {
        $API->comm("/ip/hotspot/user/add", array(
            "name" => $username,
            "password" => $user_password, // Gunakan variabel yang benar
            "profile" => $profile,
        ));

        echo 'Voucher Created: ' . $username . '/' . $user_password;

        $API->disconnect();
    } else {
        echo 'Not Connected';
    }
}
