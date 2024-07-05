<?php
$server = "localhost";
$username = "root";
$password = "gts0ulit";
$db_name = "db_mikrotik";
$conn = mysqli_connect($server, $username, $password, $db_name);

// mengecek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}


$tanggal = date("ymd"); // Tahun, bulan, dan hari dalam format yyMMdd
$waktu = date("His"); // Jam, menit, dan detik dalam format His
// Menggabungkan teks, tanggal, dan waktu
$kodeVoucher = "KV" . $tanggal . $waktu;
$kodeUser = "KU" . $tanggal . $waktu;

function convertDurationToDescription($duration)
{
    $description = '';

    // Memeriksa dan mengonversi berbagai format durasi
    if (preg_match('/(\d+)w(\d+)d/', $duration, $matches)) {
        // Mengambil jumlah minggu dan hari
        $weeks = intval($matches[1]);
        $days = intval($matches[2]);
        // Mengonversi ke deskripsi
        $description = "$weeks minggu $days hari";
    } elseif (preg_match('/(\d+)w/', $duration, $matches)) {
        // Mengambil jumlah minggu
        $weeks = intval($matches[1]);
        // Mengonversi ke deskripsi
        $description = "$weeks minggu";
    } elseif (preg_match('/(\d+)d/', $duration, $matches)) {
        // Mengambil jumlah hari
        $days = intval($matches[1]);
        // Mengonversi ke deskripsi
        $description = "$days hari";
    } elseif (preg_match('/(\d+)h/', $duration, $matches)) {
        // Mengambil jumlah jam
        $hours = intval($matches[1]);
        // Mengonversi ke deskripsi
        $description = "$hours jam";
    } elseif (preg_match('/(\d+)m/', $duration, $matches)) {
        // Mengambil jumlah bulan
        $months = intval($matches[1]);
        // Mengonversi ke deskripsi
        $description = "$months bulan";
    } else {
        $description = 'Format waktu tidak valid';
    }

    return $description;
}



