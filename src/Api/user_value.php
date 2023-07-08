<?php

require '../model/auth.php';
require '../model/user.php';

use Triplesss\user\User as User;

/**
 *   Set (POST) or get (GET) an arbitrary user value
 */

header('Content-Type: application/json');

$res = ['success' => 'false'];

if(isset($_GET) && count($_GET) > 0) {
    extract($_GET);  
    $user = new User();
    $user->setUserId($user_id);
    $value = $user->getValue($key);
    $res = ['user_id' => $user_id, 'key' => $key, 'value' => $value];

} else {
    $content = trim(file_get_contents("php://input"));
    $postObj = json_decode($content, true);
    extract( $postObj); 
    $user = new User();
    $user->setUserId($user_id);  
    $status = $user->setValue($key, $value);
    if($status) {
        $res = ['success' => 'true'];
    } else {
        $res = ['success' => 'false', 'error' => 'key not found'];
    }
}

echo json_encode($res);