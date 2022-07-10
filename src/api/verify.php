<?php

require '../model/user.php';
require '../model/repository.php';

use Triplesss\user\User;

/**
 *   Verify a newly registered user account
 *    
**/

header('Content-Type: application/json');

isset($_GET['key']) ? $key = $_GET['key'] : $key = false;

$user = new User();
$u = $user->verify($key);

if($u) {
    $userid = $user->getUserId();
    $username = $user->getName();
    echo json_encode(['userid' => $userid, 'username' => $username]);
} else {
    echo json_encode(['error' => 'Verification failed']);
}