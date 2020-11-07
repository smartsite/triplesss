<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../model/auth.php';

use Triplesss\auth\Auth;

/**
 *   Check logged in status. This is mainly for UX, not to be used for security!
 *    
**/

header('Content-Type: application/json');

//$content = trim(file_get_contents("php://input"));
//$postObj = json_decode($content, true);
//isset($_GET['username']) ? $username = $_GET['username'] :  $username = '';

$auth = new Auth();
$logged_in = $auth->isLoggedIn();

//echo json_encode(['logged_in' => $logged_in]);
echo json_encode($logged_in);
