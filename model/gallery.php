<?php
namespace gallery;

class Gallery extends Image {
    
    Static $id = -1;
    Static $images = [];
       
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