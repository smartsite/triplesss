<?php

require '../model/auth.php';
require '../model/post.php';
require '../model/visibility.php';

use Triplesss\post\Post as Post;
use Triplesss\visibility\Visibility;

/**
 *   Sets the visibility of a post. Non-zero levels are pre-defined and can  
 *   be customised, but assume setting visibility to 0 = private ( user only can see ),
 *   < 0 is hidden / deleted ( nobody except admin can see ). Could be improved
 *   so that -1 = hidden, and < -1 is not visible to admins and marked for deletion from storage
 */


header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

$post_id = $postObj->post_id;
$user_id = $postObj->user_id;
$visibility = $postObj->level;

$post = new Post($user_id);
$post->setId($post_id); 

$v = new Visibility();
$v->setLevel($visibility);
$del = $post->setVisibility($v, $post_id);

echo json_encode($del);