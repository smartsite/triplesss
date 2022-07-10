<?php

/**
 *   A Feed is a collection of posts, which may be filtered and  /or sorted
 *   A user owns  a feed, feeds from multiple users are combined using an aggregator
 *     
 */

require '../model/auth.php';
require '../model/user.php';
require '../model/feed.php';
require '../model/filter.php';

use Triplesss\user\User as User;
use Triplesss\feed\Feed as Feed;
use Triplesss\filter\Filter as Filter;

header('Content-Type: application/json');

$sort_by = 'date_desc';

if(isset($_GET)) {
    extract($_GET);     
} else {
    $content = trim(file_get_contents("php://input"));
    $postObj = json_decode($content, true);
    extract( $postObj);   
}

if(!$offset) {
    $offset = 0;
}

if(!$count) {
    $count = 10;
}

$feed = new Feed();
$feed->setId($feed_id);
$filter = new Filter($filter_options = []);
$filter->setType('range');
$filter->setRange($offset, (int)($count) + (int)($offset));
$feed->setFilter($filter);
$feed->setPostRange([$offset, $count]);
$feed->sortBy($sort_by);
$ag = $feed->getPosts();

$posts = array_filter(array_map(function($u) {
    $fu = new User();
    $fu->setUserId($u[0]['owner']);
    $u[0]['avatar'] = $fu->getAvatar();      
    return $u;
}, $ag));

echo json_encode($posts);
