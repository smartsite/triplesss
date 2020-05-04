<?php


require_once('../model/image.php');
use Triplesss\image\Image as Image;
use Triplesss\visibility as Visibility;

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);

$img =  new Image();
$img->setUserId($postObj->userid);
$img->setBaseFolder($postObj->basefolder);
$im = $postObj->image;

$img->add($im);

return json_encode($img);