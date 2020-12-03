<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../model/user.php';
require '../model/member.php';
require '../model/repository.php';

use Triplesss\user\User;
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

//isset($_POST['description']) ? $description = $_POST['description'] : $description = 'VIP member';
//isset($_POST['name']) ? $name = $_POST['name'] : $name = '';
//isset($_POST['email']) ? $email = $_POST['email'] : $email = '';
//isset($_POST['user_name']) ? $user_name = $_POST['user_name'] : $user_name = '';

if($member_id) {
    // GET and existing user
    $member = new Member();
    $member->setId($member_id);
    $details = $member->getDetails(true);   
} else if($postObj->user_id) {
    // create a new user
    $member = new Member();
    $member->setUserId($postObj->user_id);
    $member_id = $member->create();
    $member->setId($member_id);
    $details = $member->getDetails(true);   
}

echo json_encode($details);
exit();