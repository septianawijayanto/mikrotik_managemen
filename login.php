<?php
require 'config/config.php';
session_start();
if (isset($_SESSION["login"])) {
	header("Location:index.php");
	exit;
}

$msg = 'none';
if (!empty($_POST['username']) && !empty($_POST['password'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	$qstr = "SELECT * FROM user WHERE username='$username' AND password='$password'";
	$query = mysqli_query($conn, $qstr);
	// $ini = "SELECT * FROM inisialisasi WHERE status='aktif' ";
	// $iquery = mysqli_query($conn, $ini);

	$row = mysqli_num_rows($query);
	if ($row < 1) {
		$msg = 'Gagal';
	} else {
		$data = mysqli_fetch_assoc($query);
		// $indata = mysqli_fetch_assoc($iquery);

		$_SESSION['login'] = true;
		$_SESSION['nama'] = $data["nama"];
		$_SESSION['username'] = $data["username"];
		$_SESSION['level'] = $data["level"];


		// $_SESSION['ppn'] = $indata["ppn"];
		// $_SESSION['namaperusahaan'] = $indata["nama"];
		// $_SESSION['kode_cabang'] = $indata["kode_cabang"];
		// $_SESSION['alamat'] = $indata["alamat"];
		// $_SESSION['status'] = $indata["status"];
		header("location:index.php");
		exit;
	}
}
?><html>

<head>
	<title>Login</title>
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="adminlte/plugins/fontawesome-free/css/all.min.css">
	<!-- icheck bootstrap -->
	<link rel="stylesheet" href="adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="adminlte/dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page" onload="document.getElementById('uname').focus();">
	<div class="login-box">
		<!-- /.login-logo -->
		<div class="card card-outline card-primary">
			<div class="card-header text-center">
				<a href="adminlte/index2.html" class="h1"><b>Mikrotik</b> Managjemen</a>
			</div>
			<div class="card-body">
				<p class="login-box-msg">Sign in to start your session</p>

				<form name="login" method="post">
					<div class="input-group mb-3">
						<input type="text" name="username" id="uname" class="form-control" placeholder="Username">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-envelope"></span>
							</div>
						</div>
					</div>
					<div class="input-group mb-3">
						<input type="password" name="password" class="form-control" placeholder="Password">
						<div class="input-group-append">
							<div class="input-group-text">
								<span class="fas fa-lock"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-8">
							<div class="icheck-primary">
								<input type="checkbox" id="remember">
								<label for="remember">
									Remember Me
								</label>
							</div>
						</div>
						<!-- /.col -->
						<div class="col-4">
							<button type="submit" class="btn btn-primary btn-block">Sign In</button>
						</div>
						<!-- /.col -->
					</div>
					<p colspan=2 style="color:#ff0000;font-family:arial;"><?php echo ($msg == 'none') ? '' : 'Username atau password Anda salah!!'; ?></p>
				</form>


			</div>
			<!-- /.card-body -->
		</div>
		<!-- /.card -->
	</div>
	<!-- /.login-box -->

	<!-- jQuery -->
	<script src="adminlte/plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- AdminLTE App -->
	<script src="adminlte/dist/js/adminlte.min.js"></script>
</body>

</html>