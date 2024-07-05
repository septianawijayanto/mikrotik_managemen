<?php

$title = 'Index'; // judul halaman

include 'layouts/header.php'; // memanggil file layout/header.php

require('config/device.php');
require('config/config.php');
include('routeros_api.class.php');
$API = new RouterosAPI();
$profiles = [];

if ($API->connect($host, $login, $password, $port, $timeout)) {
    $profiles = $API->comm("/ip/hotspot/user/profile/print");
    $API->disconnect();
}
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
               <!-- ini konten -->

               <!-- end konten -->
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>

<?php

include 'layouts/script.php'; // memanggil file layout/footer.php
?>
<script>
   
</script>