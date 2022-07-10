<?php

/**
 *   Any reaction to a post - like, unlike, meh... is handled here.
 *   This endpoint should also let a user remove their reaction, but it doesn't yet!
 */


require '../model/auth.php';
require '../model/post.php';
require '../model/user.php';
require '../model/content.php';
require '../model/reaction.php';
require '../model/notification.php';

use Triplesss\user\User as User;
use Triplesss\post\Post as Post;
use Triplesss\reaction\Reaction as Reaction;
use Triplesss\notification\Notification;

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

$user_id = $postObj->userid;
$post_id = $postObj->postid;
$level = $postObj->level;

$post2 = new Post(0);
$post2->setPostId($post_id);
$owner = $post2->getOwnerFull();

$owner_user = new User();
$owner_user->setUserId($owner['id']);

$user = new User();
$user->setUserId($user_id);

$post = new Post($user_id);
$post->postId = $post_id;
$reaction = new Reaction($level, $user);

$post->addReaction($reaction);
$reactions =  $post->getReactions();

$notification = new Notification($owner_user);
$notification->setFromUser($user);
$notification->setPostId($post_id);
$notification->setType('reaction');
$notification->notify();

echo json_encode(['reactions' => $reactions]);