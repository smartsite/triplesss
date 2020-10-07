<?php

//ini_set('display_errors', 1);
//error_reporting(E_ALL);

require '../model/user.php';
require '../model/repository.php';

use Triplesss\user\User;

/**
 *   Register a new user
 *    
**/

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content, true);

$citysplit = explode("|", $postObj['city']);
$postObj['city'] = $citysplit[0]; 
$postObj['postcode'] = $citysplit[1]; 

$user = new User();
//$u = $user->update($postObj);

$username = $postObj['user_name'];
$user->add($username);
$u = $user->update($postObj);
$from = 'webmaster@surfsouthoz.com';
$reply = 'register@surfsouthoz.com';
$user->sendRegisterLink($username, $from, $reply);

if($u === true) {
    echo json_encode(['userid' => $user->getUserId()]);
} else {
    echo json_encode(['error' => 'Registraion failed']);
}



/*
$username = $postObj['username'];
$user = new User();
$u = $user->add($username);

if(gettype($u) == "object") {
    // $u = user_id 
   unset($postObj['username']);
    $user->update($details);
} else {
    $user = ['userid' => $u];
    echo json_encode($u);
}
*/
