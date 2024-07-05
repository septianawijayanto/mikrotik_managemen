<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');

function deleteVouchers($kodeVoucher)
{
    global $host, $login, $password, $port, $timeout, $conn;

    // Ambil data dari database berdasarkan kode
    $stmt = $conn->prepare("SELECT name FROM tm_user WHERE kode = ?");
    if (!$stmt) {
        echo "<script>alert('Failed to prepare statement: " . $conn->error . "');</script>";
        return false;
    }
    $stmt->bind_param("s", $kodeVoucher);
    $stmt->execute();
    $stmt->bind_result($username);

    $usernames = array();
    while ($stmt->fetch()) {
        $usernames[] = $username;
    }
    $stmt->close();

    if (empty($usernames)) {
        echo "<script>alert('No users found in database for provided code');</script>";
        return false;
    }

    $API = new RouterosAPI();

    if ($API->connect($host, $login, $password, $port, $timeout)) {
        $success = true;
        foreach ($usernames as $username) {
            // Hapus pengguna di MikroTik berdasarkan nama
            $user = $API->comm("/ip/hotspot/user/print", array(
                "?name" => $username
            ));

            if (!empty($user)) {
                // Hapus pengguna di MikroTik berdasarkan ID
                $API->comm("/ip/hotspot/user/remove", array(
                    ".id" => $user[0]['.id']
                ));
            } else {
                echo "<script>alert('User $username not found in MikroTik');</script>";
                $success = false;
            }
        }

        $API->disconnect();

        if ($success) {
            // Hapus semua entri dari database berdasarkan kode
            $stmt = $conn->prepare("DELETE FROM tm_user WHERE kode = ?");
            if (!$stmt) {
                echo "<script>alert('Failed to prepare statement: " . $conn->error . "');</script>";
                return false;
            }
            $stmt->bind_param("s", $kodeVoucher);

            if ($stmt->execute()) {
                $stmt->close();
                echo "<script>alert('Vouchers deleted from database');</script>";
                return true;
            } else {
                echo "<script>alert('Failed to delete vouchers from database: " . $stmt->error . "');</script>";
                $stmt->close();
                return false;
            }
        } else {
            echo "<script>alert('Failed to delete all users from MikroTik');</script>";
            return false;
        }
    } else {
        echo "<script>alert('Not Connected to MikroTik');</script>";
        return false;
    }
}

if (isset($_GET['kode'])) {
    $kodeVoucher = $_GET['kode'];
    if (deleteVouchers($kodeVoucher)) {
        echo "<script>alert('Vouchers Deleted: $kodeVoucher');</script>";
    } else {
        echo "<script>alert('Failed to delete vouchers: $kodeVoucher');</script>";
    }
} else {
    echo "<script>alert('No voucher code provided.');</script>";
}
