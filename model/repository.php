<?php
namespace  Triplesss\repository;

require_once('dbsettings.php');
require_once('db.php');
require_once('error.php');

use Triplesss\error\Error as Error;
use Triplesss\feed\Feed as Feed;
use Triplesss\post\Post as Post;
use Triplesss\db\DB as Db;
use Triplesss\content\content as Content;
use Triplesss\visibility\Visibility as Visibility;

/**
 * 
 *   MySQL ORM
 *   
 *   The Repository object is an abstraction of the database layer, seprating query-level
 *   CRUD from the application layer. It can be written to suit different storage systems, 
 *   e.g PostGres, MongoDB or any NoSQL type 
 *   
 *   Create a single instance of the DB class and access all
 *   data objects thtough this class 
 * 
 * */

class Repository {
    
    Public $db;
    Public $error;
       
    function __construct() {
        $this->db = new Db(); 
        $this->error = new Error;       
    }

    Public function imageAdd(String $link, String $path, String $type, String $mime, Int $userId, String $tags='') {
        $db = $this->db;
        $s = 'INSERT INTO `image` SET `link`="'.$link.'", `path`="'.$path.'", `created`="'.date("Y-m-d H:i_s").'", `type`="'.$type.'", `mime_type`="'.$mime.'", `creator_id`='.$userId.', `tags`="'.$tags.'"';
        $r = $db->query($s);
        //$db->freeResult($r);
        if($r) {
            $id = $db->lastInsertedID();
            $s = 'INSERT INTO content SET content_id='.$id.', content_type="image"';
            $in =  $db->query($s);
            return ["id" => $id];
        } else {
            return $db->sql_error();
        }     
    }

    Public function textAdd(String $text, Int $user_id, String $tags) {
        $db = $this->db;
        $text_id = 0;
        $s = 'INSERT INTO `text` SET `creator_id`='.$user_id.', `content`="'.$text.'", `text_id`="'.$text_id.'", `created`="'.date("Y-m-d H:i_s").'", `tags`="'.$tags.'"';
        $r = $db->query($s);
        //$db->freeResult($r);
       
        if($r) {
            $id = $db->lastInsertedID();
            $s = 'INSERT INTO content SET content_id='.$id.', content_type="text"';
            $in =  $db->query($s);
            return ["id" => $id];
        } else {
            return $db->sql_error();
        }  
    }

    

    Public function assetGetById(Int $id, String $asset_type) {
        
        $this->checkAssetType($asset_type);        
        $db = $this->db;
        $s = 'SELECT * FROM '.$asset_type.' WHERE id='.$id;        
        $p = $db->query($s);
        $r = $db->fetchRow($p);
        return $r;       
    }

    Public function getAssetFiltered(String $tags = '', Int $user_id = -1, String $asset_type) {
        
        $this->checkAssetType($asset_type);        
        $db = $this->db;
        // first, count how many tags there are 
       
        $s = 'SELECT * FROM '.$asset_type.' WHERE ';
        $w = $this->tagSelect($tags);

        if($user_id > -1) {
            $w.= ' AND creator_id='.$user_id;
        }        
        $q = $s.$w;
        //echo $q;
        $p = $db->query($q);
        $r = $db->fetchAll($p);
        return $r;
    }

    //Public function getPostById(Int $id) {
    Public function getPostById(String $id) {
       
        $r = [];
        $r =  array_merge($r, $this->getPostAsset('image', $id));
        $r = array_merge($r, $this->getPostAsset('text', $id));  

        $assets = array_map(function($post_item) {
         
            if($post_item['content_type'] == 'text') {
               
               return [
                    'owner' => $post_item['owner'], 
                    'content_type' => $post_item['content_type'], 
                    'text_id' => $post_item['text_id'], 
                    'content' => $post_item['content'], 
                    'tags' =>    $post_item['tags'], 
                    'creator_id' => $post_item['creator_id'],
                    'visibility' =>   $post_item['visibility']                   
                ];               
            }
            
            if($post_item['content_type'] == 'image') {
              
                return [
                    'owner' => $post_item['owner'], 
                    'content_type' => $post_item['content_type'], 
                    'link' => $post_item['link'], 
                    'path' => $post_item['path'], 
                    'tags' =>    $post_item['tags'], 
                    'mime_type' =>    $post_item['mime_type'], 
                    'creator_id' => $post_item['creator_id'],
                    'visibility' =>   $post_item['visibility']                       
                ];
            }          
            
        }, $r);
        return $assets;
    }

