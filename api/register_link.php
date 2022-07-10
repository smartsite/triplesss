<?php

/**
 *   send or re-send a registration link
 *    
**/

require '../model/user.php';
require '../model/connection.php';
require '../model/repository.php';
require '../model/feed.php';

use Triplesss\user\User;

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
isset($_GET['from_email']) ? $from = $_GET['from_email'] : $from = '';
isset($_GET['reply_email']) ? $reply = $_GET['reply_email'] : $reply = '';
isset($_GET['user_id']) ? $user_id = $_GET['user_id'] : $user_id = '';

$user = new User();
$user->setUserId($user_id);
$user_name = $user->getName();

$mail = $user->sendRegisterLink($user_name, $from, $reply);

if($mail) {
    $email = $mail['email'];
    echo json_encode(['email' => $email]);
} else {
    echo json_encode(['error' => 'Sending Registration link failed']);
}