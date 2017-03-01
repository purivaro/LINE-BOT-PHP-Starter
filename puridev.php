<?php
<<<<<<< HEAD
$access_token = 'tjNYZtCkJ031iEjB3YJJOcRLjN8si60Z3jc2b3aR36tX95Qi4bwaruSdYxP34x0coZlGJ0YXDVVGk/fxxRerx9AXW5s4bFungF6OXvoa39YMCR12oGXDrH8JgGUDFGa1eKTP7Mrdihz9AniPim0RlgdB04t89/1O/w1cDnyilFU=';
=======
$access_token = '+3Rtmn3cUM2ToF17ubFQKVfZ5M6z+jKV6dJet2nhoKx3N+6wt1P31KDcyZz1ENnT51zGDZ23dznOogrpAhxNh3z881mOnyZ5M5mZVZPsyDjmf0BtSEFt7z7dA6Kmm0jGrhHLnLylyjyy7yCs38mUBQdB04t89/1O/w1cDnyilFU=';
>>>>>>> 26d416314555ea2f1329f3c7549d8c97498f265b

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

<<<<<<< HEAD
            /*
=======
>>>>>>> 26d416314555ea2f1329f3c7549d8c97498f265b
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
<<<<<<< HEAD
            */
            $text = "hello";
            $messages = [
                [
				'type' => 'text',
				'text' => $text
			    ]
            ];
=======

>>>>>>> 26d416314555ea2f1329f3c7549d8c97498f265b
            // Get reply token
            $replyToken = $event['replyToken'];

            $url = 'https://api.line.me/v2/bot/message/reply';
            $data = [
                'replyToken'=> $replyToken,
                'messages'=>$messages
            ];
            $post = json_encode($data);
            $headers = ['Content-Type: application/json','Authorization: Bearer ' . $access_token];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            echo $result."\r\n";
        }
    }
}

echo "OK";
?>