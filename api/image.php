<?php

//require_once('auth.php');
require_once('../model/image.php');
use Triplesss\image\Image as Image;

//header('Content-Type: application/json');

//$post = $_POST;
//$get = $_GET;

// GET to read, POST to upload

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

$img =  new Image();
$img->setUserId($postObj->userid);
$img->setBaseFolder($postObj->basefolder);
$im = $postObj->image;

$img->add($im);

return json_encode($img);