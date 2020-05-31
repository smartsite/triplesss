<?php

require '../model/auth.php';
require '../model/feed.php';
require '../model/filter.php';
require '../model/repository.php';

use Triplesss\feed\Feed as Feed;
use Triplesss\filter\Filter as Filter;


/**
 *   A Feed is a collection of posts, which may be filtered and  /or sorted
 *     
 */


header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

$feed_id = $postObj->feed_id;
$filter_options = $postObj->filter_options;
$sort_by = $postObj->sort_by;
$count = $postObj->count;
$offset = $postObj->offset;

$feed = new Feed();
$feed->setId($feed_id);

$filter = new Filter($options);
$feed->setFilter($filter);
$feed->sortBy($sort_by);
$feed->setPostRange($offset, $count);
$posts = $feed->getPosts();
echo json_encode($posts);