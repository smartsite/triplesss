<?php

namespace Triplesss\post;

use Triplesss\repository\Repository;
use Triplesss\content\Content;
use Triplesss\image\Image;
use Triplesss\visibility\Visibility;
use Triplesss\reaction\Reaction;
use Triplesss\user\User;
use Triplesss\tag\Tag;


class Post {
    
    Public $id = 0;
    Public $postId;   
    Public $contentType;
    Public $text = '';
    Public $images = [];
    Public $link = '';
    Public $owner = null; // Alias for userId
    Public $items = [];
    Public $tags = null;
    Public $likes = 0;
    Public $comments = [];
    Public $reactions = [];
    Public $repository;
    Public $visibility;
           
    
    function __construct($owner) {
       $this->owner = $owner;
       $this->repository = new Repository();
    }

    function setId($id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }

    function getPostId() :String {
        return $this->postId;
    }

    function setPostId(String $post_id) {
        $this->postId = $post_id;
    }

    public function getOwner() : Int {
        return $this->owner;
    }

    public function getOwnerFull() :Array {
        return $this->repository->getPostOwnerById($this->postId);
    }

    function addContent(Content $content) {
        array_push($this->items, $content);
    }

    function removeContent(Content $content) {
        unset($this->posts[$content]);
    }

    function setContentType($contentype) {
        $this->contentType = $contentype;
    }

    public function getContent() {
        
        $content = [];           
        return $content;         
    }

    public function add() {
        $postId = $this->repository->addPost($this);
        $this->postId = $postId;
        return $postId;        
    }

    public function updateContent(String $post_id, String $text) { 
        /**
         *  TODO: handle more than one image :(
         */
       
        $status = $this->repository->updatePost($post_id, $text);
        return $status;
    }

    public function delete(String $post_id) { 
        $status = $this->repository->deletePost($post_id);
        return $status;
    }

    public function edit(String $text, Image $image) { 
         /**
         *  TODO: handle more than one image :(
         */
        $status = $this->repository->editPost($this, $text, $image);
        return $status;
    }

    
    public function getItems() {       
        $items = $this->repository->getPostById($this->postId);
        $this->items = $items;
        return $this->items;
    }
    

    public function setVisibility(Visibility $v, String $post_id) {
        $this->visibility = $v;
        return $this->repository->postVisibility($post_id, $v->getLevel());
    }

    public function getVisibility() : Visibility {
        return $this->visibility;
    }

    public function render() {
        // output markup for a post. Not really needed with a F/E framework.
    }

    public function addTag(String $tag) {
       $tags = $this->tags; 
       $tags->add($tag);
       $this->tags = $tag;       
    }

    public function setTags(Tag $tagObj) {
        $this->tags = $tagObj;
    }

    public function saveTags() {
        $tagArray= $this->tags->keys;
        $repository = $this->repository;
        $tags = implode(",",  $tagArray);  
        $items = $this->getItems();
        return array_map(function($content) use($tags, $repository){
            return $repository->setTags($content, $tags); 
        }, $items);     
    }

    public function addReaction(Reaction $reaction) : Bool {      
        $repository = $this->repository;
        return $repository->addReaction($this, $reaction); 
    }

    public function getReactions() :Array {
        $repository = $this->repository;
        $post = $this;
        $this->reactions = $repository->getPostReactions($post);
        return $this->reactions;
    } 

    public function removeReaction(User $user) {       
        $reactions = $this->reactions;
        $this->reactions = array_map(function($reaction, $key) use ($user, $reactions) {
            if($reaction->getUser() == $user) {
                unset($reactions[$key]);                
            }
        }, $this->reactions, array_keys($this->reactions));
    }

    public function getComments() {
        $repository = $this->repository;
        $this->comments = $repository->getPostComments($this->postId);
        return $this->comments;
    }

    public function userReacted(User $user) {        
        $reacted = false;
        array_map(function($reaction) use ($user, $reacted) {
            if($reaction->getUser() == $user) {
                $reacted = false;
            }
        }, $this->reactions);

        return $reacted;
    }

    private function unReact(User $user) {

    }    
}