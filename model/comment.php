<?php
namespace  Triplesss\post;

class Comment extends Post {
    
    Public $id = 0;
    Public $text = '';
    Public $image = '';          
    
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