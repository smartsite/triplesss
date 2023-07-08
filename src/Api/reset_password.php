<?php

require '../model/user.php';
require '../model/repository.php';

use Triplesss\user\User;

/**
 *   Reset a user's password. Must have a valid token from a reset request.
 *    
**/

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

$user_id = $postObj->userid;
$key = $postObj->key;
$password = $postObj->password;

$success = false;

if($userid > 0) {
    $user = new User();
    $user->setUserId($userid);
    $username = $user->getName();
    $valid = $user->passwordToken($key);
    $pwd = md5($password);
    if($valid === true) {
        $success = $user->update(['user_name' => $username, 'password' => $pwd]);        
    } else {
        $result = ['error' => 'invalid token'];
    }
    $result = ['success' => $success];
} else {
    $result = ['error' => 'Unknown user']; 
}

echo json_encode($result);