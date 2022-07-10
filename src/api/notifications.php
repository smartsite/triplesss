<?php

/**
 *   Get this user's notifications
 *   
 */

require '../model/auth.php';
require '../model/filter.php';
require '../model/notification.php';
require '../model/feed.php';
require '../model/user.php';

use Triplesss\auth\Auth;
use Triplesss\user\User;

header('Content-Type: application/json');

if(isset($_GET)) {
    extract($_GET);     
} else {
    $content = trim(file_get_contents("php://input"));
    $postObj = json_decode($content, true);
    extract( $postObj);   
}

if(!isset($start)) {$start = 0;}
if(!isset($count)) {$count = 50;}

$notifications = [];
if(isset($userid) && $userid != "undefined") {

    $user = new User();
    $user->setUserId($userid);
    $n = $user->getNotifications($start, $count);

    $notifications = array_filter(array_map(function($u) {
        $fu = new User();
        if($u['type'] == 1 || $u['type'] == 2) {
            $fu->setUserId($u['to_user_id']);
        } else {
            $fu->setUserId($u['from_user_id']);
        }
    
        $u['avatar'] = $fu->getAvatar();  
        unset($u['password']); 
        unset($u['address_1']); 
        unset($u['address_2']);
        unset($u['city']);
        unset($u['phone']);
        unset($u['postcode']);
        unset($u['email']);
        return $u;
    }, $n));
}    

echo json_encode($notifications);