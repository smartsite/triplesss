<?php

/**
 *   A Post is an object containing AT LEAST one Content object. 
 *   Content objects can be images or text, so a Post can contain either,
 *   or both of these. A simple Post object contains a single text Content object. 
 * 
 *   A single image with a caption is represented by a Post object with 
 *   one text type Content object and one Image type content object.
 * 
 *   TODO: add / remove images from a post ( hard! )
 * 
 */

require '../model/auth.php';
require '../model/image.php';
require '../model/text.php';
require '../model/post.php';
require '../model/content.php';

use Triplesss\post\Post as Post;
use Triplesss\content\Content as Content;

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

$txt = $postObj->text;
$post_id = $postObj->post_id;
$user_id = $postObj->user_id;

$post = new Post($user_id);
$post->setContentType('text');

if($txt != '') {
    $text = $post->updateContent($post_id, $txt);
} else {
    // not updated, so just return the same text
    $text = $txt;
}

echo json_encode(['text' => $text]);