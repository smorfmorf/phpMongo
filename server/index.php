<?php


require 'vendor/autoload.php';

$client = new MongoDB\Client();

$collecion = $client->test->test;

$collecion->insertOne(['name' => 'test']);

$res = $collecion->find()->toArray();

echo json_encode($res);