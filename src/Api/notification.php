<?php

/**
 *   Send a notification from one user to another
 *   Includes system messages like warnings or errors,
 *   also reports to admins 
 */

require '../model/auth.php';
require '../model/notification.php';
require '../model/feed.php';
require '../model/user.php';

use Triplesss\auth\Auth;
use Triplesss\user\User;
use Triplesss\post\Post;
use Triplesss\notification\Notification;

header('Content-Type: application/json');

$message = false;

if(isset($_GET)) {
    extract($_GET);     
} else {
    $content = trim(file_get_contents("php://input"));
    $postObj = json_decode($content, true);
    extract( $postObj);   
}

$post = new Post(0); // this is a trick to force  a post lookup
$post->setPostId($post_id);
$post_owner =  $post->getOwner();

$from_user = new User();
$from_user->setUserId($to_user_id);

if($to_user = -1) {
    // assume the to_user is the post owner
    $to_user = $post_owner;
}

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