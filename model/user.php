<?php
namespace  Triplesss\user;

use \Triplesss\connection\Connection;
use \Triplesss\notification\Notification;
use \Triplesss\repository\Repository;
use \Triplesss\feed\Feed as Feed;
use \Triplesss\filter\Filter;
use \Triplesss\error\Error;


class User {
    
    public $username = '';
    public $filter;
    public $repository;
    public $userid;
    public $level;
    public $session;
    public $connections = [];
    
    function __construct() {
        $this->repository = new Repository();
        $this->error = new Error();
        return $this;
    }

    public function setName($name) {
        $this->username = $name;
    }

    public function getName() :String {
        $this->username = $this->repository->getUserName($this);
        return $this->username;
    }

    public function addBuddy(User $user) {
        /**
         *   An actual buddy         
         **/

        return $this->addConnection($user, 2);
    }

    public function setFilter(Filter $filter) {
        $this->filter = $filter;
    }

    public function checkUserName(String $username='') { 
        $repository = $this->repository;
        return $repository->checkUserName($username);
    }

    public function add(String $username='') {
        $repository = $this->repository;
        if($username == '') {
            $username = $this->username;
            if(!$this->username || $this->username == '') {
                $this->error->setMessage('Empty username');
                $this->error->setCode(61);
                return $this->error;  
            }
        }
       
        $taken = $this->checkUserName($username);
        if(!$taken) {
            $this->username = $username;
            return $repository->addUser($username);
        } else {
            $this->error->setMessage('Username exists');
            $this->error->setCode(60);
            return $this->error;  
        }
    }

    public function delete() {
        $repository = $this->repository;
        $user = $this->user;
        return $repository->deleteUser($user);
    }

    public function update(Array $details) {
        $repository = $this->repository;
        return $repository->updateUser($details);
    }

    public function sendRegisterLink(String $username, String $from, String $reply) {
        $repository = $this->repository;
        return $repository->generateRegisterLink($username, $from, $reply);
    }

    public function sendResetLink(String $username, String $from) {
        $repository = $this->repository;
        return $repository->generateResetLink($username, $from);
    }

    public function addConnection(User $user, $type) {
         /**
         *   Add any type of connection to this user    
         **/
        
        $connection = new Connection($type);
        $connection->connect($this, $user, $type);
    }

    public function getConnection(Int $userid) {
        
        return array_filter(array_map(function($connection) use ($userid) {
            if($connection['id'] == $userid) {
                return $connection;
            }           
        }, $this->connections));
    }

    public function verify(String $key) {
        // user clicked on the register link 
        $repository = $this->repository;
        $u = $repository->verifyUser($key);
        if($u) {
            $this->username = $u['user_name'];
            $this->userid = $u['id'];
            return true;
        } else {
            return false;
        }
    }

    public function passwordToken($token) {
        $repository = $this->repository;
        $username = $this->getName();
        $tokenObj = $repository->userToken($username, 'password_reset');
        if(!$tokenObj) {
            return ['error' => 'Username not found'];
        } else {
            $check = $tokenObj['value'];
            if($check == $token) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getUserId() {
        $repository = $this->repository;
        return $repository->getUserId($this->username);
    }

    public function setUserId(Int $userid) {
        $this->userid = $userid;
    }

    public function setValue($key, $value) {
        $repository = $this->repository;
        return $repository->setUserValue($this->userid, $key, $value);
    }

    public function getValue(String $key) {
        $repository = $this->repository;
        return $repository->getUserValue($this->userid, $key);
    }

    public function login(String $username, String $password) {
        $repository = $this->repository;
        return $repository->userLogin($username, $password);
    }

    public function logout() {
        $repository = $this->repository;
        return $repository->userLogout($this);
    }

    public function getSession() {
        if($this->session) {
            return $this->session;
        } else {
            return false;
        }        
    }

    public function getLevel() {
        $details = $this->repository->userFlags($this->userid);
        $this->level = $details['user_level'];
        return $this->level;
    }

    public function getAvatar() {
        $feed = new Feed();
        $feed->setId(0); // user posts to feed_id=0 are profiles!
        $avatar = false;

        $filter = new Filter();
        $filter->setType('userid');
        $filter->setUserid($this->userid);
        $feed->setFilter($filter);

        $posts = $feed->getFilteredPosts();
        if(!$posts) {
            $posts[0] = null;
            $posts[1] = null;
        } else {
            $image_post = $posts[0];
            if($image_post['content_type'] != 'image') {
                $image_post = $posts[1];
                if($image_post['content_type'] == 'image') {
                    $avatar = $image_post['path'].'/'.$image_post['link'];
                } 
            } else {
                $avatar = $image_post['path'].'/'.$image_post['link'];
            }
        }
        return $avatar;
    }

    public function getFeeds() :Array {
        // Get the list of feeds that belong to this user
        $repository = $this->repository;
        return  $repository->getUserFeeds($this->userid);
    }

    public function getReactions(Int $count) {
        return  $this->repository->getUserReactions($this->userid, $count);
    }

    public function getConnections() {
        $connections = $this->repository->getConnectedUsers($this);
        $requests = $this->repository->getConnectionRequests($this); 
               
        $ids = array_intersect(array_column($connections, 'id'), array_column($requests, 'id'));
        $r = array_filter($requests, function($req) use($ids) {
            if(!in_array($req['id'], $ids)) {
                return $req;
            }
        });
                
        $this->connections =  array_merge( $connections, $r);
        return $this->connections; 
    }

    public function getNotifications() {
        $notifications = $this->repository->getNotifications($this);
        $this->notifications = $notifications;
        return $this->notifications; 
    }

    /*
    private function generateRegisterLink() {
        $repository = $this->repository;
        return $repository->userRegLink($this->username);
    }
    */
}