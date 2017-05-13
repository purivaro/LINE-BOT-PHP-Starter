<?php
define("LINE_MESSAGING_API_CHANNEL_SECRET", '3e4001657a33f71bb310ffba52370d8e');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", '2Kv6ENeGC9MlIuiCLnePFEfbvkntSmNgCXhhel73PHUZVRoJIkcorESN4CcusUDzS+whtgnSRimkzU/fkFQb7b8v+4t0FLrHqUDHhRjJohCqcIm7sJYxrC9vxpUxBzXfpXeo+y7BhalZIS/OFhx14wdB04t89/1O/w1cDnyilFU=');

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
		$ref_group = $database->getReference('ibs/line/contact/group');
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
		"🌟🌟🌟🌟🌟🌟🌟🌟🌟🌟
		\n🌹 ขอบคุณที่เชิญเข้าร่วมกลุ่มนะคะ 🌹
		\n🔊 ฉันคือ Line-Bot 
		\n🔔 \"แจ้งข่าวชมรมพุทธ\" 🔔
		\n🍀🍀🍀 มีหน้าที่นำข่าวสารและกิจกรรมดีๆจากชมรมพุทธศาสตร์สากลฯ มาฝากทุกท่านค่ะ 🕊 🕊 🕊 
		\n💡 คอยติดตามกันนะคะ ^^
		\n🌟🌟🌟🌟🌟🌟🌟🌟🌟🌟
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
		$ref_group = $database->getReference('ibs/line/contact/group');
		$data = $ref_group->getValue(); 

		foreach($data as $key => $value){
			if($GroupId==$value['GroupId']){
				$database->getReference('ibs/line/contact/group/'.$key)->remove();
			}
		}
		

		// ข้อความตอบกลับ 2
		$response_text = 
		" ถูกลบจากกลุ่มแล้ว น้องเสียจุย TT
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
				$response = $bot->getMessageContent($msgId);
				if ($response->isSucceeded()) {
					$tempfile = tmpfile();
					fwrite($tempfile, $response->getRawBody());
				} else {
					error_log($response->getHTTPStatus() . ' ' . $response->getRawBody());
				}
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
				$location =	[
					'Title' => $getTitle,
					'Address' => $getAddress,
					'Latitude' => $getLatitude,
					'Longitude' => $getLongitude,
				];					
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
		$ref_user = $database->getReference('ibs/line/contact/user');
		$data = $ref_user->getValue(); 
		foreach($data as $key => $value){
			if($UserId==$value['UserId']){
				$registed = true;
				$row_key = $key;
				$Unread = ($value['Unread'])*1;
			}
		}
		
		// ถ้ายังไม่ลงทะเบียน ก็ลงทะเบียนให้ โดยส่งค่าไปบันทึกใน firebase	
		if(!$registed){
			$postRef = $ref_user->push([
					'UserId' => $UserId,
					'PhotoUrl' => $pictureUrl,
					'DisplayName' => $displayName,
					'timestamp' => $timestamp
			]);
			$row_key = $postRef->getKey();
		}

		// เก็บข้อมูลที่เต้าส่งมา Push to Firebase
		$chat_history = $database->getReference('ibs/line/chat_all');
		$chat_history->push([
				'UserId' => $UserId,
				'MessageType' => $MessageType,
				'PhotoUrl' => $pictureUrl,
				'DisplayName' => $displayName,
				'Text' => $text,
				'timestamp' => $timestamp,
				'MessageId' => $msgId,
				'Location' => $location,
		]);


		// เก็บข้อมูลที่เต้าส่งมา Push to Firebase
		$chat_history_user = $database->getReference("ibs/line/contact/user/{$row_key}/ChatHistory");
		$chat_history_user->push([
				'UserId' => $UserId,
				'MessageType' => $MessageType,
				'PhotoUrl' => $pictureUrl,
				'DisplayName' => $displayName,
				'Text' => $text,
				'timestamp' => $timestamp,
				'MessageId' => $msgId,
				'Location' => $location,
				'Read' => 0,
		]);

		// เก็บข้อมูล Chat ล่าสุด to Firebase
		$database->getReference("ibs/line/contact/user/{$row_key}/ChatLastText")->set($text);
		$database->getReference("ibs/line/contact/user/{$row_key}/ChatLastTimestamp")->set(-1*$timestamp);
		$database->getReference("ibs/line/contact/user/{$row_key}/Unread")->set($Unread+1);



		// สร้าง Object ข้อความตอบกลับ
		$reply_messages = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();


		// ข้อความตอบกลับ 1
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("🌟เราได้รับข้อความของคุณ{$displayName} แล้วค่ะ ทางทีมงานจะทยอยตอบนะคะ💡");
		$reply_messages->add($_msg);

/*
		$response_text = 
		"\n ท่านสามารถฝากข้อความไว้ได้้อีกนะคะ แล้วทางทีมงานจะทยอยตอบให้ค่ะ ^^
		\n🌟🌟🌟🌟🌟🌟🌟🌟🌟🌟
		";


		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($response_text);
		$reply_messages->add($_msg);
*/

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


	// ข้อความส่งถึง Puri

	$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($response_text);

	$response = $bot->pushMessage('U66c236822e18940229be8b7e93464a99', $textMessageBuilder);

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