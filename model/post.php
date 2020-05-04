<?php
namespace Triplesss\post;

use Triplesss\repository\Repository;
use Triplesss\content\Content;
use Triplesss\reaction\Reaction;
use Triplesss\user\User;
use Triplesss\tag\Tag;

class Post {
    
    Public $id = 0;
    //Public $userId;
    Public $contentType;
    Public $text = '';
    Public $images = [];
    Public $link = '';
    Public $owner = null; // Alias for userId
    Public $items = [];
    Public $tags = null;
    Public $reactions = [];
    Public $repository;
           
    
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

    public function getItems() {
        return $this->items;
    }

    public function render() {
        // output markup for a post. Not really needed with a F/E framework.
    }

    public function addTag(Tag $tag) {
        $this->tags = $tag;
    }

    public function addReaction(Reaction $reaction) : Bool {
        // first, see if user has reacted
        $added = false;
        /*
        if(!$this->userReacted($user)) {
            array_push($this->reactions, $reaction);
            $added = true;
        }
        */
        array_push($this->reactions, $reaction);
        return $added;
    }

    public function getReactions() :Array {
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