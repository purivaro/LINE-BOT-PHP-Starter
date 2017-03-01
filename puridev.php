<?php

$access_token = '6tS7pO00ncfJFML6WrMEMXhtYru4rMFRapvH4qzPbxFp/2cf9dK6uxzzotYxyNMV51zGDZ23dznOogrpAhxNh3z881mOnyZ5M5mZVZPsyDj52DEvuJQZCf1u67UBBgkj+zrgPiD6n8Pd+lByPRTN0wdB04t89/1O/w1cDnyilFU=';

define("LINE_MESSAGING_API_CHANNEL_SECRET", '59cc1269e2956fd52b9c0eaadc70225c');
define("LINE_MESSAGING_API_CHANNEL_TOKEN", '6tS7pO00ncfJFML6WrMEMXhtYru4rMFRapvH4qzPbxFp/2cf9dK6uxzzotYxyNMV51zGDZ23dznOogrpAhxNh3z881mOnyZ5M5mZVZPsyDj52DEvuJQZCf1u67UBBgkj+zrgPiD6n8Pd+lByPRTN0wdB04t89/1O/w1cDnyilFU=');

require __DIR__."/vendor/autoload.php";
/*
$bot = new \LINE\LINEBot(
    new \LINE\LINEBot\HTTPClient\CurlHTTPClient(LINE_MESSAGING_API_CHANNEL_TOKEN),
    ['channelSecret' => LINE_MESSAGING_API_CHANNEL_SECRET]
);
*/

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


            /*

            $url_ibs = "http://www.ibsone.com/project/linebot/vstarapp/api/reply.php";

            $data_ibs = [
            'text'=> $text
            ];

            $post_ibs = json_encode($data_ibs);
            $headers_ibs = ['Content-Type: application/json'];
            $ch_ibs = curl_init($url_ibs);
            curl_setopt($ch_ibs, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch_ibs, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_ibs, CURLOPT_POSTFIELDS, $post_ibs);
            curl_setopt($ch_ibs, CURLOPT_HTTPHEADER, $headers_ibs);
            curl_setopt($ch_ibs, CURLOPT_FOLLOWLOCATION, 1);
            $result_ibs = curl_exec($ch_ibs);
            curl_close($ch_ibs);

            $res = json_decode($result_ibs,true);

            $messages = $res['messages'];

            */
            $text = "$userId";
            $messages = [
                [
				'type' => 'text',
				'text' => $text
			    ]
            ];


            // Get reply token
            $replyToken = $event['replyToken'];


            $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');
            $response = $bot->replyMessage($replyToken, $textMessageBuilder);


            echo $response->getHTTPStatus() . ' ' . $response->getRawBody();



        }
    }
}

echo "OK";
?>