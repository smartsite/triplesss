<?php

require '../model/auth.php';
require '../model/user.php';
use Triplesss\user\User as User;

/**
 *   Get all the reactions to posts for this user (for the last 6 months).
 * 
 *   This is a super lazy way of doing it, and the approach needs to be rethunk. 
 *   It won't scale, but even 5000 reactions would be a tiny JSON file. We can live with it for a while! 
 */

header('Content-Type: application/json');

isset($_GET['user_id']) ? $user_id = $_GET['user_id'] : $user_id = 0;

$user = new User();
$user->setUserId($user_id);
$reactions = $user->getReactions(500);

echo json_encode(['reactions' => $reactions]);