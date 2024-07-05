<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');


echo '    <table style="border-collapse:collapse;" class="table table-bordered" >
<tr style="background:#cccccc;">
    <td class="headtbl">No.</td>
    <td class="headtbl">Profile Name</td>
    <td class="headtbl">Shared Users</td>
    <td class="headtbl">Rate Limit</td>
    <td class="headtbl">Session Timeout</td>
    <td class="headtbl">Action</td>
</tr>';

if (isset($_POST['cari'])) {
    $cari = $_POST['cari'];
    $datas = mysqli_query($conn, "SELECT * FROM tm_profil WHERE   profile_name LIKE '%$cari%'
    OR  shared_users LIKE '%$cari%'");
} else {
    $datas = mysqli_query($conn, "SELECT * FROM tm_profil ORDER BY created_at DESC");
}
if (mysqli_num_rows($datas) > 0) {
    $i = 1;

    while ($row = mysqli_fetch_assoc($datas)) :
        echo '  <tr>
                    <td>' . $i . '</td>
                    <td>' . $row["profile_name"] . '</td>
                    <td>' . $row["shared_users"] . '</td>
                    <td>' . $row["rate_limit"] . '</td>
                    <td>' . $row["session_timeout"] . '</td>
                    <td>
                        <a href="fungsi/profil/update.php?id="' . $row["id"] . '"
                        id="' . $row["id"] . '" 
                        profile_name="' . $row["profile_name"] . '" 
                        shared_users="' . $row["shared_users"] . '" 
                        rate_limit="' . $row["rate_limit"] . '" 
                        session_timeout="' . $row["session_timeout"] . '" class="editData btn btn-warning btn-xs"><i class="fas fa-pencil-alt"></i></a>
                        <a  href="fungsi/profil/delete.php?id=' . $row["id"] . '"  class="hapusData delete btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
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
