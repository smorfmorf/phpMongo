<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Vue через CDN</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
</head>

<body>

    <div id="app">
        <h1>{{ message }}</h1>
        <button @click="reverseMessage">Перевернуть</button>
    </div>

    <script>
    const {
        createApp
    } = Vue;

    createApp({
        data() {
            return {
                message: 'Привет, Vue!'
            }
        },
        methods: {
            reverseMessage() {
                this.message = this.message.split('').reverse().join('');
            }
        }
    }).mount('#app');
    </script>

</body>

</html>



<?php
require 'vendor/autoload.php';
// $client = new MongoDB\Client();
// $collecion = $client->test->test;
// $collecion->insertOne(['name' => 'test']);
// $res = $collecion->find()->toArray();
// echo json_encode($res);


$message = 'Hack';
$to = "mmaxim0@mail.ru";
$from = "mega///.com";

$subject = "=?utf-8?b?".base64_encode($message)."?=";
$headers = "From: $from\r\nReply-To: $from\r\nContent-type: text/html; charset=utf-8\r\n";
mail($to, $subject, $message, $headers);

// кодировка темы сообщения


$char = md5('test');
print_r($char);
?>