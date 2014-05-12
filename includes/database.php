<?php
  $config = parse_ini_file('config/config.ini', true);
  $mysql_user = $config['Database']['mysql_user'];
  $mysql_pass = $config['Database']['mysql_password'];
  $mysql_host     = $config['Database']['mysql_host'];
  $mysql_database = $config['Database']['mysql_database']; 

  $database = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_database);
  if ($database->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

?>
