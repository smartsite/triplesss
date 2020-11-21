<?php

require '../model/auth.php';
use Triplesss\auth\Auth;

/**
 *   Check logged in status. This is mainly for UX, not to be used for security!
 *    
**/

header('Content-Type: application/json');

$auth = new Auth();
$logged_in = $auth->isLoggedIn();
echo json_encode($logged_in);
