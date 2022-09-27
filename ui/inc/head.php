<?php
header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache');
header('X-Robots-Tag: noindex');

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

?>
<!--
Réutilisation des données essentielles des marchés publics du profil d'acheteur de Territoires Numériques

Une idée d'amélioration ? un bug ? contactez Victor VILA au 03 80 20 04 20

Juillet 2019
-->
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
<!--  <link rel="icon" type="image/png" href="favicon_TerNum.png">-->
  <link rel="stylesheet" href="assets/bulma/bulma.min.css">
  <link rel="stylesheet" href="assets/font-awesome/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <title><?php echo $title;?></title>
  <meta name="description" content="<?php echo $desc;?>">
  <meta name="robots" content="noindex, nofollow">
