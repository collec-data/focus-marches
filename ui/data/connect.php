<?php

//  // paramètres par défaut (confif pour attaquer la bdd docker)
$host = 'database';
$user = 'user';
$pass = 'password';
$name = 'marches_publics';

    // serveur easyphp sous windows
  if ($_SERVER['SERVER_NAME'] == 'localhost' or $_SERVER['SERVER_NAME'] == '127.0.0.1' )
  {
      $host = '152.228.212.208';
      $user = 'user';
      $pass = 'password';
      $name = 'marches_publics';
  }

  $connect  = new mysqli($host, $user, $pass, $name);
  $connect->query("SET NAMES 'utf8'");

  // check
  if ( $connect->connect_error )
  die("Erreur de connexion à la BDD : " . $connect->connect_error);

?>
