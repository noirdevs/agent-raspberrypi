<?php
declare(strict_types=1);

class DeviceMonitor
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function collectAndSendData(): bool
    {
        $payload = $this->collectMetrics();
        return $this->sendData($payload);
    }

    private function collectMetrics(): array
    {
        $uptime = $this->getUptime();
        return [
            "DEVICENAME" => $this->config['deviceId'],
            "LATENCY"    => $this->getLatency($this->config['latencyHost']),
            "TANGGAL"    => date('Y-m-d H:i:s'),
            "UPTIME1"    => $uptime['boot_time'],
            "UPTIME2"    => $uptime['duration'],
            "MEMORY"     => $this->getAvailableMemory(),
            "DISK"       => $this->getAvailableDisk(),
            "DC"         => $this->getTaxServerStatus($this->config['taxServerHost'], (int)$this->config['taxServerPort']),
            "PPTP"       => $this->getInterfaceStatus('ppp1'),
            "OVPN"       => $this->getInterfaceStatus('tun0'),
            "WILAYAH"    => $this->config['region'],
            "PROVINSI"   => $this->config['province'],
        ];
    }

    private function sendData(array $payload): bool
    {
        $dataString = json_encode($payload);
        $ch = curl_init($this->config['supabaseUrl']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'apikey: ' . $this->config['supabaseServiceKey'],
            'Authorization: Bearer ' . $this->config['supabaseServiceKey']
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $httpCode === 201;
    }

    private function getLatency(string $host): string
    {
        $cmd = "ping -c 1 " . escapeshellarg($host) . " | tail -1 | awk '{print $4}' | cut -d '/' -f 2";
        return trim(shell_exec($cmd) ?: '0.000') . ' ms';
    }

    private function getUptime(): array
    {
        return [
            'boot_time' => trim(shell_exec('uptime -s') ?: ''),
            'duration'  => trim(shell_exec('uptime -p') ?: ''),
        ];
    }

    private function getAvailableMemory(): string
    {
        $cmd = "free -m | awk '/Mem:/ {printf \"%.2f\", $7/$2 * 100.0}'";
        return trim(shell_exec($cmd) ?: '0.00') . ' %';
    }

    private function getAvailableDisk(): string
    {
        $cmd = "df -h / | awk 'NR==2 {print $4}'";
        return trim(shell_exec($cmd) ?: '0B');
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
        return empty(trim($result)) ? 'DOWN' : 'UP';
    }
}
