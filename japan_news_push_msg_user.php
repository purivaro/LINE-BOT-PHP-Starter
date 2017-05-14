<?php
header('Access-Control-Allow-Origin: *');
define("LINE_MESSAGING_API_CHANNEL_SECRET", 'eaeea42ffe229f75cb6626547ae8e82e');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'ZOcfp4Mi8az1ON54HxXTWb09114ukaQFGOAmT9ILSuEz7hHlNKFPHU7t8YUjutXEzwU6p8oK3s1D6ZzC8GLaO3DM3qXi4TczJ1xni09vrV+qQUD2UkZsbpBkVJ8LCoJwwBumuCeWCRAyjqdbazcQEwdB04t89/1O/w1cDnyilFU=');

require __DIR__."/vendor/autoload.php";

$MessageType = $_REQUEST['msg_type'];
$UserId = $_REQUEST['UserId'];
$text_send = $_REQUEST['text_send'];
$thumbnail = $_REQUEST['thumbnail'];

if(!$text_send){
    exit();
}
// user id ของ puri = 'U02a2cb394330d90571a21b09f2c230ea'

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


$response = $bot->pushMessage($UserId, $messages);    


date_default_timezone_set('Asia/Bangkok');

$current_time = date("Y/m/d H:i:s");

$date = new DateTime();
$timestamp = $date->getTimestamp();

// เก็บข้อมูลที่เต้าส่งมา Push to Firebase
$chat_history = $database->getReference('ibs/line/chat_all');
$chat_history->push([
    'UserId' => $UserId,
    'MessageType' => $MessageType,
    'Text' => $text_send,
    'Thumbnail' => $thumbnail,
    'current_time' => $current_time,
    'timestamp' => $timestamp,
    'DisplayName' => 'admin',
    'Admin' => 1,
]);


// check ดูว่ามีรายชื่อ line id นี้ ใน firebase หรือยัง
$ref_user = $database->getReference('ibs/line/contact/user');
$data = $ref_user->getValue(); 
foreach($data as $key => $value){
    if($UserId==$value['UserId']){
        $row_key = $key;
    }
}

// เก็บข้อมูลที่เต้าส่งมา Push to Firebase
$chat_history_user = $database->getReference("ibs/line/contact/user/{$row_key}/ChatHistory");
$chat_history_user->push([
    'UserId' => $UserId,
    'MessageType' => $MessageType,
    'Text' => $text_send,
    'Thumbnail' => $thumbnail,
    'current_time' => $current_time,
    'timestamp' => $timestamp,
    'DisplayName' => 'admin',
    'Admin' => 1,
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