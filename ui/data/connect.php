<?php
  // paramètres
  if ($_SERVER['SERVER_NAME'] == 'localhost')
  {
      $host = 'localhost';
      $user = '';
      $pass = '';
      $name = 'marches_publics';
  }

  // serveur easyphp sous windows
  if ($_SERVER['SERVER_NAME'] == '127.0.0.1')
  {
      $host = 'localhost';
      $user = '';
      $pass = '';
      $name = 'marches_publics';
  }

  // serveur OVH du labo
  if ($_SERVER['SERVER_NAME'] == 'labo.e-bourgogne.fr')
  {
    $host = '';
    $user = '';
    $pass = '';
    $name = '';
  }

  // serveur OVH du labo
  if ($_SERVER['SERVER_NAME'] == 'focus-marches.ternum-bfc.fr')
  {
    $host = '';
    $user = '';
    $pass = '';
    $name = '';
  }

  $connect  = new mysqli($host, $user, $pass, $name);
  $connect->query("SET NAMES 'utf8'");

  // check
  if ( $connect->connect_error )
  die("Erreur de connexion à la BDD : " . $connect->connect_error);

?>
