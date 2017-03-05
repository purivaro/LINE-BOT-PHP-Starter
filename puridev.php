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

foreach ($events as $event) {
	$reply_token = $event->getReplyToken();
	$text = $event->getText();
	$userId = $event->getUserId();
	$type = $event->getType();
	$timestamp = $event->getTimestamp();

	$getProfileResponse = $bot->getProfile($userId);
	if ($getProfileResponse->isSucceeded()) {
	    $profile = $getProfileResponse->getJSONDecodedBody();
	    //echo $profile['displayName'];
	    //echo $profile['pictureUrl'];
	    //echo $profile['statusMessage'];
	    $displayName =  $profile['displayName'];
	    $pictureUrl =  $profile['pictureUrl'];
	    $statusMessage =  $profile['statusMessage'];
	}

	$messages = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();


	$firebase = Firebase::fromServiceAccount(__DIR__.'/puri-contact-firebase-adminsdk-l04g2-fa656ae233.json');
	$database = $firebase->getDatabase();

	$reference = $database->getReference('object/Line_contact');
	$data = $reference->getValue(); 


	foreach($data as $value){
		if($userId==$value['line_id']){$registed = true;}
	}


	
	if(!$registed){
		// ถ้ายังไม่ลงทะเบียน ก็ลงทะเบียนให้ โดยส่งค่าไปบันทึกใน firebase
		$reference->push([
				'line_id' => $userId,
				'nickname' => $text,
				'pictureUrl' => $pictureUrl,
				'displayName' => $displayName
		]);

		// จากนั้นส่งข้อความตอบกลับไป
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ขอบคุณที่ลงทะเบียน คุณ".$displayName);
		$messages->add($_msg);     

		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$statusMessage");
		$messages->add($_msg);     

	}else{
		// ถ้าลงทะเบียนแล้ว
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("คุณ".$displayName." ลงทะเบียนเรียบร้อยแล้ว");
		$messages->add($_msg);
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ข้อความที่คุณส่งมา จะรวมอยู่ที่นี่ https://puri-contact.firebaseapp.com/line_chat_puridev.html");
		$messages->add($_msg);

		$chat_history = $database->getReference('line/chat_history/puri_dev');
		if($type=='sticker'){$text="sticker send";}	
		$chat_history->push([
				'line_id' => $userId,
				'pictureUrl' => $pictureUrl,
				'displayName' => $displayName,
				'text' => $text,
				'timestamp' => $timestamp
		]);
	}




/*        for($i=0;$i<2;$i++)
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


	$imageMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($profile['pictureUrl'],$profile['pictureUrl']);            
	$messages->add($imageMessageBuilder);
*/

	$response = $bot->replyMessage($reply_token, $messages);


	$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("{$userId} : {$displayName} : {$text}");
	$response = $bot->pushMessage('U02a2cb394330d90571a21b09f2c230ea', $textMessageBuilder);

	echo $response->getHTTPStatus() . ' ' . $response->getRawBody();


}



echo "OK";
?>