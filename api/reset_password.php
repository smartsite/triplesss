<?php

require '../model/user.php';
require '../model/repository.php';

use Triplesss\user\User;

/**
 *   Reset a user's password. Must have a valid token from a reset request.
 *    
**/

header('Content-Type: application/json');

isset($_GET['password']) ? $password = $_GET['password'] : $password = '';
isset($_GET['key']) ? $key = $_GET['key'] : $key = '';
isset($_GET['userid']) ? $userid = $_GET['userid'] : $userid = -1;

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