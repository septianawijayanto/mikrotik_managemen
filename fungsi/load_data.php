<?php
// Load library RouterOS PHP API
require('../config/device.php');
require('../config/config.php');
require('../routeros_api.class.php');

// Fungsi untuk memformat bytes ke unit yang lebih besar (KB, MB, GB, dsb.)
function formatBytes($bytes, $precision = 2)
{
    if (!is_numeric($bytes)) {
        return "Invalid data";
    }

    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= (1 << (10 * $pow));

    return round($bytes, $precision) . ' ' . $units[$pow];
}
// Fungsi untuk memformat bits per second
function formatBitsPerSecond($bits)
{
    if (!is_numeric($bits)) {
        return "Invalid data";
    }

    $units = array('bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps');

    $bits = max($bits, 0);
    $pow = floor(($bits ? log($bits) : 0) / log(1000));
    $pow = min($pow, count($units) - 1);

    $bits /= pow(1000, $pow);

    return round($bits, 2) . ' ' . $units[$pow];
}
// Buat objek RouterosAPI
$API = new RouterosAPI();
$API->debug = false;

// Coba melakukan koneksi
if ($API->connect($host, $login, $password, $port, $timeout)) {
    // Sukses terhubung, dapatkan informasi traffic
    $API->write('/interface/monitor-traffic', false);
    $API->write('=once=');

    // Baca hasil respons dari Mikrotik
    $READ = $API->read(false);
    $ARRAY = $API->parseResponse($READ);

    // Tampilkan informasi trafik
    if (!empty($ARRAY)) {
        foreach ($ARRAY as $item) {
            echo "Received Bits Per Second: " . $item['rx-bits-per-second'] . "<br>";
            echo "Transmitted Bits Per Second: " . $item['tx-bits-per-second'] . "<br>";
            echo "Received Packets Per Second: " . $item['rx-packets-per-second'] . "<br>";
            echo "Transmitted Packets Per Second: " . $item['tx-packets-per-second'] . "<br>";
            echo "<br>";
        }
    } else {
        echo "Tidak ada data traffic yang ditemukan.";
    }
    // Informasi siapa saja yang terhubung
    echo "<h2>Pengguna yang Terhubung melalui DHCP</h2>";

    $API->write('/ip/dhcp-server/lease/print');
    $leases = $API->read();

    // Proses data yang diperoleh dari DHCP leases
    if (!empty($leases)) {
        echo "<table border='1'>";
        echo "<tr><th>IP Address</th><th>MAC Address</th><th>Hostname</th><th>Server</th><th>Status</th></tr>";
        foreach ($leases as $lease) {
            $ip_address = $lease['address'];
            $mac_address = $lease['mac-address'];
            $hostname = $lease['host-name'];
            // $interface = $lease['interface'];
            $server = $lease['server'];
            $status = $lease['status'];

            echo "<tr>";
            echo "<td>$ip_address</td>";
            echo "<td>$mac_address</td>";
            echo "<td>$hostname</td>";
            // echo "<td>$interface</td>";
            echo "<td>$server</td>";
            echo "<td>$status</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Tidak ada pengguna yang terhubung melalui DHCP.";
    }
    // Sukses terhubung, dapatkan informasi trafik dari Simple Queue
    echo "<h2>Informasi Trafik dari Simple Queue</h2>";

    $API->write('/queue/simple/print');
    $queues = $API->read();

    // Proses data yang diperoleh dari Simple Queue
    if (!empty($queues)) {
        echo "<table border='1'>";
        echo "<tr><th>Target</th><th>Bytes Uploaded</th><th>Bytes Downloaded</th><th>Total Bytes</th></tr>";
        foreach ($queues as $queue) {
            $target = isset($queue['target']) ? $queue['target'] : "Unknown";
            $bytes = isset($queue['bytes']) ? $queue['bytes'] : "0/0";
            $totalBytes = isset($queue['total-bytes']) ? $queue['total-bytes'] : 0;

            // Split bytes uploaded and downloaded
            list($bytesUploaded, $bytesDownloaded) = explode('/', $bytes);

            $formattedUploaded = formatBytes($bytesUploaded);
            $formattedDownloaded = formatBytes($bytesDownloaded);
            $formattedTotal = formatBytes($totalBytes);

            echo "<tr>";
            echo "<td>$target</td>";
            echo "<td>$formattedUploaded</td>";
            echo "<td>$formattedDownloaded</td>";
            echo "<td>$formattedTotal</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Tidak ada data Simple Queue yang ditemukan.";
    }
    // Dapatkan daftar semua interface
    $API->write('/interface/print');
    $interfaces = $API->read();

    echo "<h2>Informasi Trafik dari Semua Interface</h2>";

    echo "<table border='1'>";
    echo "<tr><th>Interface</th><th>Received Bits Per Second</th><th>Transmitted Bits Per Second</th></tr>";

    // Iterasi setiap interface untuk mendapatkan trafiknya
    foreach ($interfaces as $interface) {
        $interfaceName = $interface['name'];

        $API->write('/interface/monitor-traffic', false);
        $API->write('=interface=' . $interfaceName, false);
        $API->write('=once=', false);
        $API->write('=interval=1');

        // Baca hasil respons dari Mikrotik
        $READ = $API->read(false);
        $ARRAY = $API->parseResponse($READ);

        // Tampilkan informasi trafik
        if (!empty($ARRAY)) {
            foreach ($ARRAY as $item) {
                echo "<tr>";
                echo "<td>{$interfaceName}</td>";
                echo "<td>" . formatBitsPerSecond($item['rx-bits-per-second']) . "</td>";
                echo "<td>" . formatBitsPerSecond($item['tx-bits-per-second']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr>";
            echo "<td>{$interfaceName}</td>";
            echo "<td colspan='2'>Tidak ada data traffic yang ditemukan.</td>";
            echo "</tr>";
        }
    }

    echo "</table>";

    // Tutup koneksi
    $API->disconnect();
} else {
    echo "Tidak dapat terhubung ke Mikrotik.";
}
