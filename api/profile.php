<?php

require '../model/auth.php';
require '../model/user.php';
require '../model/feed.php';
require '../model/filter.php';

use Triplesss\user\User as User;
use Triplesss\feed\Feed as Feed;
use Triplesss\filter\Filter as Filter;

/**
 *   Whatever a user posts to feed_id = 0 becomes a profile
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
$feed->setId(0); // the user profile feed

$filter = new Filter();
$filter->setType('userid');
$filter->setUserid($userid);
$feed->setFilter($filter);

$posts = $feed->getFilteredPosts();
if(!$posts) {
    $user = new User();
    $user->setUserId($userid);
    $username = $user->getName();
    $posts[0] = ['owner' => $userid, 'user_name' => $username];
    $posts[1] = ['owner' => $userid, 'user_name' => $username];
}

echo json_encode($posts);