<?php
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

<!DOCTYPE html>
<html>
<head>
    <title>Generate Voucher</title>
</head>
<body>
    <form action="post/user.php" method="post">
        Username: <input type="text" name="username" required><br>
        Password: <input type="text" name="password" required><br>
        Profile: 
        <select name="profile" required>
            <?php foreach ($profiles as $profile): ?>
                <option value="<?php echo htmlspecialchars($profile['name']); ?>">
                    <?php echo htmlspecialchars($profile['name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <input type="submit" value="Create Password">
    </form>
</body>
</html>
