<?php
namespace Triplesss\gallery;

use Triplesss\image\Image as Image; 

class Gallery extends Image {
    
    Public $id = -1;
    Public $images = [];
       
    function __construct() {
       
    }

    function setId(Int $id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }

    function addImage(Image $image) {
        array_push($this->images, $image);
    }

    function removeImage(Int $id) {
       unset($this->images[$id]);
    }

    function getImages() {
        return $this->images;
    }

    function getCount() :Int {
        return count($this->images);
    }   
}