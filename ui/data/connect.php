<?php

// Depuis PHP 8.1, mysqli lève des exceptions par défaut en cas d'erreur.
// On restaure l'ancien comportement pour garder valides les vérifications
// existantes (connect_error, $result falsy...).
mysqli_report(MYSQLI_REPORT_OFF);

if (file_exists("data/connect_dev.php"))
{
	// En mode dev, connection à la bdd spécifique au besoin
	require_once("data/connect_dev.php");
} else if (file_exists("connect_dev.php")) 
{
	require_once("connect_dev.php");
} 
else { 
	//  // paramètres par défaut (confif pour attaquer la bdd docker)
	$host = 'database';
	$user = 'user';
	$pass = 'password';
	$name = 'marches_publics';

  $connect  = new mysqli($host, $user, $pass, $name);
  $connect->query("SET NAMES 'utf8'");
}

// check
  if ( $connect->connect_error )
	  die("Erreur de connexion à la BDD : " . $connect->connect_error);


?>
