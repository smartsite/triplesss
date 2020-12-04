<?php

namespace Triplesss\post;

use Triplesss\repository\Repository;
use Triplesss\content\Content;
use Triplesss\image\Image;
use Triplesss\visibility\Visibility;
use Triplesss\reaction\Reaction;
use Triplesss\user\User;
use Triplesss\tag\Tag;


//ini_set('display_errors', 1);
//error_reporting(E_ALL);

class Post {
    
    Public $id = 0;
    Public $postId;
    //Public $userId;
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

        /*
        foreach($this->items as $item) {
            if($item->getContentType() == 'image') {
                $im = ['basefolder' => $item->getBaseFolder(), 'name' => $item->getImageName()];
                $content['images'][] = $im;
            }

            if($item->getContentType() == 'text') {
                $tx = $item->getTex
            }
        }
        */
        
        return $content;
        //return ['text' => $this->text, 'images' => $this->images];       
    }

    public function add() {
        $postId = $this->repository->addPost($this);
        $this->postId = $postId;
        return $postId;        
    }

    public function updateContent(String $post_id, String $text) { 
        // TODO: handle more than one image :(
        $status = $this->repository->updatePost($post_id, $text);
        return $status;
    }

    public function delete(String $post_id) { 
        $status = $this->repository->deletePost($post_id);
        return $status;
    }

    public function edit(String $text, Image $image) { 
        // TODO: handle more than one image :(
        $status = $this->repository->editPost($this, $text, $image);
        return $status;
    }

    
    public function getItems() {
        //$text = $this->repository->getPostAsset('text', $this->postId);
        //$image = $this->repository->getPostAsset('image', $this->postId);
        //$this->items = [$text, $image];
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
        array_map(function($content) use($tags, $repository){
            $repository->setTags($content, $tags); 
        }, $items);      
       
    }

    public function addReaction(Reaction $reaction) : Bool {
        // first, see if user has reacted
        //$added = false;

        $repository = $this->repository;
        return $repository->addReaction($this, $reaction);  

        /*
        if(!$this->userReacted($user)) {
            array_push($this->reactions, $reaction);
            $added = true;
        }
        */
        
        //array_push($this->reactions, $reaction);
        //return $added;
    }

    public function getReactions() :Array {
        $repository = $this->repository;
        $post = $this;
        $this->reactions = $repository->getPostReactions($post);
        return $this->reactions;
    } 

    public function removeReaction(User $user) {
        
        // Need to loop through reactions until we find this user, and unset it
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
        // See if User is in $this->reactions
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

       
    /*
    function setOwner(User $user) {
        $this->owner = $user;
    }
    */


}