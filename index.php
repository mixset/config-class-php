<?php
/**
 @Author: Dominik Ryñko
 @Website: http://www.rynko.pl/
 @Version: 1.1
 @Contact: http://www.rynko.pl/kontakt
*/

/*
 a) E_ALL ^ E_NOTICE -> show all errors except for notices
 b) E_ALL -> show all errors
 c) 0 -> don't show any errors
*/
error_reporting(E_ALL ^ E_NOTICE);
  
// 1 -> turn on showing errors, 0 -> turn off showing errors
ini_set('display_error', '1');

$file = 'config.class.php';

if (file_exists($file)) {
    include $file;
}

// Block try..catch
try {
 
 // Create new object of class config
 $cfg = new Config('config.ini');
 
 // Initiating basic settings
 $cfg -> init();
 
 // Set example settings
 // Attention: You can set just ONE config data with key port
  $cfg -> setConfig('name1', 'val1');


 // Attention: You have to comment line above to see example below!!
 
 // First way to get Config data
 #echo '<p>First way to show settings data: </p>';
 #echo '<p>Host: '.$cfg -> getConfig(array('host'))[0].' Password: '. $cfg -> getConfig(array('password'))[0].'</p>';
 
 echo '<br>'; // Just to example looks nicely :)
 
 // Second way to get Config data 
// $data = $cfg -> getConfig(array('host', 'password'));
// echo '<p>Host: '.$data[0]. ' Password: '. $data[1].'</p>';
}
catch(Exception $e) {
 echo $e -> getMessage();
}


?>