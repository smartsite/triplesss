<?php
namespace  Triplesss\post;

class Comment extends Post {
    
    Static $id = 0;
    Static $text = '';
    Static $image = '';          
    
    function __construct($content) {
        $this->Content = $content;
    }

    function setId($id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }

    function setText($text) {
        $this->text = $text;
    }

    function setImage($image) {
        $this->image = $image;
    }

    function getComment(){
        return $this->text. " " . $this->image;
    }

    

}