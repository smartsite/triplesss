<?php

require '../model/user.php';
require '../model/repository.php';

use Triplesss\user\User;

/**
 *   Send a passsword reset link
 *    
**/

header('Content-Type: application/json');

$postObj = json_decode($content, true);

$from =  $postObj['from'];
$username = $postObj['username'];
$userid = $postObj['userid'];

$user = new User();
if($userid > 0) {
    $user->setUserId($userid);
    $username = $user->getName();
}

$email = $user->sendResetLink($username, $from);

if($email['sent'] === true) {
    echo json_encode(['link' => $email]);
} else {
    echo json_encode(['error' => 'Reset failed']);
}