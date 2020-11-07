<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../model/auth.php';
require '../model/filter.php';
require '../model/notification.php';
require '../model/feed.php';
require '../model/user.php';

use Triplesss\auth\Auth;
use Triplesss\user\User;
use Triplesss\notification\Notification;


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
$n = $user->getNotifications();

$notifications = array_filter(array_map(function($u) {
    $fu = new User();
    $fu->setUserId($u['user_id']);
    $u['avatar'] = $fu->getAvatar();   
    return $u;
}, $n));

echo json_encode($notifications);