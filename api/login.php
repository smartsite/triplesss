<?php

/**
 *   Log a user in
 *    
**/

require '../model/auth.php';
use Triplesss\auth\Auth;

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content, true);

$auth = new Auth();
$password = $postObj['password'];
$username = $postObj['username'];

$login = $auth->login($username, $password, true);
echo json_encode($login);