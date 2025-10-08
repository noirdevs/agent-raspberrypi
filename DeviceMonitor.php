<?php
// DeviceMonitor.php

declare(strict_types=1);

class DeviceMonitor
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function collectAndSendData(): bool
    {
        $payload = $this->collectMetrics();
        echo "Data collected. Sending to Supabase...\n";
        $success = $this->sendData($payload);

        if ($success) {
            echo "Data successfully sent.\n";
        } else {
            echo "Failed to send data.\n";
        }

        return $success;
    }

    private function collectMetrics(): array
    {
        $uptime = $this->getUptime();
        return [
            "devicename" => $this->config['deviceId'],
            "latency"    => $this->getLatency($this->config['latencyHost']),
            "uptime1"    => $uptime['boot_time'],
            "uptime2"    => $uptime['duration'],
            "memory"     => $this->getAvailableMemory(),
            "disk"       => $this->getAvailableDisk(),
            "dc"         => $this->getTaxServerStatus($this->config['taxServerHost'], (int)$this->config['taxServerPort']),
            "pptp"       => $this->getInterfaceStatus('ppp1'),
            "ovpn"       => $this->getInterfaceStatus('tun0'),
        ];
    }

    private function sendData(array $payload): bool
    {
        $jsonPayload = json_encode($payload);
        $headers = [
            'Content-Type: application/json',
            'apikey: ' . $this->config['supabaseServiceKey'],
            'Authorization: Bearer ' . $this->config['supabaseServiceKey']
        ];

        $ch = curl_init($this->config['supabaseUrl']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            echo 'cURL Error: ' . curl_error($ch) . "\n";
        }
        
        echo "HTTP Response Code from Supabase: " . $httpCode . "\n";
        curl_close($ch);

        return $httpCode === 201;
    }

    private function getLatency(string $host): float
    {
        $cmd = "ping -c 1 " . escapeshellarg($host) . " | tail -1 | awk '{print $4}' | cut -d '/' -f 2";
        $latency = trim((string) shell_exec($cmd));
        return (float) ($latency ?: 0.0);
    }

    private function getUptime(): array
    {
        return [
            'boot_time' => trim((string) shell_exec('uptime -s')),
            'duration'  => trim((string) shell_exec('uptime -p')),
        ];
    }

    private function getAvailableMemory(): float
    {
        $cmd = "free -m | awk '/Mem:/ {printf \"%.2f\", $7/$2 * 100.0}'";
        $memory = trim((string) shell_exec($cmd));
        return (float) ($memory ?: 0.0);
    }

    private function getAvailableDisk(): string
    {
        $cmd = "df -h / | awk 'NR==2 {print $4}'";
        return trim((string) shell_exec($cmd)) ?: '0B';
    }

    private function getTaxServerStatus(string $host, int $port): string
    {
        $connection = @fsockopen($host, $port, $errno, $errstr, 2);
        if (is_resource($connection)) {
            fclose($connection);
            return 'CONNECTED';
        }
        return 'DISCONNECTED';
    }

    private function getInterfaceStatus(string $interface): string
    {
        $result = shell_exec("ip a | grep " . escapeshellarg($interface));
        return empty(trim((string) $result)) ? 'DOWN' : 'UP';
    }
}
