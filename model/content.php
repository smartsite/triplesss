<?php
namespace  Triplesss\content;

//require_once('image.php');
//require_once('text.php');

use Triplesss\text\Text as Text;
use Triplesss\image\Image as Image;
use Triplesss\repository\Repository as Repository;

class content {
    
    Public $id = 0;
    Public $contentType;
    Public $content;
    Public $repository;
    Public $tags;
    Public $userId;
    Public $baseFolder;
    Public $contentId;
          
    
    function __construct() {
        $this->repository = new Repository();
    }

    Public function setId($id) {
        $this->id = $id;
    }

    Public function getId() :Int {
        return $this->id;
    }

    Public function setUserId(Int $user_id) {
        $this->userId = $user_id;
    }

    Public function getUserId() : Int {
        return $this->user_id;
    }

    Public function setContentType($contentype) {
        $this->contentType = $contentype;
    }

    Public function getContentId() : Int{
        $idArray = $this->contentId;
        return $idArray['id'];
    }

    Public function setBaseFolder(String $folder) {
        $this->baseFolder = $folder;
    }

    Public function getContentType() {
        return $this->contentType;
    }

    Public function setContent($content) {
        $this->content = $content;        
    }

    Public function getContent(Int $id) {
        
        //return $this->content;
    }

    Public function write() {
        $id = -1;
        if($this->contentType == 'image') {
            $im = new Image();
            $im->setBaseFolder($this->baseFolder);
            $im->setUserId($this->userId);
            $stored = $im->add($this->content);
            // This is the database insert ID
            $id = $this->repository->imageAdd($stored['name'], $stored['folder'], $stored['type'], $stored['mime_type'], $this->userId, '');           
        }

        if($this->contentType == 'text') {
            $txt = new Text();
            $txt->setText($this->content);
            // This is the database insert ID
            $id = $this->repository->textAdd($this->content, $this->userId, '');           
        }
        $this->contentId = $id;
        return $id;
    }


}