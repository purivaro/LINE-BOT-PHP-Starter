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

		// https://line.github.io/line-bot-sdk-php/class-LINE.LINEBot.Event.BaseEvent.html#_getUserId
        $messages = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
        for($i=0;$i<2;$i++)
        {
            $_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$userId".$i);
            $messages->add($_msg);
        }
        $txt = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$text");
        $messages->add($txt);    
        $txt = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$type");
        $messages->add($txt);    

        $imageMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder("https://s-media-cache-ak0.pinimg.com/originals/3d/19/e2/3d19e22f8fc92cdbd53337558220e262.jpg","https://s-media-cache-ak0.pinimg.com/originals/3d/19/e2/3d19e22f8fc92cdbd53337558220e262.jpg");            
        $messages->add($imageMessageBuilder);


        $response = $bot->replyMessage($reply_token, $messages);

        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();

}
echo "OK";
?>