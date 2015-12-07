<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$conf = parse_ini_file('/var/www/html/v1/config/app.ini');
require_once $conf['basedir'].'/class/class.cars.php';
$cars = new cars();

$action = isset($_GET['action']) ? $_GET['action'] : NULL;
$type = isset($_GET['type']) ? $_GET['type'] : NULL;
$make = isset($_POST['make']) ? $_POST['make'] : NULL;
$model = isset($_POST['model']) ? $_POST['model'] : NULL;

if($action == 'modelsByType'){
    if($type === 'all'){
        $mbt = $cars->getAllMakes();
        foreach($mbt as $k=>$v){
            echo "<option value=\"{$v["makeID"]}\">{$v["makeName"]}</option>\n";
        }
    }else{
        $mbt = $cars->getModelByType($type);
        foreach($mbt as $k=>$v){
            echo "<option value=\"{$v["makeID"]}\">{$v["makeName"]}</option>\n";
        }
    }
}

if(!is_null($make) && is_null($model)){
    $query = "SELECT * FROM model WHERE make ='".$make."'";
    $type = isset($_POST['type']) ? $_POST['type'] : NULL;
    if(isset($type) AND $type !== 'all'){
        $query .= " AND type IN (SELECT id FROM category WHERE name = '" . $type ."')";
    }
    $models = $cars->db->get_results($query);
    echo "<option>Please select a model</option>";
    foreach($models as $key=>$value){
        echo "<option value=\"{$value["modelID"]}\">{$value["name"]}</option>\n";
    }
}

//Get model trims
if(!is_null($model)){
    $niceModel = $cars->getModelNiceName($model);
    $niceMake = $cars->getMakeNiceName($make);
    $year = $conf['apiyr'];
    //https://api.edmunds.com/api/vehicle/v2/honda/civic/2015/styles?state=new&view=full&fmt=json&api_key=hydhk9uvdaa29frpw6dw9hwe
    $url = $conf['apiurl'].$niceMake.'/'.$niceModel.'/'.$conf['apiyr'].
            '/styles?state=new&view=full&fmt=json&api_key='. $conf['apikey'];
    $trims = json_decode(file_get_contents($url), true);
    echo "<option>Please select a trim</option>";
    foreach($trims['styles'] as $key=>$value){
        $edmunds = $value['id'];
        $name = $value['name'];
        $msrp = "MSRP: $". number_format($value['price']['baseMSRP'], 2, '.', ',');
        $name = $msrp. " :: " .$name;
        echo "<option value=\"{$value['id']}\">$name</option>\n";
    }
}