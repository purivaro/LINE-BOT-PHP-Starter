<?php
require __DIR__."/vendor/autoload.php";

$firebase = Firebase::fromServiceAccount(__DIR__.'/Puri-contact-f810e37143c7.json');
$database = $firebase->getDatabase();

$reference = $database->getReference('object/Line_contact');
$value = $reference->getValue();

/*
$snapshot = $reference->orderByChild('line_id')->equalTo('Uf539dec2c746e3b8c869fa69e6a96e06')->getSnapshot();
$value = $snapshot->getValue();
*/
echo json_encode($value);
/*
$reference->push([
        'title' => 'Post title',
        'body' => 'This should probably be longer.'
    ]);
*/
?>