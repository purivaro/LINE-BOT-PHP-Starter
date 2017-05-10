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
	$timestamp = $event->getTimestamp();	

	// ถ้าเป็นการ join group
	if($eventType == 'join'){
		$reply_token = $event->getReplyToken();			
		$GroupId = $event->getGroupId();

		//  check ว่ามี group นี้ใน firebase หรือยัง ถ้ายัง ก็เพิ่มเลย
		$ref_group = $database->getReference('line/contact/group');
		$data = $ref_group->getValue(); 
		foreach($data as $value){
			if($GroupId==$value['GroupId']){$group_registed = true;}
		}
		
		// ถ้ายังไม่ลงทะเบียน ก็ลงทะเบียนให้ โดยส่งค่าไปบันทึกใน firebase	
		if(!$group_registed){
			$ref_group->push([
					'GroupId' => $GroupId,
					'timestamp' => $timestamp,
			]);
		}

		// ข้อความตอบกลับ
		$response_text = 
		" Thanks 
		\n GroupId : {$GroupId} 
		\n EventType : {$eventType}
		";


		// ส่งข้อความไปยังกลุ่ม
		$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($response_text);
		$response_group = $bot->pushMessage($GroupId, $textMessageBuilder);

		/*
		// ส่งข้อความตอบกลับ
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($response_text);
		$reply_messages->add($_msg);
		$response = $bot->replyMessage($reply_token, $reply_messages);
		*/
	}

	// ถ้าเป็นการ leave group
	if($eventType == 'leave'){
		$GroupId = $event->getGroupId();
		//  ลบ group id นี้ออกจาก firebase
		$ref_group = $database->getReference('line/contact/group');
		$data = $ref_group->getValue(); 

		foreach($data as $key => $value){
			if($GroupId==$value['GroupId']){
				$database->getReference('line/contact/group/'.$key)->remove();
			}
		}
		

		// ข้อความตอบกลับ 2
		$response_text = 
		" Sorry  
		\n GroupId : {$GroupId} 
		\n EventType : {$eventType}
		";

	}

	// ถ้าเป็นการส่งข้อความ
	if($eventType == 'message'){
		$reply_token = $event->getReplyToken();			
		$msgId = $event->getMessageId();
		$UserId = $event->getUserId();
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

		// เก็บข้อมูล profile
		$getProfileResponse = $bot->getProfile($UserId);
		if ($getProfileResponse->isSucceeded()) {
			$profile = $getProfileResponse->getJSONDecodedBody();

			$displayName =  $profile['displayName'];
			$pictureUrl =  $profile['pictureUrl'];
			$statusMessage =  $profile['statusMessage'];
		}		

		// check ดูว่ามีรายชื่อ line id นี้ ใน firebase หรือยัง
		$ref_user = $database->getReference('line/contact/user');
		$data = $ref_user->getValue(); 
		foreach($data as $value){
			if($UserId==$value['UserId']){$registed = true;}
		}
		
		// ถ้ายังไม่ลงทะเบียน ก็ลงทะเบียนให้ โดยส่งค่าไปบันทึกใน firebase	
		if(!$registed){
			$ref_user->push([
					'UserId' => $UserId,
					'PhotoUrl' => $pictureUrl,
					'DisplayName' => $displayName
			]);
		}

		// เก็บข้อมูลที่เต้าส่งมา Push to Firebase
		$chat_history = $database->getReference('line/chat_history');
		$chat_history->push([
				'line_id' => $UserId,
				'MessageType' => $MessageType,
				'pictureUrl' => $pictureUrl,
				'displayName' => $displayName,
				'text' => $text,
				'timestamp' => $timestamp,
				'msgId' => $msgId,
		]);

		// สร้าง Object ข้อความตอบกลับ
		$reply_messages = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();


		// ข้อความตอบกลับ 1
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("สวัสดีคุณ".$displayName);
		$reply_messages->add($_msg);

		// ข้อความตอบกลับ 2
		$response_text = 
		" MsgId : {$msgId} 
		\n User ID : {$UserId} 
		\n Display Name : {$displayName} 
		\n MessageType : {$MessageType} 
		\n Text : {$text}
		\n EventType : {$eventType}
		";

		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($response_text);
		$reply_messages->add($_msg);


		// ส่งข้อความตอบกลับ
		$response = $bot->replyMessage($reply_token, $reply_messages);


	}




	//Get content such as image
	//https://api.line.me/v2/bot/message/{messageId}/content
	/*
		$response = $bot->getMessageContent($msgId);
		if ($response->isSucceeded()) {
			$tempfile = tmpfile();
			fwrite($tempfile, $response->getRawBody());
			$write_file = 'Yes';
		} else {
			error_log($response->getHTTPStatus() . ' ' . $response->getRawBody());
		}
	*/


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
	    $_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$UserId".$i);
	    $messages->add($_msg);
	}
 */
 

/* 
	$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$pictureUrl");
	$messages->add($_msg);      

	$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ID : $UserId");
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