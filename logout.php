<?php
session_start();
unset($_SESSION['username']);
unset($_SESSION['login']);
unset($_SESSION['level']);
session_unset();
session_destroy();
header("Location:login.php");
