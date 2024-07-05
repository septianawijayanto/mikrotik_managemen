<?php
require('../../config/device.php');
require('../../config/config.php');

function fetchVouchers($kodeVoucher)
{
    global $conn;

    // Ambil data voucher dari database berdasarkan kode
    $stmt = $conn->prepare("SELECT name, password, time_limit FROM tm_user WHERE kode = ?");
    if (!$stmt) {
        die("Failed to prepare statement: " . $conn->error);
    }
    $stmt->bind_param("s", $kodeVoucher);
    $stmt->execute();
    $result = $stmt->get_result();
    $vouchers = array();
    while ($row = $result->fetch_assoc()) {
        $vouchers[] = $row;
    }
    $stmt->close();

    return $vouchers;
}

if (isset($_GET['kode'])) {
    $kodeVoucher = $_GET['kode'];
    $vouchers = fetchVouchers($kodeVoucher);
} else {
    echo "<script>alert('No voucher code provided.');</script>";
    exit;
}

// Contoh penggunaan


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Voucher Printing</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .voucher-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .voucher {
            width: 23%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        .voucher h3 {
            margin-top: 0;
        }

        .clearfix {
            clear: both;
        }

        .print-button {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="voucher-container">

        <?php
        if (!empty($vouchers)) {
            foreach ($vouchers as $voucher) {
                echo '<div class="voucher">';
                echo '<h3>Voucher</h3>';
                echo '<p><strong>Kode Voucher:</strong> ' . htmlspecialchars($voucher['name']) . '</p>';
                // echo '<p><strong>Password:</strong> ' . htmlspecialchars($voucher['password']) . '</p>';
                echo '<p><strong>Durasi:</strong> ' . htmlspecialchars(convertDurationToDescription($voucher['time_limit'])) . '</p>';
                echo '</div>';
            }
        } else {
            echo '<p>No vouchers found for provided code.</p>';
        }
        ?>

    </div>

    <div class="print-button">
        <button onclick="window.print()">Print</button>
    </div>

</body>

</html>