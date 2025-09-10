<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$host = $_GET['host'] ?? 'error';

if ($host === 'error' || $host === '') {

    // Отправляем статус код 400
    http_response_code(400);



    echo json_encode([
        'status' => 'error',
        'output' => 'error',
        'packets_sent' => 0,
        'packets_received' => 0,
        'packet_loss' => 'N/A',
        'latency_avg_ms' => null
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$command = 'ping -n 1 ' . escapeshellarg($host) . ' 2>&1';

$output = shell_exec($command);
$output_utf8 = iconv('CP866', 'UTF-8', $output);

preg_match('/отправлено = (\d+), получено = (\d+), потеряно = (\d+)/u', $output_utf8, $packets);
preg_match('/\((\d+%) потерь\)/u', $output_utf8, $loss);
preg_match('/Среднее = (\d+)\s*мсек/u', $output_utf8, $avg);

$response = [
    'status' => $packets && $packets[3] == 0 ? 'success' : 'error',
    'output' => $output_utf8,
    'packets_sent' => (int)($packets[1] ?? 0),
    'packets_received' => (int)($packets[2] ?? 0),
    'packet_loss' => $loss[1] ?? 'N/A',
    'latency_avg_ms' => isset($avg[1]) ? (int)$avg[1] : null
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
exit;