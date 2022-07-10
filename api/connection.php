<?php

require '../model/auth.php';
require '../model/connection.php';
require '../model/user.php';
require '../model/notification.php';

use Triplesss\auth\Auth;
use Triplesss\connection\Connection;
use Triplesss\user\User;
use Triplesss\notification\Notification;

/**
 *   Create or break a connection to a user
 *
 *   At this stage, actions are:
 *
 *   0 - disconnect
 *   1 - follow
 *   2 - friend / approve request  
 *   9 - request
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

$type = 0; // same as "unfollow"
if($action == 'follow') {$type = 1;}
if($action == 'request') {$type = 9;}
if($action == 'connect') {$type = 9;}
if($action == 'accept') {$type = 2;}


$connection = new Connection();
$connection->setType($type);

$from_user = new User();
$from_user->setUserId($from);

$to_user = new User();
$to_user->setUserId($to);

$connection->connect($from_user, $to_user, $type);

$notification = new Notification($to_user);
$notification->setFromUser($from_user);
$notification->setPostId(0); // This is unrelated to any post!

if($action == 'accept' || $action == 'request' ) {
    $notification = new Notification($from_user);
    $notification->setFromUser($to_user);
}

$notification->setType($action);
$notification->notify();

echo json_encode($connection);