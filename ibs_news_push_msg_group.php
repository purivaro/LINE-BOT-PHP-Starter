<?php
header('Access-Control-Allow-Origin: *');
define("LINE_MESSAGING_API_CHANNEL_SECRET", '3e4001657a33f71bb310ffba52370d8e');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", '2Kv6ENeGC9MlIuiCLnePFEfbvkntSmNgCXhhel73PHUZVRoJIkcorESN4CcusUDzS+whtgnSRimkzU/fkFQb7b8v+4t0FLrHqUDHhRjJohCqcIm7sJYxrC9vxpUxBzXfpXeo+y7BhalZIS/OFhx14wdB04t89/1O/w1cDnyilFU=');

require __DIR__."/vendor/autoload.php";

$MessageType = $_REQUEST['msg_type'];
$text_send = $_REQUEST['text_send'];
$thumbnail = $_REQUEST['thumbnail'];

// user id ของ puri = 'U02a2cb394330d90571a21b09f2c230ea'
if(!$text_send){
    exit();
}


$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET]);


// connect กับ firebase
$firebase = Firebase::fromServiceAccount(__DIR__.'/puri-contact-firebase-adminsdk-l04g2-fa656ae233.json');
$database = $firebase->getDatabase();

// สร้าง Object ข้อความตอบกลับ
$messages = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();

if($MessageType == 'text'){
    // ข้อความ
    $_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text_send);
    $messages->add($_msg);
    
}elseif($MessageType == 'image'){
    // รูป
    $imageMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($text_send,$thumbnail);
    $messages->add($imageMessageBuilder);
}elseif($MessageType == 'video'){
    // Video
    $VideoMessageBuilder = new \LINE\LINEBot\MessageBuilder\VideoMessageBuilder($text_send,$thumbnail);
    $messages->add($VideoMessageBuilder);
}





//  check ว่ามี group นี้ใน firebase หรือยัง ถ้ายัง ก็เพิ่มเลย
$ref_group = $database->getReference('ibs/line/contact/group');
$data = $ref_group->getValue(); 
foreach($data as $value){
    $GroupId = $value['GroupId'];
    $response = $bot->pushMessage($GroupId, $messages);    
}

date_default_timezone_set('Asia/Bangkok');

$current_time = date("Y/m/d H:i:s");

$date = new DateTime();
$timestamp = $date->getTimestamp();

// เก็บข้อมูลที่เต้าส่งมา Push to Firebase
$chat_history = $database->getReference('ibs/line/broadcastGroup');
$chat_history->push([
    'GroupId' => $GroupId,
    'MessageType' => $MessageType,
    'Text' => $text_send,
    'Thumbnail' => $thumbnail,
    'current_time' => $current_time,
    'timestamp' => $timestamp,
]);


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