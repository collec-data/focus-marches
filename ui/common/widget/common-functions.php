<?php
function nb_mois_calcul($date_min, $date_max, $config){

    //override nb_mois
    if(isset($date_min) && isset($date_max) && $date_max !== "0"){
        $nb_mois = calculateMonths($date_min, $date_max);
    } else if (isset($date_min) && !isset($date_max) || $date_max === "0"){
        $nb_mois = calculateMonths($date_min, $config['date_mise_a_jour']);
    } else if (!isset($date_min) && isset($date_max) && $date_max !== "0"){
        $nb_mois = calculateMonths($config['date_debut'], $date_max);
    } 
    // default in config.php
    else {
        $nb_mois = calculateMonths($config['date_debut'], $config['date_mise_a_jour']);
    }

    return $nb_mois;
}
