<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../model/auth.php';
require '../model/filter.php';
require '../model/connection.php';
require '../model/feed.php';
require '../model/user.php';

use Triplesss\auth\Auth;
use Triplesss\user\User;


/**
 *   Get this user's connections to other users
 *   
 */


header('Content-Type: application/json');

if(isset($_GET)) {
    extract($_GET);     
} else {
    $content = trim(file_get_contents("php://input"));
    $postObj = json_decode($content, true);
    extract( $postObj);   
}


$user = new User();
$user->setUserId($userid);
$connections = $user->getConnections();

if(!is_null($connections[0])) {
    $cu = array_filter(array_map(function($u) {
        $fu = new User();
        $fu->setUserId($u['id']);
        $u['avatar'] = $fu->getAvatar();
        //$u['connection_type'] = 'friend';
        return $u;
    }, $connections));    
} else {
    $cu = [];
}

echo json_encode($cu);