<?php

require '../model/auth.php';
require '../model/image.php';
require '../model/text.php';
require '../model/post.php';
require '../model/feed.php';
require '../model/content.php';
require '../model/visibility.php';
require '../model/repository.php';


use Triplesss\feed\Feed as Feed;
use Triplesss\post\Post as Post;
use Triplesss\content\Content as Content;
use Triplesss\visibility\Visibility;

//use Triplesss\visibility\Visibility as Visibility;

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

$p1 = $post;

$postContent = new Content();
$postContent->setUserId($user_id);
$postContent->setContentType('text');
$postContent->setContent($txt);
$postContent->write();
$post->addContent($postContent);
//$textContent = $postContent;

$postContent = new Content();
$postContent->setUserId($user_id);
$postContent->setBaseFolder($basefolder);
$postContent->setContentType('image');
$postContent->setContent($im);
$postContent->write();
$post->addContent($postContent);

$v = new Visibility();
$v->setLevel(0);
$post->setVisibility($v);

$id = $post->add();
$feed->addPost($post);
echo json_encode(['postId' => $id]);
//$imageContent = $postContent;

//$content = $post->getContent();
//echo json_encode($content);
//echo json_encode($image_id);
