<?php

namespace  Triplesss\repository;

require_once('settings.php');
require_once('db.php');
require_once('error.php');

use Triplesss\settings\Settings as Settings;
use Triplesss\error\Error as Error;
use Triplesss\feed\Feed as Feed;
use Triplesss\post\Post as Post;
//use Triplesss\text\Emoji as Emoji;
use Triplesss\user\User as User;
use Triplesss\user\Member as Member;
use Triplesss\users\Members as Members;
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
use Triplesss\text\Emoji;

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

    Public function hasEmojis(String $string ) {
        // Array of emoji (v12, 2019) unicodes from https://unicode.org/emoji/charts/emoji-list.html    
        $unicodes = array( '1F600','1F603','1F604','1F601','1F606','1F605','1F923','1F602','1F642','1F643','1F609','1F60A','1F607','1F970','1F60D','1F929','1F618','1F617','263A','1F61A','1F619','1F60B','1F61B','1F61C','1F92A','1F61D','1F911','1F917','1F92D','1F92B','1F914','1F910','1F928','1F610','1F611','1F636','1F60F','1F612','1F644','1F62C','1F925','1F60C','1F614','1F62A','1F924','1F634','1F637','1F912','1F915','1F922','1F92E','1F927','1F975','1F976','1F974','1F635','1F92F','1F920','1F973','1F60E','1F913','1F9D0','1F615','1F61F','1F641','2639','1F62E','1F62F','1F632','1F633','1F97A','1F626','1F627','1F628','1F630','1F625','1F622','1F62D','1F631','1F616','1F623','1F61E','1F613','1F629','1F62B','1F971','1F624','1F621','1F620','1F92C','1F608','1F47F','1F480','2620','1F4A9','1F921','1F479','1F47A','1F47B','1F47D','1F47E','1F916','1F63A','1F638','1F639','1F63B','1F63C','1F63D','1F640','1F63F','1F63E','1F648','1F649','1F64A','1F48B','1F48C','1F498','1F49D','1F496','1F497','1F493','1F49E','1F495','1F49F','2763','1F494','2764','1F9E1','1F49B','1F49A','1F499','1F49C','1F90E','1F5A4','1F90D','1F4AF','1F4A2','1F4A5','1F4AB','1F4A6','1F4A8','1F573','1F4A3','1F4AC','1F441','FE0F','200D','1F5E8','FE0F','1F5E8','1F5EF','1F4AD','1F4A4','1F44B','1F91A','1F590','270B','1F596','1F44C','1F90F','270C','1F91E','1F91F','1F918','1F919','1F448','1F449','1F446','1F595','1F447','261D','1F44D','1F44E','270A','1F44A','1F91B','1F91C','1F44F','1F64C','1F450','1F932','1F91D','1F64F','270D','1F485','1F933','1F4AA','1F9BE','1F9BF','1F9B5','1F9B6','1F442','1F9BB','1F443','1F9E0','1F9B7','1F9B4','1F440','1F441','1F445','1F444','1F476','1F9D2','1F466','1F467','1F9D1','1F471','1F468','1F9D4','1F471','200D','2642','FE0F','1F468','200D','1F9B0','1F468','200D','1F9B1','1F468','200D','1F9B3','1F468','200D','1F9B2','1F469','1F471','200D','2640','FE0F','1F469','200D','1F9B0','1F469','200D','1F9B1','1F469','200D','1F9B3','1F469','200D','1F9B2','1F9D3','1F474','1F475','1F64D','1F64D','200D','2642','FE0F','1F64D','200D','2640','FE0F','1F64E','1F64E','200D','2642','FE0F','1F64E','200D','2640','FE0F','1F645','1F645','200D','2642','FE0F','1F645','200D','2640','FE0F','1F646','1F646','200D','2642','FE0F','1F646','200D','2640','FE0F','1F481','1F481','200D','2642','FE0F','1F481','200D','2640','FE0F','1F64B','1F64B','200D','2642','FE0F','1F64B','200D','2640','FE0F','1F9CF','1F9CF','200D','2642','FE0F','1F9CF','200D','2640','FE0F','1F647','1F647','200D','2642','FE0F','1F647','200D','2640','FE0F','1F926','1F926','200D','2642','FE0F','1F926','200D','2640','FE0F','1F937','1F937','200D','2642','FE0F','1F937','200D','2640','FE0F','1F468','200D','2695','FE0F','1F469','200D','2695','FE0F','1F468','200D','1F393','1F469','200D','1F393','1F468','200D','1F3EB','1F469','200D','1F3EB','1F468','200D','2696','FE0F','1F469','200D','2696','FE0F','1F468','200D','1F33E','1F469','200D','1F33E','1F468','200D','1F373','1F469','200D','1F373','1F468','200D','1F527','1F469','200D','1F527','1F468','200D','1F3ED','1F469','200D','1F3ED','1F468','200D','1F4BC','1F469','200D','1F4BC','1F468','200D','1F52C','1F469','200D','1F52C','1F468','200D','1F4BB','1F469','200D','1F4BB','1F468','200D','1F3A4','1F469','200D','1F3A4','1F468','200D','1F3A8','1F469','200D','1F3A8','1F468','200D','2708','FE0F','1F469','200D','2708','FE0F','1F468','200D','1F680','1F469','200D','1F680','1F468','200D','1F692','1F469','200D','1F692','1F46E','1F46E','200D','2642','FE0F','1F46E','200D','2640','FE0F','1F575','1F575','FE0F','200D','2642','FE0F','1F575','FE0F','200D','2640','FE0F','1F482','1F482','200D','2642','FE0F','1F482','200D','2640','FE0F','1F477','1F477','200D','2642','FE0F','1F477','200D','2640','FE0F','1F934','1F478','1F473','1F473','200D','2642','FE0F','1F473','200D','2640','FE0F','1F472','1F9D5','1F935','1F470','1F930','1F931','1F47C','1F385','1F936','1F9B8','1F9B8','200D','2642','FE0F','1F9B8','200D','2640','FE0F','1F9B9','1F9B9','200D','2642','FE0F','1F9B9','200D','2640','FE0F','1F9D9','1F9D9','200D','2642','FE0F','1F9D9','200D','2640','FE0F','1F9DA','1F9DA','200D','2642','FE0F','1F9DA','200D','2640','FE0F','1F9DB','1F9DB','200D','2642','FE0F','1F9DB','200D','2640','FE0F','1F9DC','1F9DC','200D','2642','FE0F','1F9DC','200D','2640','FE0F','1F9DD','1F9DD','200D','2642','FE0F','1F9DD','200D','2640','FE0F','1F9DE','1F9DE','200D','2642','FE0F','1F9DE','200D','2640','FE0F','1F9DF','1F9DF','200D','2642','FE0F','1F9DF','200D','2640','FE0F','1F486','1F486','200D','2642','FE0F','1F486','200D','2640','FE0F','1F487','1F487','200D','2642','FE0F','1F487','200D','2640','FE0F','1F6B6','1F6B6','200D','2642','FE0F','1F6B6','200D','2640','FE0F','1F9CD','1F9CD','200D','2642','FE0F','1F9CD','200D','2640','FE0F','1F9CE','1F9CE','200D','2642','FE0F','1F9CE','200D','2640','FE0F','1F468','200D','1F9AF','1F469','200D','1F9AF','1F468','200D','1F9BC','1F469','200D','1F9BC','1F468','200D','1F9BD','1F469','200D','1F9BD','1F3C3','1F3C3','200D','2642','FE0F','1F3C3','200D','2640','FE0F','1F483','1F57A','1F574','1F46F','1F46F','200D','2642','FE0F','1F46F','200D','2640','FE0F','1F9D6','1F9D6','200D','2642','FE0F','1F9D6','200D','2640','FE0F','1F9D7','1F9D7','200D','2642','FE0F','1F9D7','200D','2640','FE0F','1F93A','1F3C7','26F7','1F3C2','1F3CC','1F3CC','FE0F','200D','2642','FE0F','1F3CC','FE0F','200D','2640','FE0F','1F3C4','1F3C4','200D','2642','FE0F','1F3C4','200D','2640','FE0F','1F6A3','1F6A3','200D','2642','FE0F','1F6A3','200D','2640','FE0F','1F3CA','1F3CA','200D','2642','FE0F','1F3CA','200D','2640','FE0F','26F9','26F9','FE0F','200D','2642','FE0F','26F9','FE0F','200D','2640','FE0F','1F3CB','1F3CB','FE0F','200D','2642','FE0F','1F3CB','FE0F','200D','2640','FE0F','1F6B4','1F6B4','200D','2642','FE0F','1F6B4','200D','2640','FE0F','1F6B5','1F6B5','200D','2642','FE0F','1F6B5','200D','2640','FE0F','1F938','1F938','200D','2642','FE0F','1F938','200D','2640','FE0F','1F93C','1F93C','200D','2642','FE0F','1F93C','200D','2640','FE0F','1F93D','1F93D','200D','2642','FE0F','1F93D','200D','2640','FE0F','1F93E','1F93E','200D','2642','FE0F','1F93E','200D','2640','FE0F','1F939','1F939','200D','2642','FE0F','1F939','200D','2640','FE0F','1F9D8','1F9D8','200D','2642','FE0F','1F9D8','200D','2640','FE0F','1F6C0','1F6CC','1F9D1','200D','1F91D','200D','1F9D1','1F46D','1F46B','1F46C','1F48F','1F469','200D','2764','FE0F','200D','1F48B','200D','1F468','1F468','200D','2764','FE0F','200D','1F48B','200D','1F468','1F469','200D','2764','FE0F','200D','1F48B','200D','1F469','1F491','1F469','200D','2764','FE0F','200D','1F468','1F468','200D','2764','FE0F','200D','1F468','1F469','200D','2764','FE0F','200D','1F469','1F46A','1F468','200D','1F469','200D','1F466','1F468','200D','1F469','200D','1F467','1F468','200D','1F469','200D','1F467','200D','1F466','1F468','200D','1F469','200D','1F466','200D','1F466','1F468','200D','1F469','200D','1F467','200D','1F467','1F468','200D','1F468','200D','1F466','1F468','200D','1F468','200D','1F467','1F468','200D','1F468','200D','1F467','200D','1F466','1F468','200D','1F468','200D','1F466','200D','1F466','1F468','200D','1F468','200D','1F467','200D','1F467','1F469','200D','1F469','200D','1F466','1F469','200D','1F469','200D','1F467','1F469','200D','1F469','200D','1F467','200D','1F466','1F469','200D','1F469','200D','1F466','200D','1F466','1F469','200D','1F469','200D','1F467','200D','1F467','1F468','200D','1F466','1F468','200D','1F466','200D','1F466','1F468','200D','1F467','1F468','200D','1F467','200D','1F466','1F468','200D','1F467','200D','1F467','1F469','200D','1F466','1F469','200D','1F466','200D','1F466','1F469','200D','1F467','1F469','200D','1F467','200D','1F466','1F469','200D','1F467','200D','1F467','1F5E3','1F464','1F465','1F463','1F9B0','1F9B1','1F9B3','1F9B2','1F435','1F412','1F98D','1F9A7','1F436','1F415','1F9AE','1F415','200D','1F9BA','1F429','1F43A','1F98A','1F99D','1F431','1F408','1F981','1F42F','1F405','1F406','1F434','1F40E','1F984','1F993','1F98C','1F42E','1F402','1F403','1F404','1F437','1F416','1F417','1F43D','1F40F','1F411','1F410','1F42A','1F42B','1F999','1F992','1F418','1F98F','1F99B','1F42D','1F401','1F400','1F439','1F430','1F407','1F43F','1F994','1F987','1F43B','1F428','1F43C','1F9A5','1F9A6','1F9A8','1F998','1F9A1','1F43E','1F983','1F414','1F413','1F423','1F424','1F425','1F426','1F427','1F54A','1F985','1F986','1F9A2','1F989','1F9A9','1F99A','1F99C','1F438','1F40A','1F422','1F98E','1F40D','1F432','1F409','1F995','1F996','1F433','1F40B','1F42C','1F41F','1F420','1F421','1F988','1F419','1F41A','1F40C','1F98B','1F41B','1F41C','1F41D','1F41E','1F997','1F577','1F578','1F982','1F99F','1F9A0','1F490','1F338','1F4AE','1F3F5','1F339','1F940','1F33A','1F33B','1F33C','1F337','1F331','1F332','1F333','1F334','1F335','1F33E','1F33F','2618','1F340','1F341','1F342','1F343','1F347','1F348','1F349','1F34A','1F34B','1F34C','1F34D','1F96D','1F34E','1F34F','1F350','1F351','1F352','1F353','1F95D','1F345','1F965','1F951','1F346','1F954','1F955','1F33D','1F336','1F952','1F96C','1F966','1F9C4','1F9C5','1F344','1F95C','1F330','1F35E','1F950','1F956','1F968','1F96F','1F95E','1F9C7','1F9C0','1F356','1F357','1F969','1F953','1F354','1F35F','1F355','1F32D','1F96A','1F32E','1F32F','1F959','1F9C6','1F95A','1F373','1F958','1F372','1F963','1F957','1F37F','1F9C8','1F9C2','1F96B','1F371','1F358','1F359','1F35A','1F35B','1F35C','1F35D','1F360','1F362','1F363','1F364','1F365','1F96E','1F361','1F95F','1F960','1F961','1F980','1F99E','1F990','1F991','1F9AA','1F366','1F367','1F368','1F369','1F36A','1F382','1F370','1F9C1','1F967','1F36B','1F36C','1F36D','1F36E','1F36F','1F37C','1F95B','2615','1F375','1F376','1F37E','1F377','1F378','1F379','1F37A','1F37B','1F942','1F943','1F964','1F9C3','1F9C9','1F9CA','1F962','1F37D','1F374','1F944','1F52A','1F3FA','1F30D','1F30E','1F30F','1F310','1F5FA','1F5FE','1F9ED','1F3D4','26F0','1F30B','1F5FB','1F3D5','1F3D6','1F3DC','1F3DD','1F3DE','1F3DF','1F3DB','1F3D7','1F9F1','1F3D8','1F3DA','1F3E0','1F3E1','1F3E2','1F3E3','1F3E4','1F3E5','1F3E6','1F3E8','1F3E9','1F3EA','1F3EB','1F3EC','1F3ED','1F3EF','1F3F0','1F492','1F5FC','1F5FD','26EA','1F54C','1F6D5','1F54D','26E9','1F54B','26F2','26FA','1F301','1F303','1F3D9','1F304','1F305','1F306','1F307','1F309','2668','1F3A0','1F3A1','1F3A2','1F488','1F3AA','1F682','1F683','1F684','1F685','1F686','1F687','1F688','1F689','1F68A','1F69D','1F69E','1F68B','1F68C','1F68D','1F68E','1F690','1F691','1F692','1F693','1F694','1F695','1F696','1F697','1F698','1F699','1F69A','1F69B','1F69C','1F3CE','1F3CD','1F6F5','1F9BD','1F9BC','1F6FA','1F6B2','1F6F4','1F6F9','1F68F','1F6E3','1F6E4','1F6E2','26FD','1F6A8','1F6A5','1F6A6','1F6D1','1F6A7','2693','26F5','1F6F6','1F6A4','1F6F3','26F4','1F6E5','1F6A2','2708','1F6E9','1F6EB','1F6EC','1FA82','1F4BA','1F681','1F69F','1F6A0','1F6A1','1F6F0','1F680','1F6F8','1F6CE','1F9F3','231B','23F3','231A','23F0','23F1','23F2','1F570','1F55B','1F567','1F550','1F55C','1F551','1F55D','1F552','1F55E','1F553','1F55F','1F554','1F560','1F555','1F561','1F556','1F562','1F557','1F563','1F558','1F564','1F559','1F565','1F55A','1F566','1F311','1F312','1F313','1F314','1F315','1F316','1F317','1F318','1F319','1F31A','1F31B','1F31C','1F321','2600','1F31D','1F31E','1FA90','2B50','1F31F','1F320','1F30C','2601','26C5','26C8','1F324','1F325','1F326','1F327','1F328','1F329','1F32A','1F32B','1F32C','1F300','1F308','1F302','2602','2614','26F1','26A1','2744','2603','26C4','2604','1F525','1F4A7','1F30A','1F383','1F384','1F386','1F387','1F9E8','2728','1F388','1F389','1F38A','1F38B','1F38D','1F38E','1F38F','1F390','1F391','1F9E7','1F380','1F381','1F397','1F39F','1F3AB','1F396','1F3C6','1F3C5','1F947','1F948','1F949','26BD','26BE','1F94E','1F3C0','1F3D0','1F3C8','1F3C9','1F3BE','1F94F','1F3B3','1F3CF','1F3D1','1F3D2','1F94D','1F3D3','1F3F8','1F94A','1F94B','1F945','26F3','26F8','1F3A3','1F93F','1F3BD','1F3BF','1F6F7','1F94C','1F3AF','1FA80','1FA81','1F3B1','1F52E','1F9FF','1F3AE','1F579','1F3B0','1F3B2','1F9E9','1F9F8','2660','2665','2666','2663','265F','1F0CF','1F004','1F3B4','1F3AD','1F5BC','1F3A8','1F9F5','1F9F6','1F453','1F576','1F97D','1F97C','1F9BA','1F454','1F455','1F456','1F9E3','1F9E4','1F9E5','1F9E6','1F457','1F458','1F97B','1FA71','1FA72','1FA73','1F459','1F45A','1F45B','1F45C','1F45D','1F6CD','1F392','1F45E','1F45F','1F97E','1F97F','1F460','1F461','1FA70','1F462','1F451','1F452','1F3A9','1F393','1F9E2','26D1','1F4FF','1F484','1F48D','1F48E','1F507','1F508','1F509','1F50A','1F4E2','1F4E3','1F4EF','1F514','1F515','1F3BC','1F3B5','1F3B6','1F399','1F39A','1F39B','1F3A4','1F3A7','1F4FB','1F3B7','1F3B8','1F3B9','1F3BA','1F3BB','1FA95','1F941','1F4F1','1F4F2','260E','1F4DE','1F4DF','1F4E0','1F50B','1F50C','1F4BB','1F5A5','1F5A8','2328','1F5B1','1F5B2','1F4BD','1F4BE','1F4BF','1F4C0','1F9EE','1F3A5','1F39E','1F4FD','1F3AC','1F4FA','1F4F7','1F4F8','1F4F9','1F4FC','1F50D','1F50E','1F56F','1F4A1','1F526','1F3EE','1FA94','1F4D4','1F4D5','1F4D6','1F4D7','1F4D8','1F4D9','1F4DA','1F4D3','1F4D2','1F4C3','1F4DC','1F4C4','1F4F0','1F5DE','1F4D1','1F516','1F3F7','1F4B0','1F4B4','1F4B5','1F4B6','1F4B7','1F4B8','1F4B3','1F9FE','1F4B9','1F4B1','1F4B2','2709','1F4E7','1F4E8','1F4E9','1F4E4','1F4E5','1F4E6','1F4EB','1F4EA','1F4EC','1F4ED','1F4EE','1F5F3','270F','2712','1F58B','1F58A','1F58C','1F58D','1F4DD','1F4BC','1F4C1','1F4C2','1F5C2','1F4C5','1F4C6','1F5D2','1F5D3','1F4C7','1F4C8','1F4C9','1F4CA','1F4CB','1F4CC','1F4CD','1F4CE','1F587','1F4CF','1F4D0','2702','1F5C3','1F5C4','1F5D1','1F512','1F513','1F50F','1F510','1F511','1F5DD','1F528','1FA93','26CF','2692','1F6E0','1F5E1','2694','1F52B','1F3F9','1F6E1','1F527','1F529','2699','1F5DC','2696','1F9AF','1F517','26D3','1F9F0','1F9F2','2697','1F9EA','1F9EB','1F9EC','1F52C','1F52D','1F4E1','1F489','1FA78','1F48A','1FA79','1FA7A','1F6AA','1F6CF','1F6CB','1FA91','1F6BD','1F6BF','1F6C1','1FA92','1F9F4','1F9F7','1F9F9','1F9FA','1F9FB','1F9FC','1F9FD','1F9EF','1F6D2','1F6AC','26B0','26B1','1F5FF','1F3E7','1F6AE','1F6B0','267F','1F6B9','1F6BA','1F6BB','1F6BC','1F6BE','1F6C2','1F6C3','1F6C4','1F6C5','26A0','1F6B8','26D4','1F6AB','1F6B3','1F6AD','1F6AF','1F6B1','1F6B7','1F4F5','1F51E','2622','2623','2B06','2197','27A1','2198','2B07','2199','2B05','2196','2195','2194','21A9','21AA','2934','2935','1F503','1F504','1F519','1F51A','1F51B','1F51C','1F51D','1F6D0','269B','1F549','2721','2638','262F','271D','2626','262A','262E','1F54E','1F52F','2648','2649','264A','264B','264C','264D','264E','264F','2650','2651','2652','2653','26CE','1F500','1F501','1F502','25B6','23E9','23ED','23EF','25C0','23EA','23EE','1F53C','23EB','1F53D','23EC','23F8','23F9','23FA','23CF','1F3A6','1F505','1F506','1F4F6','1F4F3','1F4F4','2640','2642','2695','267E','267B','269C','1F531','1F4DB','1F530','2B55','2705','2611','2714','2716','274C','274E','2795','2796','2797','27B0','27BF','303D','2733','2734','2747','203C','2049','2753','2754','2755','2757','3030','00A9','00AE','2122','0023','FE0F','20E3','002A','FE0F','20E3','0030','FE0F','20E3','0031','FE0F','20E3','0032','FE0F','20E3','0033','FE0F','20E3','0034','FE0F','20E3','0035','FE0F','20E3','0036','FE0F','20E3','0037','FE0F','20E3','0038','FE0F','20E3','0039','FE0F','20E3','1F51F','1F520','1F521','1F522','1F523','1F524','1F170','1F18E','1F171','1F191','1F192','1F193','2139','1F194','24C2','1F195','1F196','1F17E','1F197','1F17F','1F198','1F199','1F19A','1F201','1F202','1F237','1F236','1F22F','1F250','1F239','1F21A','1F232','1F251','1F238','1F234','1F233','3297','3299','1F23A','1F235','1F534','1F7E0','1F7E1','1F7E2','1F535','1F7E3','1F7E4','26AB','26AA','1F7E5','1F7E7','1F7E8','1F7E9','1F7E6','1F7EA','1F7EB','2B1B','2B1C','25FC','25FB','25FE','25FD','25AA','25AB','1F536','1F537','1F538','1F539','1F53A','1F53B','1F4A0','1F518','1F533','1F532','1F3C1','1F6A9','1F38C','1F3F4','1F3F3','1F3F3','FE0F','200D','1F308','1F3F4','200D','2620','FE0F','1F1E6','1F1E8','1F1E6','1F1E9','1F1E6','1F1EA','1F1E6','1F1EB','1F1E6','1F1EC','1F1E6','1F1EE','1F1E6','1F1F1','1F1E6','1F1F2','1F1E6','1F1F4','1F1E6','1F1F6','1F1E6','1F1F7','1F1E6','1F1F8','1F1E6','1F1F9','1F1E6','1F1FA','1F1E6','1F1FC','1F1E6','1F1FD','1F1E6','1F1FF','1F1E7','1F1E6','1F1E7','1F1E7','1F1E7','1F1E9','1F1E7','1F1EA','1F1E7','1F1EB','1F1E7','1F1EC','1F1E7','1F1ED','1F1E7','1F1EE','1F1E7','1F1EF','1F1E7','1F1F1','1F1E7','1F1F2','1F1E7','1F1F3','1F1E7','1F1F4','1F1E7','1F1F6','1F1E7','1F1F7','1F1E7','1F1F8','1F1E7','1F1F9','1F1E7','1F1FB','1F1E7','1F1FC','1F1E7','1F1FE','1F1E7','1F1FF','1F1E8','1F1E6','1F1E8','1F1E8','1F1E8','1F1E9','1F1E8','1F1EB','1F1E8','1F1EC','1F1E8','1F1ED','1F1E8','1F1EE','1F1E8','1F1F0','1F1E8','1F1F1','1F1E8','1F1F2','1F1E8','1F1F3','1F1E8','1F1F4','1F1E8','1F1F5','1F1E8','1F1F7','1F1E8','1F1FA','1F1E8','1F1FB','1F1E8','1F1FC','1F1E8','1F1FD','1F1E8','1F1FE','1F1E8','1F1FF','1F1E9','1F1EA','1F1E9','1F1EC','1F1E9','1F1EF','1F1E9','1F1F0','1F1E9','1F1F2','1F1E9','1F1F4','1F1E9','1F1FF','1F1EA','1F1E6','1F1EA','1F1E8','1F1EA','1F1EA','1F1EA','1F1EC','1F1EA','1F1ED','1F1EA','1F1F7','1F1EA','1F1F8','1F1EA','1F1F9','1F1EA','1F1FA','1F1EB','1F1EE','1F1EB','1F1EF','1F1EB','1F1F0','1F1EB','1F1F2','1F1EB','1F1F4','1F1EB','1F1F7','1F1EC','1F1E6','1F1EC','1F1E7','1F1EC','1F1E9','1F1EC','1F1EA','1F1EC','1F1EB','1F1EC','1F1EC','1F1EC','1F1ED','1F1EC','1F1EE','1F1EC','1F1F1','1F1EC','1F1F2','1F1EC','1F1F3','1F1EC','1F1F5','1F1EC','1F1F6','1F1EC','1F1F7','1F1EC','1F1F8','1F1EC','1F1F9','1F1EC','1F1FA','1F1EC','1F1FC','1F1EC','1F1FE','1F1ED','1F1F0','1F1ED','1F1F2','1F1ED','1F1F3','1F1ED','1F1F7','1F1ED','1F1F9','1F1ED','1F1FA','1F1EE','1F1E8','1F1EE','1F1E9','1F1EE','1F1EA','1F1EE','1F1F1','1F1EE','1F1F2','1F1EE','1F1F3','1F1EE','1F1F4','1F1EE','1F1F6','1F1EE','1F1F7','1F1EE','1F1F8','1F1EE','1F1F9','1F1EF','1F1EA','1F1EF','1F1F2','1F1EF','1F1F4','1F1EF','1F1F5','1F1F0','1F1EA','1F1F0','1F1EC','1F1F0','1F1ED','1F1F0','1F1EE','1F1F0','1F1F2','1F1F0','1F1F3','1F1F0','1F1F5','1F1F0','1F1F7','1F1F0','1F1FC','1F1F0','1F1FE','1F1F0','1F1FF','1F1F1','1F1E6','1F1F1','1F1E7','1F1F1','1F1E8','1F1F1','1F1EE','1F1F1','1F1F0','1F1F1','1F1F7','1F1F1','1F1F8','1F1F1','1F1F9','1F1F1','1F1FA','1F1F1','1F1FB','1F1F1','1F1FE','1F1F2','1F1E6','1F1F2','1F1E8','1F1F2','1F1E9','1F1F2','1F1EA','1F1F2','1F1EB','1F1F2','1F1EC','1F1F2','1F1ED','1F1F2','1F1F0','1F1F2','1F1F1','1F1F2','1F1F2','1F1F2','1F1F3','1F1F2','1F1F4','1F1F2','1F1F5','1F1F2','1F1F6','1F1F2','1F1F7','1F1F2','1F1F8','1F1F2','1F1F9','1F1F2','1F1FA','1F1F2','1F1FB','1F1F2','1F1FC','1F1F2','1F1FD','1F1F2','1F1FE','1F1F2','1F1FF','1F1F3','1F1E6','1F1F3','1F1E8','1F1F3','1F1EA','1F1F3','1F1EB','1F1F3','1F1EC','1F1F3','1F1EE','1F1F3','1F1F1','1F1F3','1F1F4','1F1F3','1F1F5','1F1F3','1F1F7','1F1F3','1F1FA','1F1F3','1F1FF','1F1F4','1F1F2','1F1F5','1F1E6','1F1F5','1F1EA','1F1F5','1F1EB','1F1F5','1F1EC','1F1F5','1F1ED','1F1F5','1F1F0','1F1F5','1F1F1','1F1F5','1F1F2','1F1F5','1F1F3','1F1F5','1F1F7','1F1F5','1F1F8','1F1F5','1F1F9','1F1F5','1F1FC','1F1F5','1F1FE','1F1F6','1F1E6','1F1F7','1F1EA','1F1F7','1F1F4','1F1F7','1F1F8','1F1F7','1F1FA','1F1F7','1F1FC','1F1F8','1F1E6','1F1F8','1F1E7','1F1F8','1F1E8','1F1F8','1F1E9','1F1F8','1F1EA','1F1F8','1F1EC','1F1F8','1F1ED','1F1F8','1F1EE','1F1F8','1F1EF','1F1F8','1F1F0','1F1F8','1F1F1','1F1F8','1F1F2','1F1F8','1F1F3','1F1F8','1F1F4','1F1F8','1F1F7','1F1F8','1F1F8','1F1F8','1F1F9','1F1F8','1F1FB','1F1F8','1F1FD','1F1F8','1F1FE','1F1F8','1F1FF','1F1F9','1F1E6','1F1F9','1F1E8','1F1F9','1F1E9','1F1F9','1F1EB','1F1F9','1F1EC','1F1F9','1F1ED','1F1F9','1F1EF','1F1F9','1F1F0','1F1F9','1F1F1','1F1F9','1F1F2','1F1F9','1F1F3','1F1F9','1F1F4','1F1F9','1F1F7','1F1F9','1F1F9','1F1F9','1F1FB','1F1F9','1F1FC','1F1F9','1F1FF','1F1FA','1F1E6','1F1FA','1F1EC','1F1FA','1F1F2','1F1FA','1F1F3','1F1FA','1F1F8','1F1FA','1F1FE','1F1FA','1F1FF','1F1FB','1F1E6','1F1FB','1F1E8','1F1FB','1F1EA','1F1FB','1F1EC','1F1FB','1F1EE','1F1FB','1F1F3','1F1FB','1F1FA','1F1FC','1F1EB','1F1FC','1F1F8','1F1FD','1F1F0','1F1FE','1F1EA','1F1FE','1F1F9','1F1FF','1F1E6','1F1FF','1F1F2','1F1FF','1F1FC','1F3F4','E0067','E0062','E0065','E006E','E0067','E007F','1F3F4','E0067','E0062','E0073','E0063','E0074','E007F','1F3F4','E0067','E0062','E0077','E006C','E0073','E007F' );
        return preg_match( '/[\x{' . implode( '}\x{', $unicodes ) . '}]/u', $string ) ? true : false;   
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

    

    Public function getEmojis(String $text) {
        $em = new Emoji();
        return $em->Decode($text);        
    }

    //Public function getPostById(Int $id) {
    Public function getPostById(String $id) {       
        
        $db = $this->db;
        $r = [];
        $r =  array_merge($r, $this->getPostAsset('image', $id));
        $r = array_merge($r, $this->getPostAsset('text', $id));  
        
        $comments = $this->getPostComments($id);       
        //$comments = false;

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
        
        /*
        // Test dataset
        $assets = [[ 'post_id'=> $id,
        'owner' => 2, 
        'user_name' => 'fred', 
        'content_type' => 'text', 
        'content_id' => 0,
        'text_id' => 0, 
        'content' => 'test content', 
        'tags' =>    '', 
        'comment_count' => 4, 
        'likes' =>  5, 
        'creator_id' => 0,
        'visibility' =>   2,
        'date' =>  'Jan 1, 1970']];
       */ 

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
        JOIN `post` ON  post.id = content_post.post_id SET text.visibility = '.$visibility.' WHERE post.post_id="'.$p_id.'"';
        $r = $db->query($qry);

        $qry = 'UPDATE `image` JOIN content_post on content_id = image.id AND content_type="image" 
        JOIN `post` ON  post.id = content_post.post_id SET image.visibility = '.$visibility.' WHERE post.post_id="'.$p_id.'"';
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

    Public function getFeedPosts(Feed $feed, Int $visibility = 0) {
        
        $db = $this->db;
        $filter = $feed->getFilter();
        $s = 'SELECT post_id FROM feed_post WHERE feed_id='.$feed->id;
        if($filter) {
            if($filter->type == 'userid' && $filter->userid) {
                $s = 'SELECT feed_post.post_id FROM feed_post JOIN post ON post.post_id = feed_post.post_id 
                WHERE feed_id='.$feed->id.' AND post.owner='.$filter->userid.' AND feed_post.visibility >= '.$visibility ;
            }  
            
            if($filter->type == 'tag') {
                $tags = $filter->getTags();
                $s = 'SELECT feed_post.post_id FROM feed_post JOIN post ON post.post_id = feed_post.post_id 
                JOIN content_post ON  content_post.post_id = post.id 
                JOIN text ON text.id = content_post.content_id AND  content_post.content_type = "text" 
                WHERE '.$this->tagSelect($tags);
            }

            if($filter->type == 'range') { 
                $range = $filter->getRange();
                if(is_array($range)) {                    
                    $start = $range[0];
                    $rows = $range[1] - $range[0];
                    $s.= ' LIMIT '.$start.', '.$rows;
                }               
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
        if($userid > 1) {
            $s = 'SELECT owner, p1.post_id FROM feed_post
                JOIN post AS p1 ON  feed_post.post_id = p1.post_id 
                JOIN
                (SELECT DISTINCT * FROM 
                (SELECT from_id con FROM connection WHERE connection_type IN (1,2)  AND to_id = '.$userid.' AND from_id <> '.$userid.' UNION 
                SELECT to_id con FROM connection WHERE connection_type IN (1,2) AND from_id = '.$userid.' AND to_id <> '.$userid.') connected) connection 
                ON connection.con = p1.owner 
                ORDER BY p1.id DESC';
        } else {
            // it's a wall aggregator
            $s = 'SELECT owner, p1.post_id FROM feed_post
            JOIN post AS p1 ON  feed_post.post_id = p1.post_id WHERE p1.visibility = 4 ORDER BY p1.id DESC';
        }   
        
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


    Public function getPostComments(String $parent_id, Int $visibility=0, Filter $filter=null) {
        // We only support text assets at the moment, but images can be added by duplicating the query
        // and UNION it with content_type = 'image'

        $db = $this->db;
           
        $s = 'SELECT post_comment.feed_post_id parent_id, post_comment.post_id, content, owner, user.user_name, creator_id, text.visibility, text.created FROM post_comment 
        JOIN post ON post.post_id = post_comment.post_id 
        JOIN content_post ON  content_post.post_id = post.id 
        JOIN user ON user.id = owner 
        JOIN text ON text.id = content_post.content_id AND  content_post.content_type = "text"
        WHERE post_comment.feed_post_id = "'.$parent_id.'" 
        AND content_type = "text" AND (post.visibility >='.$visibility.' OR post.visibility IS NULL)';   
              
        if(!$filter) {
            $s.= " ORDER BY text.created DESC";
        }

        $p = $db->query($s);
        $r = $db->fetchAll($p);

        // convert any binary encoded comments
        $comments = array_filter(array_map(function($post) {
            $p = $post['content'];
            if(ctype_xdigit($p)) {
                @$post['content'] = hex2bin($p);
            }; 
            return $post;
        }, $r));

        return $comments;             
    }

    
    public function checkUserName(String $username) {
        $db = $this->db;
        $s = 'SELECT user_name FROM user WHERE user_name="'.$username.'"';
        $p = $db->query($s);
        $rows = $db->fetchAll($p);
        if($rows) {
            $r = $rows[0];
            return !is_null($r);
        }
        return false;                   
    }

    public function getUserValue($user_id, $key) {
        $db = $this->db;
        $s = 'SELECT key_value FROM user_detail WHERE user_id='.$user_id.' AND key_name="'.$key.'"';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            return $r[0]['key_value'];
        } else {
            return false;
        }
    }

    public function setUserValue($user_id, $key, $value) {
        $db = $this->db;
        $s = 'INSERT INTO  user_detail (user_id, key_name, key_value) VALUES ('.$user_id.', "'.$key.'", "'.$value.'") ON DUPLICATE KEY UPDATE key_value="'.$value.'"';
        $p = $db->query($s);
        return $p;
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
               
        if($userObj){
            if($hashed) {
                //$password = hash ("sha256", $password);
                $password = md5($password);                
            }
            
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
                setcookie('PHPSESSID', null, -1, '/'); 
                //session_destroy();
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }        
    }

    public function isAdminUser(Int $max_id) {
        $db = $this->db;
        if(isset( $_COOKIE['userID'])) { 
            if(intval($_COOKIE['userID']) < $max_id) {
                return true;                
            } elseif(isset( $_COOKIE['PHPSESSID'])) { 
                $s = 'SELECT user_id FROM session WHERE session_id = "'.$_COOKIE['PHPSESSID'].'"';
                $p = $db->query($s);               
                if($p) {
                    $r = $db->fetchAssoc($p);
                    $uid = $r['user_id'];
                    if($uid < $max_id) {
                        return true;
                    } else {
                        return false;
                    }                    
                } else {
                    return false;
                }
                return false;
            }
        } else {
            return false;
        }
    }

    public function getUserProfile(Int $user_id) {
        $db = $this->db;
        $s = '(SELECT "text" as content_type, content, created  FROM feed_post 
        JOIN post ON post.post_id = feed_post.post_id 
        JOIN content_post ON content_post.post_id = post.id
        JOIN text ON text.id = content_post.content_id AND content_post.content_type = "text" 
        WHERE feed_id = 0 AND post.owner = '.$user_id.' 
        ORDER BY content_post.content_id DESC LIMIT 1)
        UNION (SELECT "image" as content_type, CONCAT(image.path, "/", image.link) content, created  FROM feed_post 
        JOIN post ON post.post_id = feed_post.post_id 
        JOIN content_post ON content_post.post_id = post.id
        JOIN image ON image.id = content_post.content_id AND content_post.content_type = "image" 
        WHERE feed_id = 0 AND post.owner = '.$user_id.' 
        ORDER BY content_post.content_id DESC  LIMIT 1)';       

        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
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

    public function generateResetLink(String $username, String $from) { 
        $db = $this->db;
        $key = $this->randomString(12);
        $reply = "nobody@nowhere.com";
        $subject = "surfsouthoz password reset requested";        

        $s = 'SELECT id, first_name, email FROM user WHERE user_name="'.$username.'" LIMIT 1';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($p) {
            $firstname = $r[0]['first_name'];
            $email = $r[0]['email'];
            $user_id = $r[0]['id'];
            $k = 'INSERT INTO tokens (`user_id`, `value`, `token_type`) VALUES('.$user_id.', "'.$key.'", "password_reset")';
            $q = $db->query($k);
            $link = $this->getSetting('hostname').'/password_reset?user_id='.$user_id.'&key='.$key;

            $msg = 'We received a password reset request for your surfsouthoz account. Click or tap on this link '.$link.' to create a new password.';
            $headers = 'From: '.$from. "\r\n" .
                    'Reply-To:' .$reply. "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
           
            $m = mail($email, $subject, $msg, $headers);
            return ['sent' => $m, 'email' =>  $email, 'username' => $username];
        } else {
            return ['error' => 'Could not find user.'];
        }
       
    }

    public function userToken(String $username, String $type) {
        $db = $this->db;
        $s = 'SELECT user_id, value, sent_at FROM tokens t1 JOIN user u1 ON u1.id = t1.user_id 
              WHERE u1.user_name="'.$username.'" AND t1.token_type="'.$type.'" AND sent_at = 
              (SELECT MAX(sent_at) FROM tokens t2 WHERE user_id = u1.id) LIMIT 1';
        $p = $db->query($s);
        $rv = $db->fetchAll($p);
        if($rv) {
            return $rv[0];    
        } else {
            return ['error' => 'Could not find token.'];
        }          
    }

    public function getConnectedUsers(User $user, Connection $connection = null) { 
        $db = $this->db;
        $user_id = $user->userid;
        $connection_level = 2; // friend by default
        if($connection) {
            $connection_level = $connection->type;
        }       
       
        /* Orignal query has performancs issues */
        
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
            JOIN connection ON connection.from_id = notification.from_user_id  AND to_user_id='.$user_id. ' ORDER BY timestamp DESC LIMIT 300';
        }

        if($user_level == 1){
            // admin user - reports
            $s = 'SELECT DISTINCT from_user_id, to_user_id, type, message, timestamp  FROM notification 
            JOIN connection ON connection.from_id = notification.from_user_id  AND to_user_id='.$user_id. ' ORDER BY timestamp DESC LIMIT 300';
        }

        if($user_level == 5){
            // show all  notifications from user_id = 1 and 2
            $s = 'SELECT * FROM (SELECT DISTINCT from_user_id, to_user_id, type, message, timestamp  FROM notification 
            JOIN connection ON connection.from_id = notification.from_user_id  AND connection.to_id = '.$user_id.' 
            UNION 
            SELECT DISTINCT from_user_id, to_user_id, type, message, timestamp  FROM notification             
            WHERE to_user_id IN(1,2) AND from_user_id < 2 
            UNION 
            SELECT DISTINCT from_user_id, to_user_id, type, message, timestamp  FROM notification 
            WHERE to_user_id = '.$user_id.' AND type IN (6,7,8,9)
            ) t1 
            JOIN user u ON u.id = to_user_id AND timestamp > u.created_on 
            WHERE t1.type NOT IN (8,9,10,11,12,15)                   
            ORDER BY t1.timestamp DESC LIMIT 200';        
            
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
                return intval($member_id);
            } 
        } else {
            return $db->lastInsertedID();
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

    public function getMembers(Members $members, $active=1) {
        $filter = $members->getFilter(); // not yet implemented
        $db = $this->db; 
        $s = 'SELECT id, user_name, first_name, last_name, email, member_id, customer_id, status FROM member
                JOIN user ON member.user_id = user.id';
                
        if($active == 1) {
            $s.= ' AND NOT ISNULL(customer_id) AND member.active=1';
        }
                        
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
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
            if($db->query($s)) {
                $p = ['success' => true];
            } else {
                $p = ['success' => false];
            }         
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