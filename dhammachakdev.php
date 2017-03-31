<?php
define("LINE_MESSAGING_API_CHANNEL_SECRET", 'cf0ce886078d72cb4dac84067938cd1a');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", 'koGlCXsvgSe+hGyZLr+5ggRf9+hy+YUFjzuZIy19/dk2T7yJLkrSB/I+R4Qjipaym4/QlQI20kGjzag554KDZc596JWaSgp5juQALxAZChLyfFiZcJxZsbU/8iRcytkDq02Q6gkxKLCyPB7g9lnIvAdB04t89/1O/w1cDnyilFU=');

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
	$text_received = $event->getText();
	$userId = $event->getUserId();
	$type = $event->getType();
	$timestamp = $event->getTimestamp();

	$getProfileResponse = $bot->getProfile($userId);
	if ($getProfileResponse->isSucceeded()) {
	    $profile = $getProfileResponse->getJSONDecodedBody();
	    $displayName =  $profile['displayName'];
	    $pictureUrl =  $profile['pictureUrl'];
	    $statusMessage =  $profile['statusMessage'];
	}

	$messages = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();


	$firebase = Firebase::fromServiceAccount(__DIR__.'/puri-contact-firebase-adminsdk-l04g2-fa656ae233.json');
	$database = $firebase->getDatabase();

	$reference = $database->getReference('dhammachak/Line_contact');
	$data = $reference->getValue(); 


	foreach($data as $value){
		if($userId==$value['line_id']){$registed = true;}
	}


	if(!$registed){
		// ถ้ายังไม่ลงทะเบียน ก็ลงทะเบียนให้ โดยส่งค่าไปบันทึกใน firebase
		$reference->push([
				'line_id' => $userId,
				'pictureUrl' => $pictureUrl,
				'displayName' => $displayName
		]);
	}

	$text_received = intval($text_received);

	// ถ้าสิ่งที่ส่งมาเป็นตัวเลข
	if(is_int($text_received)){
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("อนุโมทนากับการส่งยอดนะคะ คุณ".$displayName);
		$messages->add($_msg);     

		if(!$registed){
			$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$statusMessage");
			$messages->add($_msg);     
		}

		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ยอดที่คุณส่ง คือ ".$text_received." จบ  \n บันทึกเรียบร้อยค่ะ");
		$messages->add($_msg);

		$chants = $database->getReference('dhammachak/chants');

		$chants->push([
				'line_id' => $userId,
				'pictureUrl' => $pictureUrl,
				'displayName' => $displayName,
				'round' => $text_received,
				'timestamp' => $timestamp
		]);

	}else{
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("กรุณาส่งเฉพาะตัวเลข เพื่อบันทึกยอดสวด หรือ พิมพ์ว่า ยอดรวม หากท่านอยากดูยอดรวมนะคะ คุณ".$displayName);
		$messages->add($_msg);     
	}




	$response = $bot->replyMessage($reply_token, $messages);

/*
	$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("{$userId} : {$displayName} : {$text_received}");
	$response = $bot->pushMessage('U3c02f02d470aac70e331fcb0fe1eae3c', $textMessageBuilder);
*/
	echo $response->getHTTPStatus() . ' ' . $response->getRawBody();


}

echo "OK";
?>