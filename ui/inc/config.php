<?php

/* ------------------------------------
  Config : quelques variables globales
  -------------------------------------
*/
require_once('data/model.php');
require_once('data/connect.php');
$connect->set_charset("utf8");

$config = getConfig($connect);

$formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
$formatter->setPattern('d MMMM Y');

//calcul nombre de mois
$debut = new DateTime($config['date_debut']);
$fin = new DateTime($config['date_mise_a_jour']);
$interval = $debut->diff($fin);
$yearsInMonths = $interval->format('%r%y') * 12;
$months = $interval->format('%r%m');
$nb_mois = $yearsInMonths + $months;

// messages sur la dimension temporelle des des données
$donnees_mises_a_jour = $formatter->format(new DateTime($config['date_mise_a_jour']));
$donnees_a_partir_du = $formatter->format(new DateTime($config['date_debut']));
?>
