<?php

$title = 'User Hotspot'; // judul halaman

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
                <div style="width:100%;border-bottom:1px solid #999;padding-bottom:5px;margin-bottom:5px;"><b> <?php echo $title; ?></b></div>
                <button type="button" class="btn btn-primary" id="tombol_tambah" data-toggle="modal" data-target="#exampleModal">
                    <span class="fa fa-plus"></span>
                </button>

            </div>
            <div class="card-body">
                <!-- ini konten -->
                <div class="table-responsive" id="konten_user">
                </div>
                <!-- end konten -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>
<div class="modal fade" id="modal_tambah" tabindex="-1" role="dialog" aria-labelledby="modal_tambah_judul" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_tambah_judul"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_golongan" action="fungsi/user/create.php" method="POST">

                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="old_username" name="old_username">
                    <div class="form-group">
                        <label for=" Username">Username</label>
                        <input class="form-control" type="text" required id="username" name="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input class="form-control" type="text" required id="password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="profile"> Profile</label>
                        <select class="form-control" id="profile" name="profile" onchange="updateLimitUptime()" required>
                            <?php foreach ($profiles as $profile) : ?>
                                <option class="form-control" value="<?php echo htmlspecialchars($profile['name']); ?>">
                                    <?php echo htmlspecialchars($profile['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" id="limit-uptime" name="limit_uptime" readonly><br>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal" data-toggle="tooltip" data-placement="top" title="Klik disini untuk menutup form">Tutup</button>
                        <button type="submit" class="btn btn-primary" id="tombol_simpan" data-toggle="tooltip" data-placement="top" title="Klik disini untuk menyimpan data">Save changes</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
<?php

include 'layouts/script.php'; // memanggil file layout/footer.php
?>
<script>
    function updateLimitUptime() {
        const profiles = <?php echo json_encode($profiles); ?>;
        const selectedProfile = document.getElementById("profile").value;
        const limitUptimeInput = document.getElementById("limit-uptime");

        for (let i = 0; i < profiles.length; i++) {
            if (profiles[i].name === selectedProfile) {
                limitUptimeInput.value = profiles[i]["session-timeout"] || '1d'; // Default to 1 day if not set
                break;
            }
        }
    }
    $(document).ready(function() {

        $('#tombol_tambah').click(function(e) {
            e.preventDefault();
            $('#tombol_simpan').val('create-post');
            $('#tombol_simpan').html('Simpan');
            $('#id').val('');
            $('#modal_tambah').trigger('reset');
            $('#modal_tambah_judul').html('Create User Hotspot');
            $('#modal_tambah').modal('show');
        });
        loadData();
        $('form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: $(this).serialize(),
                success: function(response) {
                    alert(response);
                    loadData();
                    resetForm();
                }
            });
        })

    });

    function loadData() {
        $.get('fungsi/user/index.php', function(data) {
            $('#konten_user').html(data)

            $(".hapusData").click(function(e) {
                e.preventDefault();
                var yes = confirm('apakah anda yakin akan menghapus data ini?');
                if (yes) {
                    $.ajax({
                        type: 'get',
                        url: $(this).attr('href'),
                        success: function(response) {
                            alert(response);
                            loadData();
                        }
                    });
                }

            });

            $(".editData").click(function(e) {
                e.preventDefault();
                $('#tombol_simpan').val('create-post');
                $('#tombol_simpan').html('Update');
                $('#modal_tambah').trigger('reset');
                $('#modal_tambah_judul').html('Edit User Hostpot');
                $('#modal_tambah').modal('show');
                $('[name=id]').val($(this).attr('id'));
                $('[name= username]').val($(this).attr('name'));
                $('[name= old_username]').val($(this).attr('name'));
                $('[name=password]').val($(this).attr('password'));
                $('[name=profile]').val($(this).attr('profile'));
                $('[name=time_limit]').val($(this).attr('time_limit'));
                $('form').attr('action', $(this).attr('href'));
            })
        });
    }

    function resetForm() {
        $('[type=text]').val('');
        $('[name= username]').focus();
        $('#modal_tambah').modal('hide');
        $('form').attr('action', 'fungsi/user/create.php');
    }
    $('#menu_hotspot').addClass('active');
    $('#li_hotspot').addClass('menu-open');
    $('#sub_user').addClass('active');
</script>