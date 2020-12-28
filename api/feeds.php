<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../model/auth.php';
require '../model/user.php';

use Triplesss\user\User as User;

header('Content-Type: application/json');

isset($_GET['userid']) ? $userid = $_GET['userid'] : $userid = -1;

$user = new User();
$user->setUserId($userid);

// this owner's feeds
$feeds = $user->getFeeds();
echo json_encode($feeds);