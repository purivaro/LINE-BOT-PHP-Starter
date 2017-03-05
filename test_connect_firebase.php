<?php
require __DIR__."/vendor/autoload.php";

$firebase = Firebase::fromServiceAccount(__DIR__.'/puri-contact-firebase-adminsdk-l04g2-fa656ae233.json');
$database = $firebase->getDatabase();

$reference = $database->getReference('object/Line_contact');
//$reference = $database->getReference('line');
//$value = $database->getReference('line')->getChildKeys();
//$value = $reference->getChildKeys(); 
$data = $reference->getValue(); 

$filter = 'Ub1c272947e6de856751d7142334b88ca1';

foreach($data as $value){
    if($filter==$value['line_id']){$duplicate = true;}
}

if($duplicate){
    echo "Have already";
}

//$snapshot = $reference->orderByChild("line_id")->equalTo('Ub1c272947e6de86751d7142334b88ca1')->getSnapshot();
//$snapshot = $reference->orderByChild("line_id")->equalTo($filter)->getSnapshot();

//$value = $snapshot->getValue();

//$value = $reference->orderByChild("line_id")->equalTo("Ub1c272947e6de86751d7142334b88ca1")->getValue();
//$value = $reference->orderByChild("line_id")->equalTo(123)->getSnapshot();

//echo json_encode($value);
/*
$reference->push([
        'title' => 'Post title',
        'body' => 'This should probably be longer.'
    ]);
*/
?>