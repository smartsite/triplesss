<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../model/auth.php';
require '../model/image.php';
require '../model/text.php';
require '../model/post.php';
require '../model/content.php';
require '../model/comment.php';
require '../model/visibility.php';

use Triplesss\content\Content as Content;
use Triplesss\post\Comment as Comment;
use Triplesss\visibility\Visibility;

/**
 *   Get all the comments associated with a post
 * 
 */

header('Content-Type: application/json');
$comments = [];

//$user_id = $postObj->userid;
$user_id = 0;
if(isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $comment = new Comment($user_id);
    $comment->setParentId($post_id);

    // should return the comment count!
    $comments = $comment->getAll();
}

echo json_encode(['count' => count($comments), 'comments' => $comments]);