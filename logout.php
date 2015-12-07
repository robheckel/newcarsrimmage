<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

session_start();
session_destroy();
$config = parse_ini_file('config/app.ini');
header("Location: ".$config['baseurl']."?err=logout");