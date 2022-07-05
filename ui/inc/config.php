<?php

/* ------------------------------------
  Config : quelques variables globales
  -------------------------------------
*/

// Choix de l'allemand
putenv('LC_ALL=fr_FR');
setlocale(LC_ALL, 'fr_FR');

// Spécifie la localisation des tables de traduction
bindtextdomain("focusApp", "./locale");

// Choisit le domaine
textdomain("focusApp");


//TODO automatiser ça
// Nombre de mois glissants dont les requêtes doivent tenir compte
$nb_mois = 41;

// messages sur la dimension temporelle des des données
$donnees_mises_a_jour = "18 Mai 2022";
$donnees_a_partir_du = "01 janvier 2019";

// Affichage d'un message de test
echo gettext("Bienvenue dans mon application PHP");

// Ou utilisez l'alias _() pour remplacer gettext()
echo _("Passez une bonne journée");




?>
