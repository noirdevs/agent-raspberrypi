<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Susun array konfigurasi dari variabel .env
$config = [
    'supabaseUrl'        => $_ENV['SUPABASE_URL'],
    'supabaseServiceKey' => $_ENV['SUPABASE_SERVICE_KEY'],
    'deviceId'           => $_ENV['DEVICE_ID'],
    'region'             => $_ENV['DEVICE_REGION'],
    'province'           => $_ENV['DEVICE_PROVINCE'],
    'taxServerHost'      => $_ENV['TAX_SERVER_HOST'],
    'taxServerPort'      => $_ENV['TAX_SERVER_PORT'],
    'latencyHost'        => $_ENV['LATENCY_CHECK_HOST'],
];

$monitor = new DeviceMonitor($config);

$monitor->collectAndSendData();

echo "Agent finished running at " . date('Y-m-d H:i:s') . "\n";
?>
