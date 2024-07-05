<?php

// session_start();
// if (!isset($_SESSION["login"])) {
//     header("Location:login.php");
//     exit;
// }
$title = 'Index'; // judul halaman

include 'layouts/header.php'; // memanggil file layout/header.php


?>

<div class="row">
    <!-- left column -->
    <div class="col-md-12">
        <!-- jquery validation -->
        <div class="card card-navy">
            <div class="card-header">
                <div style="width:100%;border-bottom:1px solid #999;padding-bottom:5px;margin-bottom:5px;"><b>Dashboard <?php echo $_SESSION["username"]; ?></b></div>


            </div>
            <div class="card-body">
                <div id="dhcp-leases">
                    <!-- Data DHCP Leases akan dimuat di sini -->
                </div>
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>

<?php

include 'layouts/script.php'; // memanggil file layout/footer.php
?>
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
        $('#dashboard').addClass('active');
    });
</script>