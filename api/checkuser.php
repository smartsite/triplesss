<?php

require '../model/user.php';
require '../model/repository.php';

use Triplesss\user\User;

/**
 *   Check is a username is available
 *    
**/

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

$username = $postObj->username;
$user = new User();

$u = $user->checkUserName($username);
if(gettype($u) == "object") {
    echo json_encode($u); 
} else {
    $user = ['userid' => $u];
    echo json_encode($u);
}