    Public function getPostAsset(String $type, String $post_id) {

        if($this->checkAssetType($type)) {
            $db = $this->db;
            $s = 'SELECT * FROM content_post 
                    JOIN post ON content_post.post_id = post.id 
                    JOIN '.$type.' ON '.$type.'.id = content_post.content_id AND content_post.content_type="'.$type.'"   
                    WHERE post.post_id="'.$post_id.'"';            
          
            $p = $db->query($s);
            $r = $db->fetchAll($p);
            return $r; 
        }  
    }

    Public function addPost(Post $post) {
        // gets all the post items and add them to the post table
        // first, insert ths post
        $db = $this->db;
        $owner = $post->getOwner();
        $post_id = $this->newPostId();
        $s = 'INSERT INTO post SET owner='.$owner.', post_id="'.$post_id.'", title=""';
        $db->query($s);
        $p_id = $db->lastInsertedID();
        $items = $post->getItems();

        array_map(function(Content $item) use($db, $p_id) {
            $id = $item->getContentId();
            $content_type = $item->getContentType();
            $qry = 'INSERT INTO content_post SET post_id='.$p_id.', content_id='.$id.', content_type="'.$content_type.'"';
            $r = $db->query($qry);           
        }, $items);  
        
        return $post_id;
    }  
    
    Public function addPostToFeed(Post $post, Feed $feed) {
        $db = $this->db;
        //$p_id = $post->getId();
        $p_id = $post->getPostId();
        $f_id = $feed->getId();
        $id = $this->newPostId();
        $visibility = $post->getVisibility()->getLevel();
        $qry = 'INSERT INTO feed_post SET id="'.$id.'", post_id="'.$p_id.'", feed_id='.$f_id.', visibility='.$visibility;
        //echo $qry;
        $p = $db->query($qry);   
       if($p) {
            return $db->lastInsertedID();
       } else {
            return $db->sql_error();
       }
    }

    Public function getFeedPosts(Feed $feed) {
        
        $db = $this->db;
        $s = 'SELECT post_id FROM feed_post WHERE feed_id='.$feed->id;
        $p = $db->query($s);
        $r = $db->fetchAll($p);

        $posts = array_map(function($p){          
            return $this->getPostById($p['post_id']);
        }, $r);
        return $posts;
    }


    Public function getVisibilities() {
        $db = $this->db;
        $s = 'SELECT * FROM visibility';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }

    Private function tagSelect($tags) {
        $w = '1 = 1 ';
        if($tags != '') {
            $w.= ' AND ';
            $tg = explode(',', $tags);
            $itg = [];
            foreach($tg as $tag) {
               $itg[] = '"'.$tag.'"';
            }
            $intags = implode(',', $itg);

            $ss = [];
            for($i=1; $i<10 + 1; $i++) {
                $qry = 'SUBSTRING_INDEX(SUBSTRING_INDEX(tags, \',\', '.$i.'), \',\', -1) IN ('.$intags.')';
                array_push($ss, $qry);
            }
            $w.= '('.implode(' OR ', $ss).')';
        }
        return $w;
    }

    Private function checkAssetType(String $asset_type) {
        if(in_array($asset_type, ['text', 'image'])) {
            return true;     
        }
        
        $this->error->setMessage('Invalid asset type');
        $this->error->setCode(50);
        return $this->error;        
    }

    Private function newPostId() :String {
        $postId = bin2hex(openssl_random_pseudo_bytes(32));
        return $postId;
    }

}   