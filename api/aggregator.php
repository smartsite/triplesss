<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../model/auth.php';
require '../model/user.php';
require '../model/feed.php';
require '../model/filter.php';
require '../model/collection.php';
require '../model/aggregator.php';

use Triplesss\filter\Filter;
use Triplesss\collection\Aggregator;
use Triplesss\user\User;


/**
 *   An aggregator merges feeds from different users together,
 *   and allows the user to sort and / or filter the posts on
 *   those feeds. This is the guts of Triplesss, it's what makes
 *   it unique among social media platforms!  
 */


header('Content-Type: application/json');

if(isset($_GET)) {
    extract($_GET);     
} else {
    $content = trim(file_get_contents("php://input"));
    $postObj = json_decode($content, true);
    extract( $postObj);   
}

$filter_options = '';
$filter = new Filter($filter_options);

$posts = [];

if($userid > 0) {
    $aggregator = new Aggregator();
    $aggregator->setUserId($userid);
    $aggregator->setFilter($filter);
    $ag = $aggregator->getPosts();

    $posts = array_filter(array_map(function($u) {
        $fu = new User();
        $fu->setUserId($u[0]['owner']);
        $u[0]['avatar'] = $fu->getAvatar();   
        return $u;
    }, $ag));
}

echo json_encode($posts, true);
