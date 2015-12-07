<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of newPHPClass
 *
 * @author rob
 */
class cars {
    //put your code here
    
    public $conf;
    public $db;
    
    function __construct() {
        $this->conf = parse_ini_file('/var/www/html/v1/config/app.ini');
        require_once $this->conf['basedir'].'class/class.db.php';
        define( 'DB_HOST', $this->conf['dbhost'] ); // set database host
        define( 'DB_USER', $this->conf['dbuser'] ); // set database user
        define( 'DB_PASS', $this->conf['dbpass'] ); // set database password
        define( 'DB_NAME', $this->conf['dbname'] ); // set database name
        define( 'SEND_ERRORS_TO', $this->conf['adminmail'] ); //set email notification email address
        define( 'DISPLAY_DEBUG', true ); //display db errors?
        $this->db = new DB();
    }
    
    /**
     * getMakes() returns an assoc. array of vehicle makes
     * @return array
     */
    function getMakes() {
        $retMakes = array();
        $makes = json_decode(file_get_contents($this->conf['basedir'].'data/makes.json'), true);
        foreach($makes['makes'] as $key=>$value){
            $retMakes[$value['name']] = $value['niceName'];
        }
        natcasesort($retMakes);
        return($retMakes);
    }
    
    function getModels($make=NULL){
        if(is_null($make)){
            return;
        }else{
            $this->db->filter($make);
            $getMake = "SELECT makeID FROM make WHERE makeName LIKE '".
                    $make ."' OR makeNice LIKE '". $make."' OR makeID = '".$make."'";
            $result = $this->db->get_row($getMake);
            $getModels = "SELECT * FROM model WHERE make = {$result[0]}";
            $list = $this->db->get_results($getModels);
            return($list);
        }
    }
    
    // SELECT DISTINCT make FROM model WHERE type IN (SELECT ID FROM category WHERE name = 'suv');
    function getModelByType($type='car', $make = null){
        // long and somewhat drawn out, but it works. 

        $query = <<<QUERY
                SELECT makeID, makeName FROM make WHERE makeID IN (
                    SELECT DISTINCT make FROM model WHERE type IN (
                        SELECT ID FROM category WHERE name = '$type'
                    )
                )
QUERY;
        
        $results = $this->db->get_results($query);
        return($results);
   }
   
   // Get all Makes
   function getAllMakes(){
        // long and somewhat drawn out, but it works. 

    $query = <<<QUERY
            SELECT makeID, makeName FROM make;
QUERY;
        
    $results = $this->db->get_results($query);
    return($results);
   }
   
    function getModelNiceName($model=NULL){
        // long and somewhat drawn out, but it works. 
        $query = <<<QUERY
                SELECT modelNice FROM model WHERE modelID = $model
QUERY;
        
        $results = $this->db->get_results($query);
        return($results[0]['modelNice']);
   }
   
   function getMakeNiceName($make){
       if(is_numeric($make)){
           $query = "SELECT makeNice FROM make WHERE makeID = $make";
       }
       $result = $this->db->get_results($query);
       return($result[0]['makeNice']);
   }
   
   function getDataByID($edid){
       //https://api.edmunds.com/api/vehicle/v2/styles/200698445?view=full&fmt=json&api_key=hydhk9uvdaa29frpw6dw9hwe
       $url = $this->conf['apiurl'].'styles/'.$edid.'?view=full&fmt=json&api_key='.$this->conf['apikey'];
       $data = json_decode(file_get_contents($url), true);
       return($data);
   }
   
   function getNameByTrim($edid){
        $url = $this->conf['apiurl'].'styles/'.$edid.'?view=basic&fmt=json&api_key='.$this->conf['apikey'];
        $data = json_decode(file_get_contents($url), true);
        $names = array();
        $names['short'] = $data['trim'];
        $names['long'] = $data['name'];
        return($names);
   }
    
}
