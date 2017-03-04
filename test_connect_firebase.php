<?php
require __DIR__."/vendor/autoload.php";

$firebase = Firebase::fromServiceAccount(__DIR__.'/Puri-contact-f810e37143c7.json');
$database = $firebase->getDatabase();

//$reference = $database->getReference('object/Line_contact');
$reference = $database->getReference('line');
//$value = $database->getReference('line')->getChildKeys();
//$value = $reference->getValue(); 


//$snapshot = $reference->orderByChild("line_id")->equalTo("Ub1c272947e6de86751d7142334b88ca1")->getSnapshot();
$snapshot = $reference->orderByChild("line_id")->getSnapshot();

$value = $snapshot->getValue();

echo json_encode($value);
/*
$reference->push([
        'title' => 'Post title',
        'body' => 'This should probably be longer.'
    ]);
*/
?>