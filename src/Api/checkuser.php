<?php

require '../model/user.php';
require '../model/repository.php';

use Triplesss\user\User;

/**
 *   Check is a username is available
 *    
**/

header('Content-Type: application/json');
isset($_GET['username']) ? $username = $_GET['username'] : $username = "";
$user = new User();

$u = $user->checkUserName($username);
$uObj = new stdclass;
$uObj->username = $username;
$uObj->available = !$u;
echo json_encode($uObj);