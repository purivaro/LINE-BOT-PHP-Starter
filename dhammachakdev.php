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

	$reference = $database->getReference('dhammachak/contacts');
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

	$round_received = int($text_received);

	$round_limit = 500;

	// ถ้าสิ่งที่ส่งมาเป็นตัวเลข และไม่เกิน limit
	if(is_int($round_received) && $round_received > 0 && $round_received <= $round_limit){
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ขออนุโมทนาบุญกับการส่งยอดนะคะ คุณ".$displayName);
		$messages->add($_msg);     

		if(!$registed){
			$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$statusMessage");
			$messages->add($_msg);     
		}

		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ยอดที่คุณส่งล่าสุด คือ ".$text_received." จบ  \n\n**บันทึกเรียบร้อยค่ะ**");
		$messages->add($_msg);

		$chants = $database->getReference('dhammachak/chants/'.$userId);

		$chants->push([
				'line_id' => $userId,
				'pictureUrl' => $pictureUrl,
				'displayName' => $displayName,
				'round' => $text_received,
				'timestamp' => $timestamp
		]);

	}elseif(is_int($round_received) && $round_received > $round_limit){
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ท่านสามารถส่งยอดได้ ไม่เกินครั้งละ $round_limit จบ นะคะ \n\n กรุณาส่งใหม่อีกครั้งค่ะ คุณ".$displayName);
		$messages->add($_msg);  

	}elseif($text_received == "ยอดรวม"){

		$round_ref = $database->getReference('dhammachak/chants/'.$userId);
		$round_data = $round_ref->getValue(); 

		$sum_round = 0;
		foreach($round_data as $value){
			$sum_round += int($value['round']);
		}

		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ยอดรวมทั้งหมดที่ส่งมาแล้วของคุณ ".$displayName." คือ \n\n ".number_format($sum_round)." จบ \n\nขอกราบอนุโมทนาบุญด้วยนะคะ");
		$messages->add($_msg);

	}elseif($text_received == "ยกเลิก"){



		$last_round_ref = $database->getReference('dhammachak/chants/'.$userId)->orderByChild('timestamp')->limitToLast(1)->getSnapshot();
		$round_data = $last_round_ref->getValue(); 

		$last_round = 0;
		$last_round = $round_data[0]['round'];
		

		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("ลบยอดล่าสุดของท่าน คือ \n\n ".number_format($last_round)." จบ \n\n เรียบร้อยค่ะ");
		$messages->add($_msg);

	}else{
		$_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("กรุณาส่งเฉพาะตัวเลข เพื่อบันทึกยอดสวด\n\nหรือถ้าจะดูยอดทั้งหมดที่ส่งไปแล้ว  \n\nให้พิมพ์ว่า \"ยอดรวม\" นะคะ คุณ".$displayName);
		$messages->add($_msg);     
	}

	$response = $bot->replyMessage($reply_token, $messages);

	echo $response->getHTTPStatus() . ' ' . $response->getRawBody();


}

echo "OK";

/// ฟังก์ชัน เปลี่ยนข้อความเป็นตัวเลข
function int($s){return(int)preg_replace('/[^\-\d]*(\-?\d*).*/','$1',$s);}
?>