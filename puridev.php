<?php
define("LINE_MESSAGING_API_CHANNEL_SECRET", '59cc1269e2956fd52b9c0eaadc70225c');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", '6tS7pO00ncfJFML6WrMEMXhtYru4rMFRapvH4qzPbxFp/2cf9dK6uxzzotYxyNMV51zGDZ23dznOogrpAhxNh3z881mOnyZ5M5mZVZPsyDj52DEvuJQZCf1u67UBBgkj+zrgPiD6n8Pd+lByPRTN0wdB04t89/1O/w1cDnyilFU=');

require __DIR__."/vendor/autoload.php";

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET]);

$signature = $_SERVER["HTTP_".\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
$body = file_get_contents("php://input");
try {
  $events = $bot->parseEventRequest($body, $signature);
} catch (Exception $e) {
  var_dump($e); 
}

// connect กับ firebase
$firebase = Firebase::fromServiceAccount(__DIR__.'/puri-contact-firebase-adminsdk-l04g2-fa656ae233.json');
$database = $firebase->getDatabase();



foreach ($events as $event) {
	$eventType = $event->getType();
	$reply_token = $event->getReplyToken();
	$msgId = $event->getMessageId();
	$userId = $event->getUserId();
	$timestamp = $event->getTimestamp();

	$MessageType = $event->getMessageType();	
	switch($MessageType){
		case "text":
			$text = $event->getText();				
			break;
		case "image":
			/*
			$response = $bot->getMessageContent('<messageId>');
			if ($response->isSucceeded()) {
				$tempfile = tmpfile();
				fwrite($tempfile, $response->getRawBody());
			} else {
				error_log($response->getHTTPStatus() . ' ' . $response->getRawBody());
			}
			*/
			$text = "Image Sent";				
			break;
		case "video":
			$text = "video Sent";				
			break;
		case "audio":
			$text = "audio Sent";				
			break;
		case "file":
			$text = "file Sent";				
			break;
		case "location":
			$getTitle = $event->getTitle();
			$getAddress = $event->getAddress();
			$getLatitude = $event->getLatitude();
			$getLongitude = $event->getLongitude();
			$text = 
				" location Sent
				\n getTitle : $getTitle
				\n getAddress : $getAddress
				\n getLatitude : $getLatitude
				\n getLongitude : $getLongitude				
			";				
			break;
		case "sticker":
			$StickerId = $event->getStickerId();		
			$text = "Sticker Sent - ID : $StickerId ";				
			break;			
		default:
			$text = "Default";			
	}


	$getProfileResponse = $bot->getProfile($userId);
	if ($getProfileResponse->isSucceeded()) {
	    $profile = $getProfileResponse->getJSONDecodedBody();

	    $displayName =  $profile['displayName'];
	    $pictureUrl =  $profile['pictureUrl'];
	    $statusMessage =  $profile['statusMessage'];
	}



	//Get content such as image
	//https://api.line.me/v2/bot/message/{messageId}/content

	$response = $bot->getMessageContent($msgId);
	if ($response->isSucceeded()) {
		$tempfile = tmpfile();
		fwrite($tempfile, $response->getRawBody());
		$write_file = 'Yes';
	} else {
		error_log($response->getHTTPStatus() . ' ' . $response->getRawBody());
	}


	// เก็บข้อมูลที่เต้าส่งมา Push to Firebase
	$chat_history = $database->getReference('line/chat_history/puri_dev');
	$chat_history->push([
			'line_id' => $userId,
			'MessageType' => $MessageType,
			'pictureUrl' => $pictureUrl,
			'displayName' => $displayName,
			'text' => $text,
			'timestamp' => $timestamp,
			'msgId' => $msgId,
			'write_file' => $write_file,
	]);

	// สร้าง Object ข้อความตอบกลับ
	$reply_messages = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();


	// check ดูว่ามีรายชื่อ line id นี้ ใน firebase หรือยัง
	$reference = $database->getReference('object/Line_contact');
	$data = $reference->getValue(); 
	foreach($data as $value){
		if($userId==$value['line_id']){$registed = true;}
	}
	
	// ถ้ายังไม่ลงทะเบียน ก็ลงทะเบียนให้ โดยส่งค่าไปบันทึกใน firebase	
	if(!$registed){
		$reference->push([
				'line_id' => $userId,
				'nickname' => $text,
				'pictureUrl' => $pictureUrl,
				'displayName' => $displayName
		]);
	}

	// ข้อความตอบกลับ 1
	$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("สวัสดีคุณ".$displayName);
	$reply_messages->add($_msg);

	// ข้อความตอบกลับ 2
	$response_text = 
	" MsgId : {$msgId} 
	\n User ID : {$userId} 
	\n Display Name : {$displayName} 
	\n MessageType : {$MessageType} 
	\n Text : {$text}
	\n EventType : {$eventType}
	";

	$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($response_text);
	$reply_messages->add($_msg);


	// ส่งข้อความตอบกลับ
	$response = $bot->replyMessage($reply_token, $reply_messages);


	// ข้อความเลือกผู้ส่ง

	$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($response_text);

	$response = $bot->pushMessage('U02a2cb394330d90571a21b09f2c230ea', $textMessageBuilder);

	echo $response->getHTTPStatus() . ' ' . $response->getRawBody();


}

echo "OK";











	
/* 
	// ถ้าลงทะเบียนแล้ว
	//$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("คุณ".$displayName." ลงทะเบียนเรียบร้อยแล้ว");
	//$messages->add($_msg);
	//$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ข้อความที่คุณส่งมา เป็นประเภท $type จะรวมอยู่ที่นี่ https://puri-contact.firebaseapp.com/line_chat_puridev.html");
	//$messages->add($_msg);


       for($i=0;$i<2;$i++)
	{
	    $_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$userId".$i);
	    $messages->add($_msg);
	}
 */
 

/* 
	$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$pictureUrl");
	$messages->add($_msg);      

	$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ID : $userId");
	$messages->add($_msg);


	$txt = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$text");
	$messages->add($txt);    
	$txt = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$type");
	$messages->add($txt);*/
	
/*
	$imageMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder("https://s-media-cache-ak0.pinimg.com/originals/3d/19/e2/3d19e22f8fc92cdbd53337558220e262.jpg","https://s-media-cache-ak0.pinimg.com/originals/3d/19/e2/3d19e22f8fc92cdbd53337558220e262.jpg");            
	$messages->add($imageMessageBuilder);

*/
?>