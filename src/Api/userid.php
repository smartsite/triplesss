<?php

require '../model/auth.php';
require '../model/user.php';

use Triplesss\user\User as User;

/**
 *   Just returns a user id from end of a url
 */

header('Content-Type: application/json');

$a = explode('/', $_SERVER['HTTP_REFERER']);
$username = end($a);
$user =  new User();
$user->setName($username);
$user_id = $user->getUserId();
echo json_encode(['userid' => $user_id]);