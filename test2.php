<?php
require __DIR__."/firebase-php-master/src/Firebase.php";

$firebase = Firebase::fromServiceAccount(__DIR__.'/Puri-contact-f810e37143c7.json');
$database = $firebase->getDatabase();

$reference = $database->getReference('object/Line_contact');
$value = $reference->getValue();
print_r($value);
/*
$url_ibs = "http://www.ibsone.com/project/linebot/puribot/api/translate.php";
$text = 'ลิง';
$lang = 'สเปน';

$data_ibs = [
'text'=> $text,
'lang'=>$lang
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
$result = $res['result'];
echo $result;
*/
echo "Hello2";
?>