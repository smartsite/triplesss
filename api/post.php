<?php

require '../model/auth.php';
require '../model/user.php';
require '../model/image.php';
require '../model/text.php';
require '../model/post.php';
require '../model/feed.php';
require '../model/content.php';
require '../model/visibility.php';
require '../model/notification.php';
//require '../model/repository.php';


use Triplesss\feed\Feed as Feed;
use Triplesss\post\Post as Post;
use Triplesss\user\User;
use Triplesss\content\Content as Content;
use Triplesss\visibility\Visibility;
use Triplesss\notification\Notification;


/**
 *   A Post is an object containing AT LEAST one Content object. 
 *   Content objects can be images or text, so a Post can contain either,
 *   or both of these. A simple Post object contains a single text Content object. 
 * 
 *   A single image with a caption is represented by a Post object with 
 *   one text type Content object and one Image type content object.
 * 
 */


header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

$im = $postObj->image;
$txt = $postObj->comment;
$user_id = $postObj->userid;
$feed_id = $postObj->feedid;
$basefolder = $postObj->basefolder;

$feed = new Feed();
$feed->setId($feed_id);
$post = new Post($user_id);
$post->setContentType('text');

$user = new User();
$user->setUserId($user_id);

$p1 = $post;

if($txt != '') {
    $postContent = new Content();
    $postContent->setUserId($user_id);
    $postContent->setContentType('text');
    $postContent->setContent($txt);
    $postContent->write();
    $post->addContent($postContent);
}

if($im != '') {
    $maxWidth=1024;
    $maxHeight=640;
    $postContent = new Content();
    $postContent->setUserId($user_id);
    $postContent->setBaseFolder($basefolder);
    $postContent->setContentType('image');
    $postContent->setContent($im);
    $postContent->setImageConstraints($maxWidth, $maxHeight);
    $postContent->write();
    $post->addContent($postContent);
}

$id = $post->add();
$v = new Visibility();
$v->setLevel(0);
$post->setVisibility($v, $id);

$notification = new Notification($user);
$notification->setType('post');
$notification->setPostId($id);
$notification->notify();

$feed->addPost($post);
echo json_encode(['postId' => $id]);