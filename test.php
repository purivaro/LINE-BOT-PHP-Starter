<?php

$url = "http://www.ibsone.com/project/linebot/puribot/api/translate.php";

// Build message to reply back
$messages = [
    [
        'type' => 'text',
        'text' => "ดีคับ"
    ],
    [
        'type' => 'text',
        'text' => "เมื่อกี้คุณพูดว่า..".$text
    ],
    [
        'type' => 'text',
        'text' => "มีอะไรเหรอครับ?"
    ],
];


$url = 'https://api.line.me/v2/bot/message/reply';
$data = [
'replyToken'=> $replyToken,
'messages'=>$messages
];
$post = json_encode($data);
$headers = ['Content-Type: application/json','Authorization: Bearer ' . $access_token];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

echo $result."\r\n";

?>