<?php

// It's possible we might be able to do this with the Feed endpoint!


ini_set('display_errors', 1);
error_reporting(E_ALL);


require '../model/auth.php';
require '../model/feed.php';
require '../model/filter.php';
//require '../model/repository.php';

use Triplesss\feed\Feed as Feed;
use Triplesss\filter\Filter as Filter;


/**
 *   Whatever a user posts to feed_id = 0 becomes a profile.
 *   Since the sort order is descending date by default, we only need the last one!
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
$feed->setId(0);


$filter = new Filter();
$filter->setType('userid');
$filter->setUserid($userid);
$feed->setFilter($filter);

$posts = $feed->getFilteredPosts();
if(!$posts) {
    $posts[0] = null;
    $posts[1] = null;
}

echo json_encode($posts);
