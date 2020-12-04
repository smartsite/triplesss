<?php

namespace  Triplesss\repository;

require_once('settings.php');
require_once('db.php');
require_once('error.php');

use Triplesss\settings\Settings as Settings;
use Triplesss\error\Error as Error;
use Triplesss\feed\Feed as Feed;
use Triplesss\post\Post as Post;
use Triplesss\user\User as User;
use Triplesss\user\Member as Member;
use Triplesss\users\Users as Users;
use Triplesss\filter\Filter as Filter;
use Triplesss\db\DB as Db;
use Triplesss\content\content as Content;
use Triplesss\image\Image as Image;
use Triplesss\post\comment as Comment;
use Triplesss\reaction\Reaction as Reaction;
use Triplesss\connection\Connection as Connection;
use Triplesss\notification\Notification;
use Triplesss\visibility\Visibility as Visibility;
use Triplesss\collection\Aggregator;

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
        $this->settings = new Settings();
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

    Public function getPostOwnerById(String $id) {
        $db = $this->db;
        $s = 'SELECT user.id, user_name FROM user JOIN post ON post.owner = user.id WHERE post_id="'.$id.'"';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            return $r[0];
        } else {
            return ['id' => -1, 'user_name'=> ''];
        }
        
    }

    //Public function getPostById(Int $id) {
    Public function getPostById(String $id) {
       
        $db = $this->db;
        $r = [];
        $r =  array_merge($r, $this->getPostAsset('image', $id));
        $r = array_merge($r, $this->getPostAsset('text', $id));  
        $comments = $this->getPostComments($id);
        
        $likes = 0;
        // TODO: remove hard coded 'like' count, use reaction object instead
        $s = 'SELECT COUNT(*) AS likes FROM reaction WHERE post_id="'.$id.'" AND level = 2';
        $p = $db->query($s);
        $l = $db->fetchAll($p);
        if($l) {
            $likes = $l[0]['likes'];
        }
  

        $assets = array_map(function($post_item) use ($id, $comments, $likes) {
         
            if($post_item['content_type'] == 'text') {
               
               return [
                    'post_id'=> $id,
                    'owner' => $post_item['owner'], 
                    'user_name' => $post_item['user_name'], 
                    'content_type' => $post_item['content_type'], 
                    'content_id' => $post_item['content_id'],
                    'text_id' => $post_item['text_id'], 
                    'content' => $post_item['content'], 
                    'tags' =>    $post_item['tags'], 
                    'comment_count' => count($comments), 
                    'likes' =>  $likes, 
                    'creator_id' => $post_item['creator_id'],
                    'visibility' =>   $post_item['visibility'],
                    'date' =>  $post_item['created']             
                ];               
            }
            
            if($post_item['content_type'] == 'image') {
              
                return [
                    'post_id'=> $id,
                    'owner' => $post_item['owner'], 
                    'user_name' => $post_item['user_name'], 
                    'content_type' => $post_item['content_type'], 
                    'content_id' => $post_item['content_id'],
                    'link' => $post_item['link'], 
                    'path' => $post_item['path'], 
                    'tags' =>    $post_item['tags'], 
                    'comment_count' => count($comments), 
                    'likes' =>  $likes, 
                    'mime_type' =>    $post_item['mime_type'], 
                    'creator_id' => $post_item['creator_id'],
                    'visibility' =>   $post_item['visibility'],
                    'date' =>  $post_item['created']                                    
                ];
            }          
            
        }, $r);
        return $assets;
    }

    Public function getPostAsset(String $type, String $post_id, Int $visibility = 0) {

        if($this->checkAssetType($type)) {
            $db = $this->db;
            $s = 'SELECT * FROM content_post 
                    JOIN post ON content_post.post_id = post.id 
                    JOIN user ON post.owner = user.id 
                    JOIN '.$type.' ON '.$type.'.id = content_post.content_id AND content_post.content_type="'.$type.'"   
                    WHERE post.post_id="'.$post_id.'" AND post.visibility >='.$visibility;            
          
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
        $post->postId =  $post_id;
        //$items = $post->getItems();
        $items = $post->items;

        array_map(function(Content $item) use($db, $p_id) {
            $id = $item->getContentId();
            $content_type = $item->getContentType();
            $qry = 'INSERT INTO content_post SET post_id='.$p_id.', content_id='.$id.', content_type="'.$content_type.'"';
            $r = $db->query($qry);           
        }, $items);  
        
        return $post_id;
    }  

    Public function updatePost(String $p_id, String $text) {
        $db = $this->db;
        $qry = 'UPDATE `text` JOIN content_post on content_id = text.id AND content_type="text" 
                JOIN `post` ON  post.id = content_post.post_id SET `content`="'.$text.'" WHERE post.post_id="'.$p_id.'"';
        $r = $db->query($qry);
       
        if($r) {
            return $text;
        } else {
            return false;
        }        
    }
    

    Public function postVisibility(String $p_id, Int $visibility) {
        $db = $this->db;
        $qry = 'UPDATE `post` SET visibility = '.$visibility.' WHERE post.post_id="'.$p_id.'"';
        $r1 = $db->query($qry);

        $qry = 'UPDATE `text` JOIN content_post on content_id = text.id AND content_type="text" 
        JOIN `post` ON  post.id = content_post.post_id SET visibility = '.$visibility.' WHERE post.post_id="'.$p_id.'"';
        $r = $db->query($qry);

        $qry = 'UPDATE `image` JOIN content_post on content_id = image.id AND content_type="image" 
        JOIN `post` ON  post.id = content_post.post_id SET visibility = '.$visibility.' WHERE post.post_id="'.$p_id.'"';
        $r = $db->query($qry);

        return $r1;        
    }

    Public function deletePost(String $p_id) {
        return $this->postVisibility($p_id, -1);
    }

    Public function editPost(Post $post, String $text, Image $image) {
        $db = $this->db;
        $owner =  $post->getOwner();

        // basically we just need to do an update on the text in the text table if it's not empty,
        // and update the image if a replacement has been uploaded
    }
    
    Public function addPostToFeed(Post $post, String $parent = '', Feed $feed = null) {
        $db = $this->db;
        //$p_id = $post->getId();
        $p_id = $post->getPostId();
       
        if($parent == '') {
            $f_id = $feed->getId();
            $id = $this->newPostId();
        } else {
            $f_id = -1; // this indicates it's a comment, because it belongs to a post, not a feed
            $id = $parent;            
        }
       
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

    Public function getLikeCount(String $post_id) {
        $db = $this->db;
        $count = 0;
        $s = 'SELECT count(*) AS count FROM post_comment WHERE feed_post_id="'.$post_id.'"';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            $count = $r[0]['count'];
        }
        return $count;
    }

    Public function createFeed($owner_id, $name, $description) {
        $db = $this->db;
        $created = date("Y-m-d");
        $s = 'INSERT INTO feed (owner_id, feed_name, feed_description, created, active, status) VALUES 
                ('.$owner_id.', "'.$name.'", "'.$description.'", "'.$created.'", 1, "current")';
        $p = $db->query($s);
        $id = $db->lastInsertedID();
        return $id;              
    }

    Public function getFeed(Int $id) {
        $db = $this->db; 
        $feed = false;
        $s = 'SELECT * FROM feed WHERE id="'.$id.'" LIMIT 1';
        $p = $db->query($s);
        $r = $db->fetchRow($p);
        if($r) {
            $feed = $r;
        }
        return $feed;
    }

    Public function updateFeedStatus($id, $active, $status) {
        $db = $this->db; 
        $s = 'UPDATE feed SET active='.$active.', status="'.$status.'" WHERE id='.$id;
        $p = $db->query($s);
        return $p;
    }

    Public function getFeedPosts(Feed $feed, Filter $filter=null, Int $visibility = 0) {
        
        $db = $this->db;
        $s = 'SELECT post_id FROM feed_post WHERE feed_id='.$feed->id;
        if($filter) {
            if($filter->type == 'userid' && $filter->userid) {
                $s = 'SELECT feed_post.post_id FROM feed_post JOIN post ON post.post_id = feed_post.post_id 
                WHERE feed_id='.$feed->id.' AND post.owner='.$filter->userid.' AND feed_post.visibility >= '.$visibility ;
            }           
        }
        
        $p = $db->query($s);
        $r = $db->fetchAll($p);

        $posts = array_filter(array_map(function($p){          
            $p = $this->getPostById($p['post_id']);
            return $p;
        }, $r));
        return $posts;
    }

    Public function getAggregatedPosts(Aggregator $aggregator) {
        /**
         *   Get everything posted by a user's connections that's set to friend level visibility
         *   Sorted by reverse post id for now ( by default ), which is crappy, but the applied
         *   filter can probably overcome that limitation
         */
        
        $db = $this->db;

        $filter = $aggregator->filter;
        $limit = '';
        $offset = '';
        if($filter->type == 'range') {
            $range = $filter->getRange();
            $offset = $range[0];
            $limit  = $range[1];
        }

        $userid = $aggregator->userid;
        // query to fetch all post ids for connected users 
        $s = 'SELECT owner, post.post_id FROM feed_post
            JOIN post ON  feed_post.post_id = post.post_id 
            JOIN
            (SELECT DISTINCT * FROM 
            (SELECT from_id con FROM connection WHERE connection_type IN (1,2)  AND to_id = '.$userid.' AND from_id <> '.$userid.' UNION 
            SELECT to_id con FROM connection WHERE connection_type IN (1,2) AND from_id = '.$userid.' AND to_id <> '.$userid.') connected) connection 
            ON connection.con = post.owner 
            ORDER BY post.id DESC';
        if($limit !== '' && $offset !== '') {
            $s.= ' LIMIT '.$limit. ' OFFSET '.$offset;
        } 
        
                
        $p = $db->query($s);
        $r = $db->fetchAll($p);

        // get Admin / system posts
        //$s = 'SELECT owner, post.post_id FROM feed_post  JOIN post ON feed_post.post_id = post.post_id WHERE  feed_id = 1;

        $posts = [];
        if($r) {
            $posts = array_filter(array_map(function($post) {
                if(!is_null($post)) {
                    $post_id = $post['post_id'];
                    $p = $this->getPostById($post_id);
                    return array_values($p);
                }
            }, $r));
        }
        return $posts;
    }       

    Public function addPostComment(Comment $comment) {
        $db = $this->db;
        $c = $comment;
        
        // We are essentially casting the comment as a post
        $post = new Post($c->owner);
        $post->postId = $this->newPostId();
        $post->images = [];
        $post->items = $c->items;
        $post->reactions = $c->reactions;
        $post->visibility = $c->visibility;
        $post->add();

        $id = $c->parent_post;
        $p_id = $post->postId;
        $visibility = $c->visibility->level;

        $qry = 'INSERT INTO post_comment SET feed_post_id="'.$id.'", post_id="'.$p_id.'", visibility='.$visibility;
        $p = $db->query($qry);   
        if($p) {
            return $db->lastInsertedID();
        } else {
            return $db->sql_error();
        }     
        
    }

    Public function addReaction(Post $post, Reaction $reaction) {
        $post_id = $post->postId;
        $user_id = $reaction->user->userid;
        $level = $reaction->get()->level;
        
        $db = $this->db;
        $s = 'INSERT INTO reaction SET user_id='.$user_id.', post_id="'.$post_id.'", level='.$level;
        $p = $db->query($s);
        return $p;
    }

    Public function getPostReactions(Post $post, Reaction $reaction = null) {
        $db = $this->db;
        $post_id = $post->postId;
        $s = 'SELECT user_id, user_name, level FROM reaction JOIN user ON user_id = user.id WHERE post_id="'.$post_id.'"';
        if($reaction) {
            $level = $reaction->get()->level;
            $s.= ' AND level='.$level;
        }
       
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }

    Public function getUserReactions(Int $user_id, Int $count = 100) {
        $db = $this->db;
        $s = 'SELECT level, post_id FROM reaction WHERE user_id ='.$user_id.' ORDER BY id DESC LIMIT '.$count;
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }

    /*
    Public function getPostLikes(Post $post_id) {
        // todo - get likes for this post!
        $db = $this->db;
        $s = 'SELECT count(*) FROM reaction WHERE post_id='.$post_id.' AND level =' ;
        return [];
    }
    */


    Public function getPostComments(String $parent_id, Filter $filter=null) {
        // We only support text assets at the moment, but images can be added by duplicating the query
        // and UNION it with content_type = 'image'

        $db = $this->db;
           
        $s = 'SELECT post_comment.feed_post_id parent_id, post_comment.post_id, content, owner, user.user_name, creator_id, text.visibility, text.created FROM post_comment 
        JOIN post ON post.post_id = post_comment.post_id 
        JOIN content_post ON  content_post.post_id = post.id 
        JOIN user ON user.id = owner 
        JOIN text ON text.id = content_post.content_id AND  content_post.content_type = "text"
        WHERE post_comment.feed_post_id = "'.$parent_id.'" 
        AND content_type = "text"';

        if(!$filter) {
            $s.= " ORDER BY text.created DESC";
        }

        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;             
    }

    
    public function checkUserName(String $username) {
        $db = $this->db;
        $s = 'SELECT user_name FROM user WHERE user_name="'.$username.'"';
        $p = $db->query($s);
        $rows = $db->fetchAll($p);
        $r = $rows[0];
        return !is_null($r);        
    }

    public function temporaryPassword(Int $length = 10) :String {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);
    
        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }    
        return $result;
    }

    public function addUser(String $username) {
        $db = $this->db;
        $temppwd = $this->temporaryPassword();
        $qry = 'INSERT INTO user SET user_name="'.$username.'", first_name="", last_name="", password="'.$temppwd.'"';
        $p = $db->query($qry); 
        if($p) {
            return $db->lastInsertedID();
        } else {
            return $db->sql_error();
        }  
    }

    public function getUserId(String $username) {
        $db = $this->db;
        $s = 'SELECT id FROM user WHERE user_name="'.$username.'" LIMIT 1';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            return $r[0]['id'];
        } else {
            return -1;
        }       
    }

    public function deleteUser(User $user) {
        return true;
    }

    public function updateUser(Array $details) {
        $db = $this->db;
        $username = $details['user_name'];
        $s = 'UPDATE user SET ';
        
        $q = array_map(function($d, $k) {
            return '`'.$k.'`="'.$d.'"';
        }, $details, array_keys($details));
        $s.= implode(',', $q);
        $s.= ' WHERE user_name="'.$username.'"';
        $p = $db->query($s);
        return $p;
    }

    public function verifyUser(String $key) {
        $db = $this->db;
        $s = 'SELECT id, user_name FROM user WHERE reg_link LIKE "%'.$key.'%"';
        $p = $db->query($s);
        $r = $db->fetchAssoc($p);
        if($r) {
            $id = $r['id'];
            $s = 'UPDATE user SET active=1 WHERE id='.$id;
            $p = $db->query($s);
        }
        return $r;
    }

    public function userLogin($username, $password, $hashed = false) {
        $db = $this->db;
        $session_timeout = $this->getSetting('session_timeout');
        $error = [];
        $userObj = $this->allUserDetails($username);
        if($hashed) {
            $password = hash ("sha256", $password);
        }
        
        if($userObj){
            if(strtoupper($userObj['password']) == strtoupper($password)) {
                // winner, winner chicken dinner!

                $session_time = $this->getSetting('session_time');
                $session_id =  $this->getSession();
                $_SESSION['username'] = $username;
                $expiry = time() + ($session_time);  // 1 days x 24 hours x 60 mins x 60 secs ( 5 mins for testing !! )
                $_SESSION['expires'] =  $expiry;
                setcookie("userID", $userObj['id'],  $expiry, "/" );
                setcookie("userName", $username,  $expiry, "/" );
                $db->query("DELETE FROM session WHERE user_id='".$userObj['id']."'"); // clean up an old sessions for this user
                $db->query("INSERT INTO session VALUES('".$session_id."', '".$userObj['id']."', '".$expiry."')");
                $error['message'] = "logged in";
                $error['username'] = $username;
                $error['success'] = "true";
                $this->failed_logins = 0;
                
            }else{
                //echo "That password's wrong, baby!";
                $error['message'] = "Incorrect password";
                $error['success'] = "false";
                //$this->failed_logins++;
            }
        }else{
            // no result... that's bad!
            $error['message'] = "Unknown user";
            $error['success'] = "false";
        }
        return $error;
    }

    public function isUserLoggedIn() {
        // We don't ever want to return a session ID... that'd be bad
        // If a user has a valid session cookie though, we can assume they are logged in
        // first check the userID cookie

        $loggedIn = false;
        $db = $this->db;     
        if(!isset( $_COOKIE['userID'])){
            // if no cookie, then check DB for non-expired session
            if(isset($_COOKIE['PHPSESSID'])){                
                $session = $this->hasSession($_COOKIE['PHPSESSID']);   
                $loggedIn = $session['logged_in'];             
            }    
        }else{
            // ... or if we have a session cookie AND a user id cookie 
            $session = $this->hasSession($_COOKIE['PHPSESSID']); 
            $loggedIn = $session['logged_in'];
        }
        return $loggedIn;
    } 

    public function userLogout() {
        $db = $this->db;
        if(isset( $_COOKIE['userID'])) { 
            $s = 'DELETE FROM session WHERE user_id='.$_COOKIE['userID'];
            $p = $db->query($s);
            if($p) { 
                setcookie('userID', null, -1, '/'); 
                setcookie('userName', null, -1, '/'); 
                session_destroy();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }        
    }

    public function reSendRegisterLink(String $email, String $from, String $reply) {

    }

    public function generateRegisterLink(String $username, String $from, String $reply) {
        $db = $this->db;
        $key = $this->randomString(12);
        $link = $this->getSetting('hostname').'/register_confirm?key='.$key;
        $s = 'UPDATE user SET reg_link="/register_confirm?key='.$key.'" WHERE user_name="'.$username.'"';
        $p = $db->query($s);
        $regemail = false;
        if($p) {
            // send email;
            $regemail = $this->sendRegEmail($username, $link, $from, $reply);
        }
        return $regemail;
    }

    public function getConnectedUsers(User $user, Connection $connection = null) { 
        $db = $this->db;
        $user_id = $user->userid;
        $connection_level = 2; // friend by default
        if($connection) {
            $connection_level = $connection->type;
        }
        
        /*
        $s = 'SELECT DISTINCT user.id, user_name, connection_types.name relation FROM connection JOIN user ON connection.from_id = user.id 
                JOIN connection_types ON connection_types.id = connection.connection_type 
                WHERE connection.connection_type IN (1,'.$connection_level.') AND connection.to_id='.$user_id;
                     
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        $rv = [];
        
            // get the reverse connection for freinds
            
            $s = 'SELECT DISTINCT user.id, user_name, "friend" relation FROM connection JOIN user ON connection.to_id = user.id 
                JOIN connection_types ON connection_types.id = connection.connection_type 
                WHERE connection.from_id='.$user_id.' AND connection_type=2' ;
             $p = $db->query($s);
             $rv = $db->fetchAll($p);
        
        return array_merge($r, $rv);
        */

        $s = 'SELECT DISTINCT * FROM (SELECT user.id, user_name, connection_types.name relation FROM connection JOIN user ON connection.from_id = user.id 
        JOIN connection_types ON connection_types.id = connection.connection_type 
        WHERE connection.connection_type IN (1,'.$connection_level.') AND connection.to_id='.$user_id.' UNION  
        SELECT user.id, user_name, "friend" relation FROM connection JOIN user ON connection.to_id = user.id 
        JOIN connection_types ON connection_types.id = connection.connection_type 
        WHERE connection.from_id='.$user_id.' AND connection_type=2) con';

        $p = $db->query($s);
        $rv = $db->fetchAll($p);
        return $rv;

    }


    public function getConnectionRequests(User $user) { 
        $db = $this->db;
        $user_id = $user->userid;
        $connection_level = 9; // request
       
        $s = 'SELECT DISTINCT * FROM (SELECT user.id, user_name, "friend_request" relation FROM connection JOIN user ON connection.to_id = user.id 
                JOIN connection_types ON connection_types.id = connection.connection_type 
                WHERE connection.connection_type = '.$connection_level.' AND connection.from_id='.$user_id.' UNION 
                SELECT user.id, user_name, "request_friend" relation FROM connection JOIN user ON connection.from_id = user.id 
                JOIN connection_types ON connection_types.id = connection.connection_type 
                WHERE connection.connection_type = '.$connection_level.' AND connection.to_id='.$user_id.') s';
              
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }


    public function getConnectionTypes() {
        $db = $this->db;
        $s = 'SELECT * FROM connection_types';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }

    public function addConnection(User $from, User $to, Connection $connection) {
        $db = $this->db;
        $from_id = $from->userid;
        $to_id = $to->userid;
        $type = $connection->getType();

        // insert or update if connection exists
        $p = false;

        if($type == 2 ) {
            // update reverse 9 connections
            $s = 'UPDATE connection SET connection_type=2 WHERE connection_type=9 AND (to_id='.$from_id.' AND from_id='.$to_id.' 
                    AND to_id='.$from_id.' AND from_id='.$to_id.')';
            $p = $db->query($s);        
        }
        if($type != 0) {
            $s = 'INSERT INTO connection VALUES ('.$type.', '.$from_id.', '.$to_id.') ON DUPLICATE KEY UPDATE connection_type='.$type;
            $p = $db->query($s);
        } else {
            $s = 'DELETE FROM connection WHERE from_id='.$from_id.' AND to_id='.$to_id;
            $p = $db->query($s);
            $s = 'DELETE FROM connection WHERE to_id='.$from_id.' AND from_id='.$to_id;
            $p = $db->query($s);
        }      
       
        return $p;
    }

    public function removeConnection(User $from, User $to, Connection $connection) {
        // addConnection type=0 does this!
    }

    public function setNotification(Notification $notification) {
        $db = $this->db;
        $u = $notification->to_user;
        $from = $notification->from_user;
        $user_id = $u->userid;
        $from_user_id = $from->userid;
        $message = addslashes($notification->getMessage());
        $type =  $notification->typeid;

        $s = 'INSERT INTO notification (`type`, `to_user_id`, `from_user_id`, `notification_id`, `message`) VALUES ('.$type.', '.$user_id.', '.$from_user_id.', "", "'.$message.'" )'; 
        $p = $db->query($s);
        return $p;
    }

    public function getNotifications(User $user) {
        $db = $this->db;
        $user_id = $user->userid;
        $user_level = $user->getLevel();
      
        // work out the user type to determine what sort of notifications they should get
        if($user_level == 99){
            // system user - system notifications
            $s = 'SELECT DISTINCT from_user_id, to_user_id, type, message, timestamp  FROM notification 
            JOIN connection ON connection.from_id = notification.from_user_id  AND to_user_id='.$user_id;
        }

        if($user_level == 1){
            // admin user - reports
            $s = 'SELECT DISTINCT from_user_id, to_user_id, type, message, timestamp  FROM notification 
            JOIN connection ON connection.from_id = notification.from_user_id  AND to_user_id='.$user_id;
        }

        if($user_level == 5){
            // show all  notifications from user_id = 1 and 2
            $s = 'SELECT * FROM (SELECT DISTINCT from_user_id, to_user_id, type, message, timestamp  FROM notification 
            JOIN connection ON connection.from_id = notification.from_user_id  AND connection.to_id = '.$user_id.' UNION 
            SELECT DISTINCT from_user_id, to_user_id, type, message, timestamp  FROM notification             
            WHERE to_user_id IN(1,2) AND from_user_id < 2) t1 
            WHERE t1.type NOT IN (8,9,10,11,12)                  
            ORDER BY t1.timestamp DESC';        
            
            //echo $s;
           
        }

        /*
        $s = 'SELECT * FROM  notification WHERE to_user_id= '.$user_id.' OR from_user_id IN 
              (SELECT to_id FROM connection WHERE from_id='.$user_id.') OR (from_user_id = 0 AND to_user_id = 2 )
              OR (from_user_id = 0 AND to_user_id IN (SELECT to_id FROM connection WHERE from_id='.$user_id.')) ORDER BY timestamp DESC';
        */       
        
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }
      

    public function getUsers(Filter $filter, Bool $safe) {
        $db = $this->db;
        if($safe) {
            $s = 'SELECT id, user_name, first_name, last_name, last_login, session.expires, user_level, active    
                  FROM user LEFT JOIN session ON session.user_id = user.id AND expires > NOW()';
        } else {
            $s = 'SELECT * FROM users';
        }               
       
        if($filter->getType() == 'range') {
            $range = $filter->getRange();
            $s.= ' LIMIT '.$range[1].' OFFSET '.$range[0]; 
        }
        
        if($filter->getType() == 'like') {
            $like = $filter->getUsername();           
            $s.= ' WHERE user_name LIKE "'.$like.'%" AND active=1 AND user_name NOT LIKE "admin"';             
            $s.= ' LIMIT 10'; 
        }
              
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }

    public function getUserName(User $user) {
        $db = $this->db;
        $user_id = $user->userid;
        $s = 'SELECT user_name FROM user WHERE id='.$user_id;
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            return $r[0]['user_name'];
        } else {
            return false;
        }        
    }

    public function createMember(Member $member) {
        $db = $this->db;
        $user_id = $member->userid;
        $active = 0;
        $status = 1;
        $renewal_date = $member->getRenewalDate();
        $renewal_interval = $member->getRenewalInterval();
        $payment_method = $member->getPaymentMethod();
        $joined_date = $member->getCreatedDate();
        $s = 'INSERT INTO member (`user_id`, `joined_date`, `status`, `active`, `renewal_interval`, `renewal_date`, `payment_method`) 
                VALUES ('.$user_id.', "'.$joined_date.'", '.$status.','.$active.', "'.$renewal_interval.'", "'.$renewal_date.'", "'.$payment_method.'" )';
        $p = $db->query($s);
        if(!$p) {
            // Looks like we already have created this member!
            $s = 'SELECT member_id FROM member WHERE user_id='.$user_id;
            $q = $db->query($s);
            if($q) {
                $m = $db->fetchAll($q);
                $member_id = $m[0]['member_id'];
                return $member_id;
            } else {
                return $db->lastInsertedID();
            }
        }        
    }

    public function getMember(Int $member_id = -1, Int $user_id = -1, Bool $safe) {
        $db = $this->db;
        // first try member_id
        $s = 'SELECT user.user_name, user.first_name, user.last_name, user.email, user.user_level, 
             member_id, member.joined_date, member.status, member.active, member.renewal_date, member.customer_id 
             FROM member JOIN user ON member.user_id = user.id WHERE member_id ='.$member_id.' OR user.id = '.$user_id.' LIMIT 1';
       
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            return $r[0];
        } else {
            return [];
        }       
    }

    public function getMemberByCustomerId(String $customer_id) {
        $db = $this->db; // Used to find payed up members
        $s = 'SELECT member.user_id, user.user_name, user.first_name, user.last_name, user.email, user.user_level, 
        member_id, member.joined_date, member.status, member.active, member.renewal_date, member.customer_id 
        FROM member JOIN user ON member.user_id = user.id WHERE customer_id ="'.$customer_id.'" LIMIT 1';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            return $r[0];
        } else {
            return [];
        }       
    }

    public function updateMember(Array $details) {
        if(array_key_exists('member_id', $details)) {
            $db = $this->db;
            $member_id = $details['member_id'];
            $s = 'UPDATE member SET ';

            $colvals = array_map(function($d, $k) {
                return '`'.$k.'`="'.$d.'"';
            }, $details, array_keys($details));
            $s.= implode(',', $colvals);

            $s.= ' WHERE member_id = '.$member_id;
            $p = $db->query($s);
            return $p;
        }
        return false;
    }

    public function getUserFeeds(Int $userid) {
        $db = $this->db;
        $s = 'SELECT id, feed_name, feed_description FROM feed WHERE owner_id='.$userid.' AND active=1 AND status="current"';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            return $r;
        } else {
            return [];
        }       
    }


    public function getVisibilities() {
        $db = $this->db;
        $s = 'SELECT * FROM visibility';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }

    public function userFlags($userid = -1, $username = '') {
        $details = $this->allUserDetails($username, $userid);
        $flags = [
                    'is_logged_in' => $details['is_logged_in'],
                    'user_level' => $details['user_level'],
                    'active' => $details['active'],
                    'last_login' => $details['last_login']
                ];
        return $flags;        
    }

    public function allUserDetails($username = '', $userid = -1) {
        $db = $this->db;
        $s = 'SELECT * FROM user WHERE ';
        if($username != '') {
            $s.= ' user_name="'.$username.'" ';
        } elseif($userid != -1) {
            $s.= ' id="'.$userid.'" ';
        } else {
            $s.= ' user_name="'.$username.'" AND id="'.$userid.'"';
        }
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            return $r[0]; 
        } else {
            return false;
        }
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

    private function hasSession($sid) {
        $db = $this->db;
        $session = ['logged_in' =>false, 'user_id' => -1];
        //$sid = $_COOKIE['PHPSESSID'];
        $result = $db->query("SELECT * FROM session WHERE session_id = '".$sid."'");
        $db_session = $db->fetchAssoc( $result );
        if($db_session) {
            if( $db_session['expires'] > time()) {
                //echo "Is good!";
                $session = ['logged_in' =>true, 'user_id' => $db_session['user_id']];
            }
        }
        return $session;
    }

    public function getTags(Content $content) {
        $db = $this->db;
        $tags = '';
        if($content->contentType == 'image' || $content->contentType == 'text') {
            $s = 'SELECT tags FROM '.$content->contentType. ' WHERE id='.$content->contentId.' LIMIT 1';
            $p = $db->query($s);
            $r = $db->fetchAll($p);
            $tags = $r[0]['tags'];
        }
        return $tags;    
    }

    public function setTags(Array $content, String $tags) {
        $db = $this->db;
        $p = false;
        $error = [];
        if($content['content_type'] == 'image' || $content['content_type'] == 'text') {
            $s = 'UPDATE '.$content['content_type'].' SET tags="'.$tags.'" WHERE id='.$content['content_id'].' LIMIT 1';
            $p = $db->query($s);           
        } else {
            $error['message'] = "Invalid content type";
            $error['success'] = "false";
            $p = $error;
        }
        return $p;
    }

    private function checkAssetType(String $asset_type) {
        if(in_array($asset_type, ['text', 'image'])) {
            return true;     
        }
        
        $this->error->setMessage('Invalid asset type');
        $this->error->setCode(50);
        return $this->error;        
    }

    private function newPostId() :String {
        $postId = bin2hex(openssl_random_pseudo_bytes(32));
        return $postId;
    }

    private function randomString(Int $length) :String {
        $a = array_map(function($c) {
            return chr($c);
        }, array_merge(range(97, 97+25), range(65, 65+25)));        
        
        $k = [];
        
        for($i=0; $i<$length; $i++) {
            $k[] = $a[rand(0, 51)];
        }        
        
        return implode('', $k);
    }

    private function sendRegEmail($username, $link, $from, $reply) {
        $db = $this->db;
        $s = 'SELECT first_name, email FROM user WHERE user_name="'.$username.'" LIMIT 1';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($p) {
            $firstname = $r[0]['first_name'];
            $email = $r[0]['email'];
        }
        $subject = "Confirm your registration";
        $msg = 'Dear '.$firstname.', thanks for registering. Please click or tap on this link to confirm your registration. '.$link;
        $headers = 'From: '.$from. "\r\n" .
                    'Reply-To:' .$reply. "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
        $m = mail($email, $subject, $msg, $headers);
        return ['sent' => $m, 'email' =>  $email, 'username' => $username];
    }
    


    private function getSession(){
        $sessionId = null;
        
        if(!session_id()){
            session_start();
            session_regenerate_id();
            $sessionId = session_id();
        }
        return $sessionId;
    }

    private function clearSession(){
        session_unset(); 
        session_destroy(); 
    }

    private function getSetting($setting) {
        $s = $this->settings;
        $value = false;
        $settings = $s->getSettings();
        if(array_key_exists($setting, $settings)) {
            $value = $settings[$setting];
        }
        return $value;
    }

}   