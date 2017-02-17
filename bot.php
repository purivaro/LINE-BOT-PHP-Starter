<?php
$access_token = 'WNFbXbJCTv3AWv2AFQIKhe6EtJjVPzA4aSA1O1u+CtZaa0hqHH63PK+IyU+hAz3mOnt/yg6FCD62fmsf3uYkxuRZFsJ9P/p79/g8UDzPYhZzXc8lrqc2XWL4443q3d2tBaLOz1EmS3HCY0EFEu7KDgdB04t89/1O/w1cDnyilFU=';

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

            // Get reply token
            $replyToken = $event['replyToken'];

            // Build message to reply back
            $messages = [
                'type' => 'text',
                'text' => $text
            ];
        }
    }

    $url = 'https://api.line.me/v1/oauth/verify';
    $data = [
        'replyToken'=> $replyToken,
        'messages'=>[$messages]
    ];
    $post = json_encode($data);
    $headers = ['Content-Type: application/json','Authorization: Bearer ' . $access_token];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFILEDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    echo $result."\r\n";

}

echo "OK";

?>