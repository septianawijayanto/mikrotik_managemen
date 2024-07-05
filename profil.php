<?php

$title = 'User Profil'; // judul halaman

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
                <div style="width:100%;border-bottom:1px solid #999;padding-bottom:5px;margin-bottom:5px;"><b><?php echo $title; ?></b></div>
                <button type="button" class="btn btn-primary" id="tombol_tambah" data-toggle="modal" data-target="#exampleModal">
                    <span class="fa fa-plus"></span>
                </button>

            </div>
            <div class="card-body">
                <!-- ini konten -->
                <div class="table-responsive" id="konten_profil">
                    <!-- end konten -->
                </div>
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
                <form id="form_golongan" action="fungsi/profil/create.php" method="POST">

                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="old_profile_name" name="old_profile_name">
                    <div class="form-group">
                        <label for="profil_name">Profile Name</label>
                        <input class="form-control" type="text" required id="profile_name" name="profile_name">
                    </div>
                    <div class="form-group">
                        <label for="shared_users">Shared Users</label>
                        <input class="form-control" type="text" required id="shared_users" name="shared_users">
                    </div>

                    <div class="form-group">
                        <label for="rate_limit">Rate Limit (e.g., 1M/1M)</label>
                        <input class="form-control" type="text" required id="rate_limit" name="rate_limit">
                    </div>

                    <div class="form-group">
                        <label for="session_timeout"> Session Timeout (e.g., 1h)</label>

                        <input class="form-control" type="text" required id="session_timeout" name="session_timeout">
                    </div>
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
    $(document).ready(function() {

        $('#tombol_tambah').click(function(e) {
            e.preventDefault();
            $('#tombol_simpan').val('create-post');
            $('#tombol_simpan').html('Simpan');
            $('#id').val('');
            $('#modal_tambah').trigger('reset');
            $('#modal_tambah_judul').html('Create User Profile');
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
        // $('form').on('reset', function(e) {
        //     e.preventDefault();
        //     loadData();
        //     resetForm();
        // });
        // $('#cari').on('keyup', function() {
        //     $.ajax({
        //         type: 'POST',
        //         url: 'ajax/golongan/index.php',
        //         data: {
        //             cari: $(this).val()
        //         },
        //         cache: false,
        //         success: function(data) {
        //             $('#konten_profil').html(data);
        //         }
        //     });
        // });
    });

    function loadData() {
        $.get('fungsi/profil/index.php', function(data) {
            $('#konten_profil').html(data)

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
                $('#modal_tambah_judul').html('Edit User Profile');
                $('#modal_tambah').modal('show');
                $('[name=id]').val($(this).attr('id'));
                $('[name=profile_name]').val($(this).attr('profile_name'));
                $('[name=old_profile_name]').val($(this).attr('profile_name'));
                $('[name=shared_users]').val($(this).attr('shared_users'));
                $('[name=rate_limit]').val($(this).attr('rate_limit'));
                $('[name=session_timeout]').val($(this).attr('session_timeout'));
                $('form').attr('action', $(this).attr('href'));
            })
        });
    }

    function resetForm() {
        $('[type=text]').val('');
        $('[name=profile_name]').focus();
        $('#modal_tambah').modal('hide');
        $('form').attr('action', 'fungsi/profil/create.php');
    }
    $('#menu_hotspot').addClass('active');
    $('#li_hotspot').addClass('menu-open');
    $('#sub_profil').addClass('active');
</script>