<?php
require_once __DIR__ . '/DeviceMonitor.php';

$config = require __DIR__ . '/config.php';

$monitor = new DeviceMonitor($config);
$monitor->collectAndSendData();

echo "Agent finished running at " . date('Y-m-d H:i:s') . "\n";
