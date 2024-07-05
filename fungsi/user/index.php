<?php
require('../../config/device.php');
require('../../config/config.php');
include('../../routeros_api.class.php');


echo '    <table style="border-collapse:collapse;" class="table table-bordered" >
<tr style="background:#cccccc;">
    <td class="headtbl">No.</td>
    <td class="headtbl">Name</td>
    <td class="headtbl">Password</td>
    <td class="headtbl">Profile</td>

    <td class="headtbl">Time Limit</td>
    <td class="headtbl">Action</td>
</tr>';

if (isset($_POST['cari'])) {
    $cari = $_POST['cari'];
    $datas = mysqli_query($conn, "SELECT * FROM tm_user WHERE  name LIKE '%$cari%'
    OR  password LIKE '%$cari%' AND type='user'");
} else {
    $datas = mysqli_query($conn, "SELECT * FROM tm_user WHERE type='user' ORDER BY created_at DESC");
}
if (mysqli_num_rows($datas) > 0) {
    $i = 1;

    while ($row = mysqli_fetch_assoc($datas)) :
        echo '  <tr>
                    <td>' . $i . '</td>
                    <td>' . $row["name"] . '</td>
                    <td>' . $row["password"] . '</td>
                    <td>' . $row["profile"] . '</td>
                    <td>' . $row["time_limit"] . '</td>
                    <td>
                        <a href="fungsi/user/update.php?id="' . $row["id"] . '"
                        id="' . $row["id"] . '" 
                        name="' . $row["name"] . '" 
                        password="' . $row["password"] . '" 
                        profile="' . $row["profile"] . '" 
                        time_limit="' . $row["time_limit"] . '" class="editData btn btn-warning btn-xs"><i class="fas fa-pencil-alt"></i></a>
                        <a  href="fungsi/user/delete.php?id=' . $row["id"] . '"  class="hapusData delete btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
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
