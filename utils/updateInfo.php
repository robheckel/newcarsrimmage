<?php
header('Content-Type: text/plain');
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$conf = parse_ini_file('/var/www/html/v1/config/app.ini');
require_once $conf['basedir'].'class/class.cars.php';
require_once $conf['basedir'].'class/class.db.php';
define( 'DB_HOST', $conf['dbhost'] ); // set database host
define( 'DB_USER', $conf['dbuser'] ); // set database user
define( 'DB_PASS', $conf['dbpass'] ); // set database password
define( 'DB_NAME', $conf['dbname'] ); // set database name
define( 'SEND_ERRORS_TO', $conf['adminmail'] ); //set email notification email address
define( 'DISPLAY_DEBUG', true ); //display db errors?
$db = new DB();
$car = new cars();
$makes = $car->getMakes();

/*
foreach( $makes as $pretty=>$nice ){
    $sel = "SELECT * FROM make WHERE makeNice = '$nice'";
    $count = $db->num_rows($sel);
    if($count == 0){
        $in = array(
            'makeName' => $pretty,
            'makeNice' => $nice
        );
        if($db->insert('make', $in)){
            echo "ADDED $pretty\n";
        }
        
    }
    unset($sel);
    unset($count);
    unset($in);
}
*/
// https://api.edmunds.com/api/vehicle/v2/bmw/models?state=new&year=2015&category=Sedan&view=basic&fmt=json&api_key=hydhk9uvdaa29frpw6dw9hwe

$types = array(
    1 => 'car',
    2 => 'truck',
    3 => 'suv',
    4 => 'van'
);

foreach($makes as $key=>$value){
    $getMakeID = "SELECT makeID FROM make WHERE makeNice LIKE '". $value."'";
    $res = $db->get_row($getMakeID);
    echo "$value\n";
    echo "{$res[0]}\n";
    foreach($types as $id=>$name){
        $url = $conf['apiurl'].$value.'/models?state=new&year='.
            $conf['apiyr'].'&category='.$conf[$name].
                '&view=basic&fmt=json&api_key=' . $conf['apikey'];
        $results = json_decode(file_get_contents($url), true);
        sleep(2);
        foreach($results['models'] as $x=>$model){
            $cq = "SELECT * FROM model WHERE makeID = '{$res[0]}' AND modelNice = '{$model['niceName']}'";
            $check = $db->num_rows($query);
            if($check == 0){
                $add = array(
                    'make' => $res['0'],
                    'name' => $model['name'],
                    'modelNice' => $model['niceName'],
                    'type' => $id
                );
                $db->insert('model', $add);
                echo "Added {$model['name']}\n";
            }
            unset($check);
            unset($cq);
        }
        unset($results);
        unset($url);
    }
    unset($res);
}
