<?php

/**
 *   Sets the visibility of a post. Non-zero levels are pre-defined and can  
 *   be customised, but assume setting visibility to 0 = private ( user only can see ),
 *   < 0 is hidden / deleted ( nobody except admin can see ). Could be improved
 *   so that -1 = hidden, and < -1 is not visible to admins and marked for deletion from storage
 */


require '../model/auth.php';
require '../model/user.php';
require '../model/post.php';
require '../model/visibility.php';
require '../model/notification.php';

use Triplesss\user\User;
use Triplesss\post\Post as Post;
use Triplesss\notification\Notification;
use Triplesss\visibility\Visibility;

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

$post_id = $postObj->post_id;
$user_id = $postObj->user_id;
$visibility = $postObj->level;
$is_admin = $postObj->is_admin;

$post = new Post($user_id);
$post->setPostId($post_id);
$post_items = $post->getItems();
$one_item = $post_items[0]; 
$post_owner = $one_item['owner'];

// send a notification if an admin deletes a user's post
if($is_admin) {
    $to_user = new User();
    $to_user->setUserId($post_owner);
    $notification = new Notification($to_user);
    $notification->setPostId($post_id);
    $from_user = new User();
    $from_user->setUserId(1); // Admin user
    $notification->setFromUser($from_user);
    $notification->setType('admin_deleted');
    $notification->notify();
}

$v = new Visibility();
$v->setLevel($visibility);
$del = $post->setVisibility($v, $post_id);

echo json_encode($del);