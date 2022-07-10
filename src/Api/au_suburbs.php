<?php

/**
 * 
 *   Non-generic endpoint - regional postcodes for South Australia
 */

require '../model/dbsettings.php';
require '../model/db.php';

use Triplesss\db\DB as Db;

header('Content-Type: application/json');

$content = trim(file_get_contents("php://input"));
$postObj = json_decode($content);
if($content == '') {
    isset($_GET['state']) ? $state = $_GET['state'] : $state = "";
} else {
    $state = $postObj->state;
}


$db = new Db();

$s = 'SELECT `suburb`, `postcode` FROM postcode WHERE state="'.$state.'" ORDER BY suburb';        
$p = $db->query($s);
$r = $db->fetchAll($p);

echo json_encode($r);

?>





























