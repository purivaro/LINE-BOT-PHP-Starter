<?php
header('Access-Control-Allow-Origin: *');
define("LINE_MESSAGING_API_CHANNEL_SECRET", '59cc1269e2956fd52b9c0eaadc70225c');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", '6tS7pO00ncfJFML6WrMEMXhtYru4rMFRapvH4qzPbxFp/2cf9dK6uxzzotYxyNMV51zGDZ23dznOogrpAhxNh3z881mOnyZ5M5mZVZPsyDj52DEvuJQZCf1u67UBBgkj+zrgPiD6n8Pd+lByPRTN0wdB04t89/1O/w1cDnyilFU=');

require __DIR__."/vendor/autoload.php";
$text_send = $_REQUEST['text_send'];
$msg_type = $_REQUEST['msg_type'];
// user id ของ puri = 'U02a2cb394330d90571a21b09f2c230ea'

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET]);


// connect กับ firebase
$firebase = Firebase::fromServiceAccount(__DIR__.'/puri-contact-firebase-adminsdk-l04g2-fa656ae233.json');
$database = $firebase->getDatabase();

// สร้าง Object ข้อความตอบกลับ
$messages = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();

if($msg_type == 'text'){
    // ข้อความ
    $_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text_send);
    $messages->add($_msg);
    
}elseif($msg_type == 'image'){
    // รูป
    $imageMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($text_send,$text_send);
    $messages->add($imageMessageBuilder);
}





//  check ว่ามี group นี้ใน firebase หรือยัง ถ้ายัง ก็เพิ่มเลย
$ref_group = $database->getReference('line/contact/group');
$data = $ref_group->getValue(); 
foreach($data as $value){
    $GroupId = $value['GroupId'];
    $response = $bot->pushMessage($GroupId, $messages);    
}


// ส่งข้อความมาที่เครื่อง samsung taba ด้วย
//$response = $bot->pushMessage('Ua2bdf85b0466beeb8c8af8fbccfba5df', $textMessageBuilder);

// โอ๊ต ID U12fd0233ec75ef62153d1ee3b31f1037
$jsonresponse = [
    "success"=>true,
    "feedback"=>"ส่งข้อความเรียบร้อย",
    "bot_response"=>$response
];

echo json_encode($jsonresponse);
?>