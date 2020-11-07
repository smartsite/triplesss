<?php

require '../model/auth.php';
require '../model/tags.php';
require '../model/post.php';
require '../model/content.php';

use Triplesss\post\Post as Post;
use Triplesss\tag\Tag as Tag;
use Triplesss\content\Content as Content;


/**
 *   Get or set tags
 *      
 */


header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

if($content != '') {
    // we are setting
    $tags = [];
    if( explode(' ', $postObj->tags)) {
        $tags = explode(' ', $postObj->tags);
    } else {
        $tags[] = $postObj->tags;
    }
    $post_id = $postObj->post_id;
    $user_id = $postObj->user_id;
    $post = new Post($user_id);
    $post->postId = $post_id;
    $tagObj =  new Tag();
    array_map(function($t) use(&$tagObj) {
        $tag = str_replace('#', '', $t);
        $tag = trim($tag);
        $tagObj->add($tag);
    }, $tags);

    $post->setTags($tagObj);
    $t = $post->saveTags();
    echo json_encode($t);
    
    //$post->addTag($tagObj);
} else {
    $post_id = $_GET['post_id'];
    $user_id = $_GET['user_id'];
    $post = new Post($user_id);
    $post->postId = $post_id;
    $items = $post->getItems();
    $tags = array_filter(array_map(function($item) {
        return $item['tags'];
    }, $items));
    //var_dump($tags);
    count($tags) > 0 ? $t = $tags[0] : $t = [];
    echo json_encode(['tags' => $t]);
}







//echo json_encode(['text' => $text]);


