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
class user {
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
    
    public function getMyRequests($uid){
        $query = "SELECT * FROM requests WHERE customer_id = $uid";
        $get = $this->db->get_results($query);
        return($get);
    }
    
    public function getMyCars($uid){
        $query = "SELECT request_id, customer_id, car_year, car_make, car_model, car_trim, makeName, name "
               . "FROM requests, make, model "
               . "WHERE customer_id = $uid AND car_make = makeID and car_model = modelID";
        $get = $this->db->get_results($query);
        return($get);
    }
    
    public function getInventory($uid){
        $query = "SELECT inventory_id, dealer_id, car_year, car_make, car_model, car_trim, makeName, name "
               . "FROM inventory, make, model "
               . "WHERE dealer_id = $uid AND car_make = makeID and car_model = modelID";
        $get = $this->db->get_results($query);
        return($get);
    }
    
    public function getRequest($rid = NULL){
        $query = "SELECT request_id, customer_id, car_year, car_make, car_model, car_trim, makeName, name "
               . "FROM requests, make, model "
               . "WHERE request_id = $rid AND car_make = makeID and car_model = modelID";
        $get = $this->db->get_results($query);
        return($get);
    }
    
}
