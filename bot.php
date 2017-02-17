<?php
$access_token = 'WNFbXbJCTv3AWv2AFQIKhe6EtJjVPzA4aSA1O1u+CtZaa0hqHH63PK+IyU+hAz3mOnt/yg6FCD62fmsf3uYkxuRZFsJ9P/p79/g8UDzPYhZzXc8lrqc2XWL4443q3d2tBaLOz1EmS3HCY0EFEu7KDgdB04t89/1O/w1cDnyilFU=';

$url = 'https://api.line.me/v1/oauth/verify';

$headers = ['Authorization: Bearer ' . $access_token];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
$result = curl_exec($ch);
curl_close($ch);

echo $result;
?>