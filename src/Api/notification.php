<?php

require '../model/auth.php';
require '../model/notification.php';
require '../model/feed.php';
require '../model/user.php';

use Triplesss\auth\Auth;
use Triplesss\user\User;
use Triplesss\notification\Notification;

/**
 *   Send a notification from one user to another
 *   Includes system messages like warnings or errors,
 *   also reports to admins 
 */

header('Content-Type: application/json');

$message = false;

if(isset($_GET)) {
    extract($_GET);     
} else {
    $content = trim(file_get_contents("php://input"));
    $postObj = json_decode($content, true);
    extract( $postObj);   
}

$from_user = new User();
$from_user->setUserId($to_user_id);

$to_user = new User();
$to_user->setUserId($from_user_id);

$notification = new Notification($from_user);
$notification->setFromUser($to_user);
$notification->setPostId($post_id);
$notification->setType($action);
if($message) {
    $notification->setMessage($message);
}
$notification->notify();

echo json_encode($notification);