<?php
namespace  Triplesss\repository;

require_once('dbsettings.php');
require_once('db.php');
require_once('error.php');

use Triplesss\error\Error as Error;
use Triplesss\post\Post as Post;
use Triplesss\db\DB as Db;

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

    /*
    
    function imageGetById(Int $id) {
        $db = $this->db;
        $s = 'SELECT * FROM image WHERE id='.$id;        
        $p = $db->query($s);
        $r = $db->fetchRow($p);
        return $r;       
    }

    function getImageFiltered(String $tags = '', Int $user_id = -1) {
        $db = $this->db;
        // first, count how many tags there are 
       
        $s = 'SELECT * FROM image WHERE ';
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

    function textAdd(String $text, Int $user_id, String $tags) {
        $db = $this->db;
        $text_id = 0;
        $s = 'INSERT INTO text SET creator_id='.$user_id.', content='.$text.', text_id="'.$text_id.'", created="'.date("Y-m-d H:i_s").'", tags='.$tags;
        echo $s;
        $r = $db->query($s);
        $db->freeResult($r);
        if($db->sql_error()) {
            return $db->sql_error();
        } else {
            return true;
        }
    }

    function textGetById(Int $id) {
        $db = $this->db;
        $s = 'SELECT * FROM text WHERE id='.$id;        
        $p = $db->query($s);
        $r = $db->fetchRow($p);
        return $r;       
    }

    function getTextFiltered(String $tags = '', Int $user_id = -1) {
        $db = $this->db;
        // first, count how many tags there are 
       
        $s = 'SELECT * FROM text WHERE ';
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

    */

    function imageAdd(String $link, String $path, String $type, String $mime, Int $userId, String $tags='') {
        $db = $this->db;
        $s = 'INSERT INTO `image` SET `link`="'.$link.'", `path`="'.$path.'", `created`="'.date("Y-m-d H:i_s").'", `type`="'.$type.'", `mime_type`="'.$mime.'", `creator_id`='.$userId.', `tags`="'.$tags.'"';
        $r = $db->query($s);
        //$db->freeResult($r);
       
        if($db->sql_error()) {
            return $db->sql_error();
        } else {
            return ["id" => $db->lastInsertedID()];
        }
    }

    function textAdd(String $text, Int $user_id, String $tags) {
        $db = $this->db;
        $text_id = 0;
        $s = 'INSERT INTO `text` SET `creator_id`='.$user_id.', `content`="'.$text.'", `text_id`="'.$text_id.'", `created`="'.date("Y-m-d H:i_s").'", `tags`="'.$tags.'"';
        $r = $db->query($s);
        //$db->freeResult($r);
       
        if($db->sql_error()) {
            return $db->sql_error();
        } else {
            return ["id" => $db->lastInsertedID()];
        }
    }

    

    function assetGetById(Int $id, String $asset_type) {
        
        $this->checkAssetType($asset_type);        
        $db = $this->db;
        $s = 'SELECT * FROM '.$asset_type.' WHERE id='.$id;        
        $p = $db->query($s);
        $r = $db->fetchRow($p);
        return $r;       
    }

    function getAssetFiltered(String $tags = '', Int $user_id = -1, String $asset_type) {
        
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

    function getPostById(Int $id) {
        $db = $this->db;
        $images = [];
        $texts = [];

        $s = 'SELECT * FROM post 
                LEFT JOIN content_post ON post.content_post_id = content_post.post_id 
                JOIN content ON content.content_id = content_post.content_id 
                LEFT JOIN image ON content.content_type = "image" AND image.id = content_post.content_id 
                LEFT JOIN text ON content.content_type = "text" AND text.id = content_post.content_id
                WHERE post.post_id='.$id;
        $p = $db->query($s);
        $r = $db->fetchAll($p);

        $assets = array_map(function($post_item) {
         
            if($post_item['content_type'] == 'text') {
               
               return [
                    'owner' => $post_item['owner'], 
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

    function addPost(Post $post) {
        $items = $post->getItems();
    }


    function getVisibilities() {
        $db = $this->db;
        $s = 'SELECT * FROM visibility';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }

    private function tagSelect($tags) {
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

    private function checkAssetType(String $asset_type) {
        if(in_array($asset_type, ['text', 'image'])) {
            return true;     
        }
        
        $this->error->setMessage('Invalid asset type');
        $this->error->setCode(50);
        return $this->error;        
    }

}   