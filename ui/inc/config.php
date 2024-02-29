<?php

/* ------------------------------------
  Config : quelques variables globales
  -------------------------------------
*/

error_reporting(E_ALL);
require_once('data/model.php');
require_once('data/connect.php');
$connect->set_charset("utf8");

$config = getConfig($connect);

$formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
$formatter->setPattern('d MMMM yyyy');

//calcul nombre de mois
$nb_mois = calculateMonths($config['date_debut'], $config['date_mise_a_jour']);

// messages sur la dimension temporelle des des données
$donnees_mises_a_jour = $formatter->format(new DateTime($config['date_mise_a_jour']));
$donnees_a_partir_du = $formatter->format(new DateTime($config['date_debut']));


$path_prefix="edsa-focus-marches-new";
$protocol="https://";

function calculateMonths($date_debut, $date_mise_a_jour) {
  $debut = new DateTime($date_debut);
  $fin = new DateTime($date_mise_a_jour);
  $interval = $debut->diff($fin);
  $yearsInMonths = $interval->format('%r%y') * 12;
  $months = $interval->format('%r%m');
  $nb_mois = $yearsInMonths + $months;

  return $nb_mois;
}
