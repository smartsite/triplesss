<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../model/auth.php';
require '../model/user.php';
require '../model/feed.php';
require '../model/filter.php';
require '../model/image.php';
require '../model/text.php';
require '../model/emoji.php';
require '../model/post.php';
require '../model/content.php';
require '../model/visibility.php';
require '../model/notification.php';


use Triplesss\filter\Filter as Filter;
use Triplesss\post\Post as Post;
use Triplesss\feed\Feed as Feed;
use Triplesss\text\Emoji as Emoji;
use Triplesss\user\User;
use Triplesss\content\Content as Content;
use Triplesss\visibility\Visibility;
use Triplesss\notification\Notification;
use Triplesss\repository\Repository;


/**
 *   Returns a single post by post id, checks user has permission  
 *     
 */

header('Content-Type: application/json');

$sort_by = 'date_desc';

if(isset($_GET)) {
    extract($_GET);     
} else {
    $content = trim(file_get_contents("php://input"));
    $postObj = json_decode($content, true);
    extract($postObj);   
}


$r = new Repository();
$p = $r->getPostById($post_id);

$u = new User();
$u->setUserId($p[0]['owner']);
$p[0]['avatar'] = $u->getAvatar();   

echo json_encode($p);