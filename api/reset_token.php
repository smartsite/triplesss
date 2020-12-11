<?php

require '../model/user.php';
require '../model/repository.php';

use Triplesss\user\User;

/**
 *   Check a password reset token. 
 *   
 *   TODO: make this generic by adding a token_type paramater,
 *   also rewrite the verify endpoint to use the same code
 *    
**/

header('Content-Type: application/json');

isset($_GET['token']) ? $token = $_GET['token'] : $token = '';
isset($_GET['userid']) ? $user_id = $_GET['userid'] : $user_id = '';

$user = new User();
$user->setUserId($user_id);
$valid = $user->passwordToken($token);
echo json_encode(['valid' => $valid]);