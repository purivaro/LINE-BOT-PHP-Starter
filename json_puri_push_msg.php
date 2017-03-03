<?php
define("LINE_MESSAGING_API_CHANNEL_SECRET", '59cc1269e2956fd52b9c0eaadc70225c');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", '6tS7pO00ncfJFML6WrMEMXhtYru4rMFRapvH4qzPbxFp/2cf9dK6uxzzotYxyNMV51zGDZ23dznOogrpAhxNh3z881mOnyZ5M5mZVZPsyDj52DEvuJQZCf1u67UBBgkj+zrgPiD6n8Pd+lByPRTN0wdB04t89/1O/w1cDnyilFU=');

require __DIR__."/vendor/autoload.php";
$sendto = $_REQUEST['sendto'];
$text_send = $_REQUEST['text_send'];
// user id ของ puri = 'U02a2cb394330d90571a21b09f2c230ea'

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET]);



$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text_send);
$response = $bot->pushMessage($sendto, $textMessageBuilder);

// ส่งข้อความมาที่เครื่อง samsung taba ด้วย
$response = $bot->pushMessage('Ua2bdf85b0466beeb8c8af8fbccfba5df', $textMessageBuilder);

// โอ๊ต ID U12fd0233ec75ef62153d1ee3b31f1037
$jsonresponse = [
    "success"=>true,
    "feedback"=>"ส่งข้อความเรียบร้อย",
    "bot_response"=>$response
];

echo json_encode($jsonresponse);
?>