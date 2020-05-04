<?php
// creates and image on the server and returns the URL in JSON
// need to add a check auth!!!
// header('Access-Control-Allow-Origin: *'); // remove / restrict this this in production to prevent abuse!!!

namespace  Triplesss\image;

//require "auth.php";

//$req = $_SERVER['REQUEST_URI'];
//$action = explode("/", $req)[count(explode("/", $req)) - 1];

// POST to create
// GET to read
//ini_set('display_errors', 1);
//error_reporting(E_ALL);


class Image { 
        
        private $baseFolder;
        private $userId;
        private $imageFolder;
        private $imageData;
        private $imageName;
        private $imageType;
        private $imageMimeType;
        private $rawImageData;

        /*
        public function __construct(Int $user_id) {
            $this->$userId = $user_id;    
        }
        */

        public function __construct() {
                
        }

        public function add($imageData){
            // API call to add an image 
            //$folder = $this->selectStorage();
            $this->imageFolder = $this->selectStorage();
            $this->rawImageData = $imageData;   
            $this->setImageType($imageData);
            $this->imageName = $this->getUserId()."_".uniqid().$this->getImageType();
            file_put_contents( $this->imageFolder."/". $this->imageName, $this->imageData);
            //echo json_encode('{"folder": "'.  $this->imageFolder.'","name":"'.$imageName.'"}');   
            
            return ["folder" => $this->imageFolder, "name" => $this->imageName, "type" => $this->imageType, "mime_type" => $this->imageMimeType];  
        }

        public function get() {
            return $this;    
        }
 
        public function getImage($folder, $name) {
        
        }

        public function getImageById(Int $id) {

        }

        public function getImageName() :String {
            return $this->imageName;    
        }

        public function delete(){
                // API call to delete an image ( does NOT unlink file! )
        }

        public function setImageData($imageData) {
               // $this->imageData = $imageData;
               // $this->setImageType($imageData);
        }

        public function setRawImageData($imageData){
              $this->rawImageData = $imageData;
        }

        public function getImageData() {
                return $this->imageData;
        }
        
        public function setBaseFolder($fldr){
                $this->baseFolder = $fldr;              
        }

        public function getBaseFolder(){
               return $this->baseFolder;
        }

        public function getUserId(){
            return $this->userId; 
        }

        public function setUserId($userId){
            $this->userId = $userId;
        }

        public function getImageType() {
                return $this->imageType;
        }

        private function createFoldersRecursive($path){  
                if(@mkdir($path) or file_exists($path)) return true;
                return ($this->createFoldersRecursive(dirname($path)) and @mkdir($path));  
            
        }

        private function setImageType($imageData){
               
                $data = '';
                if(stristr($imageData, "image/png") > -1){
                        $data = base64_decode(str_replace("data:image/png;base64,", "", $imageData));
                        $this->imageType = ".png";
                        $this->imageMimeType =  "image/png";
                }elseif(stristr($imageData, "image/jpeg") > -1){
                        $data = base64_decode(str_replace("data:image/jpeg;base64,", "", $imageData));
                        $this->imageType = ".jpg";
                        $this->imageMimeType =  "image/jpeg";
                }elseif(stristr($imageData, "image/gif") > -1){
                        $data = base64_decode(str_replace("data:image/gif;base64,", "", $imageData));
                        $this->imageType = ".gif";
                        $this->imageMimeType = "image/gif";
                }
                $this->imageData = $data;               
        }

        private function selectStorage() {
               $correctTime = $this->correctTime();
               $baseFolder = $this->getBaseFolder();
               $folderPath = $baseFolder."/".date("Y", $correctTime)."/".date("m", $correctTime)."/".date("d", $correctTime);
               $this->createFoldersRecursive($folderPath);  
               return $folderPath;
        }

        private function correctTime(){
                //return  time() + 34200;
                return time();
        }

        public function getImageFileName(){

        }

        public function getImageFileType(){               
                return $this->type; 
        }

        // add image to database with ref to static file!

        // find image in DB ( GET /fullpath, GET /datestamp )

}

//$baseFolder = "../storage/";

/*

isset($_POST['image']) ? $imageData = $_POST['image'] : $imageData = "";
isset($_POST['userid']) ? $userId = $_POST['userid'] : $userID = '00';

$img = new Image();
$img->setUserId($userId);
$img->setBaseFolder("../storage");
$img->add($imageData);
*/


?>