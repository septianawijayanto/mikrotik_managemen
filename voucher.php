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
    </script>
</head>

<body>
    <form action="post/voucher.php" method="post">
        Profile:
        <select name="profile" id="profile" onchange="updateLimitUptime()" required>
            <option value="">Select Profile</option>
            <?php foreach ($profiles as $profile) : ?>
                <option value="<?php echo htmlspecialchars($profile['name']); ?>">
                    <?php echo htmlspecialchars($profile['name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        Number of Vouchers: <input type="number" name="voucher_count" min="1" required><br>
        Limit Uptime: <input type="text" id="limit-uptime" name="limit_uptime" readonly><br>
        <input type="submit" value="Generate Vouchers">
    </form>
</body>

</html>