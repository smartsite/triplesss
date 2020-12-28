<?php

require '../model/user.php';
require '../model/repository.php';

use Triplesss\user\User;

/**
 *   Semd a passsword reset link
 *    
**/

header('Content-Type: application/json');

isset($_GET['from']) ? $from = $_GET['from'] : $from = '';
isset($_GET['username']) ? $username = $_GET['username'] : $username = '';
isset($_GET['userid']) ? $userid = $_GET['userid'] : $userid = -1;

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