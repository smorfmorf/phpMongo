<?php
header('Content-Type: text/plain');

$host = 'localhost';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $command = "ping -n 1 " . escapeshellarg($host);
} else {
    $command = "ping -c 1 " . escapeshellarg($host);
}

$output = [];
$returnCode = -1;

exec($command, $output, $returnCode);

echo "Command: $command\n";
echo "Return code: $returnCode\n";
echo "Output:\n" . implode("\n", $output);
