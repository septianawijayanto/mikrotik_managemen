<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');


echo '    <table style="border-collapse:collapse;" class="table table-bordered" >
<tr style="background:#cccccc;">
    <td class="headtbl">No.</td>
    <td class="headtbl">Kode</td>
    <td class="headtbl">Profile</td>
    <td class="headtbl">Time Limit</td>
    <td class="headtbl">Action</td>
</tr>';

if (isset($_POST['cari'])) {
    $cari = $_POST['cari'];
    $datas = mysqli_query($conn, "SELECT * FROM tm_user WHERE name LIKE '%$cari%'
    OR  password LIKE '%$cari%' AND type='voucher'");
} else {
    $datas = mysqli_query($conn, "SELECT * FROM tm_user WHERE type='voucher' Group BY kode ORDER BY created_at DESC");
}
if (mysqli_num_rows($datas) > 0) {
    $i = 1;

    while ($row = mysqli_fetch_assoc($datas)) :
        echo '  <tr>
                    <td>' . $i . '</td>
                    <td>' . $row["kode"] . '</td>
                    <td>' . $row["profile"] . '</td>
                    <td>' . $row["time_limit"] . '</td>
                    <td>
                    <a  href="fungsi/voucher/cetak.php?kode=' . $row["kode"] . '"  class="cetakData delete btn btn-warning btn-xs"><i class="fa fa-print"></i></a>
                        <a  href="fungsi/voucher/delete.php?kode=' . $row["kode"] . '"  class="hapusData delete btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
                    </td>
            </tr>';

        $i++;

    endwhile;
} else {
    echo
    '<tr>
			<td colspan="6" style="text-align: center">Data tidak ada</td>
		</tr>';
}
echo '</table>';
