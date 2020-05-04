<?php

require '../model/auth.php';
require '../model/image.php';
require '../model/text.php';
require '../model/post.php';
require '../model/content.php';
require '../model/repository.php';

use Triplesss\repository\Repository as Repository;
use Triplesss\post\Post as Post;
use Triplesss\content\Content as Content;
use Triplesss\visibility\Visibility as Visibility;

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
$basefolder = $postObj->basefolder;

$post = new Post($user_id);
$post->setContentType('text');

$postContent = new Content();
$postContent->setUserId($user_id);
$postContent->setContentType('text');
$postContent->setContent($txt);
$postContent->write();
$post->addContent($postContent);

$postContent = new Content();
$postContent->setUserId($user_id);
$postContent->setContentType('image');
$postContent->setContent($im);
$postContent->write();
$post->addContent($postContent);

$content = $post->getContent();

echo json_encode(['text' => $textContent, 'image' => $imageContent]);
