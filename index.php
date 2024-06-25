<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Auto Update DHCP Leases</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fungsi untuk memuat data secara berkala
            function loadData() {
                $.ajax({
                    url: 'fungsi/load_data.php', // Ubah sesuai dengan nama file PHP yang Anda gunakan
                    success: function(data) {
                        $('#dhcp-leases').html(data); // Menyisipkan data ke dalam elemen dengan ID 'dhcp-leases'
                    },
                    complete: function() {
                        // Set interval untuk memuat data setiap beberapa detik
                        setTimeout(loadData, 1000); // Misalnya, setiap 5 detik sekali (5000 milidetik)
                    }
                });
            }

            // Memulai pemanggilan fungsi loadData untuk pertama kali
            loadData();
        });
    </script>
</head>

<body>
    <div id="dhcp-leases">
        <!-- Data DHCP Leases akan dimuat di sini -->
    </div>
</body>

</html>