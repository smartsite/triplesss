<?php

require '../../../../vendor/autoload.php';

require '../model/auth.php';
use Triplesss\auth\Auth;
use ReallySimpleJWT\Token;

/**
 *   Log a user in
 *    
**/

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
//$postObj = $_POST;
$postObj = json_decode($content, true);

$tok = new Token();

$auth = new Auth($tok);
$password = $postObj['password'];
$username = $postObj['username'];

$login = $auth->login($username, $password, true);
echo json_encode($login);