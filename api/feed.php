<?php

require '../model/auth.php';
require '../model/feed.php';
require '../model/filter.php';
//require '../model/repository.php';

use Triplesss\feed\Feed as Feed;
use Triplesss\filter\Filter as Filter;


/**
 *   A Feed is a collection of posts, which may be filtered and  /or sorted
 *     
 */


header('Content-Type: application/json');

if(isset($_GET)) {
    extract($_GET);     
} else {
    $content = trim(file_get_contents("php://input"));
    $postObj = json_decode($content, true);
    extract( $postObj);   
}

$feed = new Feed();
$feed->setId($feed_id);

$filter = new Filter($filter_options);
$feed->setFilter($filter);

$feed->setPostRange([$offset, $count]);
$feed->sortBy($sort_by);

$posts = $feed->getPosts();
echo json_encode($posts);
