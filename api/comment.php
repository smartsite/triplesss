<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../model/auth.php';
require '../model/user.php';
require '../model/image.php';
require '../model/text.php';
require '../model/post.php';
require '../model/feed.php';
require '../model/content.php';
require '../model/comment.php';
require '../model/visibility.php';
require '../model/notification.php';
//require '../model/repository.php';


use Triplesss\user\User;
use Triplesss\feed\Feed as Feed;
use Triplesss\post\Post as Post;
use Triplesss\content\Content as Content;
use Triplesss\post\Comment as Comment;
use Triplesss\visibility\Visibility;
use Triplesss\notification\Notification;

//use Triplesss\visibility\Visibility as Visibility;

/**
 *   A Post is an object containing AT LEAST one Content object. 
 *   Content objects can be images or text, so a Post can contain either,
 *   or both of these. A simple Post object contains a single text Content object. 
 * 
 *   A comment is just a post with a post as a parent!
 * 
 */


header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

//$im = $postObj->image; // Only allow text for now!
$im = null;
$txt = $postObj->comment;
$user_id = $postObj->userid;
$feed_id = $postObj->feedid;
$post_id = $postObj->postid;
//$basefolder = $postObj->basefolder;
$basefolder = '';

$post2 = new Post(0);
$post2->setPostId($post_id);
$owner = $post2->getOwnerFull();

$owner_user = new User();
$owner_user->setUserId($owner['id']);

$user = new User();
$user->setUserId($user_id);

$comment = new Comment($user_id);
$comment->setContentType('text');
$comment->setParentId($post_id);

if($txt != '') {
    $postContent = new Content();
    $postContent->setUserId($user_id);
    $postContent->setContentType('text');
    $postContent->setContent($txt);
    $postContent->write();
    $comment->addContent($postContent);
}

/*
if($im != '') {
    $postContent = new Content();
    $postContent->setUserId($user_id);
    $postContent->setBaseFolder($basefolder);
    $postContent->setContentType('image');
    $postContent->setContent($im);
    $postContent->write();
    $comment->addContent($postContent);
}
*/

$v = new Visibility(); // initially, EVERYONE can see comments... which is probably not the best
$v->setLevel(0);
$comment->visibility = $v;
$id = $comment->add();


$notification = new Notification($owner_user);
$notification->setFromUser($user);

$notification->setType('comment');
$notification->notify();



// should return the comment count!
$comments = $comment->getAll();

echo json_encode(['count' => count($comments), 'comments' => $comments]);