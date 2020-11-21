<?php

require '../model/auth.php';
use Triplesss\auth\Auth;

/**
 *   Log a user out
 *    
**/

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content, true);

$auth = new Auth();

$logout = $auth->logout();
echo json_encode($logout);