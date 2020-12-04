<?php

require '../model/auth.php';
require '../model/user.php';
require '../model/users.php';
require '../model/filter.php';
require '../model/feed.php';

use Triplesss\filter\Filter;
use Triplesss\user\User;
use Triplesss\users\Users;

/**
 *   Return matches a user search
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
if(!$userid) {
    $userid = -1;
}

$users = new Users();
$user = new User();
$user->setUserId($userid);
$user->getConnections();

$filter = new Filter();
$filter->setType('like');
$filter->setUserName($username);
$filter->setUserId($userid);
$users->setFilter($filter);

// Get all the users matching this search
$users = array_filter(array_map(function($user) {
    unset($user['first_name']);
    unset($user['last_name']);    
    return $user; 
}, $users->getUsers()));

// Then work out if any of those users are friends
$cu = array_filter(array_map(function($u) use ($user) {
    $u['connection_type'] = '';
    $conn = $user->getConnection($u['id']);
    $fu = new User();
    $fu->setUserId($u['id']);
    $u['avatar'] = $fu->getAvatar();
    if(is_array($conn)) {
        
        $cn = array_values($conn);
        if(array_key_exists(0, $cn)) {
            
            $u['connection_type'] = $cn[0]['relation'];
        } else {
            $u['connection_type'] = '';
        }
       
    }
    return $u;
}, $users));

echo json_encode($cu);