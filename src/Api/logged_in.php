<?php

require '../../../../vendor/autoload.php';

require '../model/auth.php';
use Triplesss\auth\Auth;
use ReallySimpleJWT\Token;

/**
 *   Check logged in status. This is mainly for UX, not to be used for security!
 *    
**/

header('Content-Type: application/json');

$token = $_GET['token'];

$tok = new Token();

$auth = new Auth($tok);

$logged_in = $auth->isLoggedIn($token);
echo json_encode($logged_in);
