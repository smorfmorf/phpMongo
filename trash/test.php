<?php
// Тестовые данные
$testData = [
    [
        "siteId" => 253,
        "stationCode" => "89009",
        "stationName" => "Богородское",
        "stationType" => "МС",
        "stationRegion" => 27,
        "pingStatus" => "OK",
        "telnetStatus" => "",
        "messageTime" => "2023-08-24 00:04:46"
    ],
    [
        "siteId" => 257,
        "stationCode" => "31469",
        "stationName" => "Чегдомын",
        "stationType" => "МС",
        "stationRegion" => 27,
        "pingStatus" => "",
        "telnetStatus" => "",
        "messageTime" => "2023-08-24 00:05:17"
    ],
    [
        "siteId" => 258,
        "stationCode" => "31478",
        "stationName" => "Софийский Прииск",
        "stationType" => "МС",
        "stationRegion" => 27,
        "pingStatus" => "",
        "telnetStatus" => "",
        "messageTime" => "2023-08-24 00:04:46"
    ]
];

// Если нужно вернуть как JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($testData, JSON_UNESCAPED_UNICODE);
?>