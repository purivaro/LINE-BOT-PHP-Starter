<?php
define("LINE_MESSAGING_API_CHANNEL_SECRET", '59cc1269e2956fd52b9c0eaadc70225c');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", '6tS7pO00ncfJFML6WrMEMXhtYru4rMFRapvH4qzPbxFp/2cf9dK6uxzzotYxyNMV51zGDZ23dznOogrpAhxNh3z881mOnyZ5M5mZVZPsyDj52DEvuJQZCf1u67UBBgkj+zrgPiD6n8Pd+lByPRTN0wdB04t89/1O/w1cDnyilFU=');

require __DIR__."/vendor/autoload.php";

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN);
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET]);


// Get POST body content
$content = file_get_contents('php://input');

// Parse JSON
$events = json_decode($content,true);

// Validate parsed JSON date_add
if(!is_null($events['events'])){
    foreach($events['events'] as $event){
        if($event['type']=='message' && $event['message']['type']=='text'){
            // Get text sent
            $text = $event['message']['text'];
            $userId = $event['source']['userId'];

            $text = "$userId";


            // Get reply token
            $replyToken = $event['replyToken'];

            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$userId");

            $messages = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
            for($i=0;$i<2;$i++)
            {
                $_msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("$userId".$i);
                $messages->add($_msg);
            }
            
            $response = $bot->replyMessage($replyToken, $messages);

            echo $response->getHTTPStatus() . ' ' . $response->getRawBody();


        }
    }
}

echo "OK";
?>