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
          
    
    function __construct() {
        $this->repository = new Repository();
    }

    function setId($id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }

    function setUserId(Int $user_id) {
        $this->userId = $user_id;
    }

    function getUserId() : Int {
        return $this->user_id;
    }

    function setContentType($contentype) {
        $this->contentType = $contentype;
    }

    function getContentType() {
        return $this->contentType;
    }

    function setContent($content) {
        $this->content = $content;        
    }

    function getContent(Int $id) {
        
        //return $this->content;
    }

    function write() {
        if($this->contentType == 'image') {
            $im = new Image();
            $im->setBaseFolder('../storage');
            $im->setUserId($this->userId);
            $stored = $im->add($this->content);
            return $this->repository->imageAdd($stored['name'], $stored['folder'], $stored['type'], $stored['mime_type'], $this->userId, '');           
        }

        if($this->contentType == 'text') {
            $txt = new Text();
            $txt->setText($this->content);
            return $this->repository->textAdd($this->content, $this->userId, '');           
        }
    }


}