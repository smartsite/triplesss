<?php

require '../model/user.php';
require '../model/connection.php';
require '../model/repository.php';
require '../model/feed.php';

use Triplesss\user\User;
use Triplesss\connection\Connection;
use Triplesss\feed\Feed;

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

// the id of the main news account owner. This will show as the only source in the new user's feed
isset($postObj['follow_id']) ? $to = $postObj['follow_id'] : $to = 2;

$user = new User();
//$u = $user->update($postObj);

$username = $postObj['user_name'];
$user->add($username);


// yes, these need to be paramaters, of course!
$from = $postObj['from_email'];
$reply = $postObj['reply_email'];

unset($postObj['from_email']);
unset($postObj['reply_email']);

$u = $user->update($postObj);

$user->sendRegisterLink($username, $from, $reply);

if($u === true) {
    // follow the news account / main user
    $user_id = $user->getUserId();    
    $feed = new Feed();
    $feed->new($user_id, 'User feed', ucfirst($username)."'s feed");

    $connection = new Connection();
    $connection->setType(1); // type 1 = Follow
    $to_user = new User();
    $to_user->setUserId($to);
    $user->setUserId($user_id);
    $connection->connect($user, $to_user, 1);

    echo json_encode(['userid' => $user_id]);
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
