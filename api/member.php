<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../model/user.php';
require '../model/member.php';
require '../model/repository.php';

use Triplesss\user\Member;

/**
 *   Get an existing member or create one
 *    
**/

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

isset($_GET['member_id']) ? $member_id = $_GET['member_id'] : $member_id = false;
isset($_GET['user_id']) ? $user_id = $_GET['user_id'] : $user_id = false;

$member = new Member();

if($member_id) {
    // GET and existing user   
    $member->setId($member_id);
    $details = $member->getDetails(true); 
} else if($user_id) {
    $details = $member->getMemberByUserId($user_id);

} else if($postObj->user_id) {
    // create a new user   
    $member->setUserId($postObj->user_id);
    $member_id = $member->create();
    $member->setId($member_id);
    $details = $member->getDetails(true);   
}

echo json_encode($details);
exit();