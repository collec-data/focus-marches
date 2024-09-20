<?php

require_once 'data/validateurs.php';
/**
 * Prépare le HTML affiché selon la période sélectionnée dans le wiget
 * Si la période n'est pas définie, affiche le nombre de mois depuis la date de début
 * @param string $nb_mois
 * @param string $date_min
 * @param string $date_max
 */
function texte_html_selon_periode($nb_mois, $date_min, $date_max)
{
    if ($nb_mois > 0 && !isset($date_min) && !isset($date_max)) {
        return "des <b>" . $nb_mois . " derniers mois</b>.";
    } elseif (isset($date_min) && is_date($date_min) && isset($date_max) && is_date($date_max)) {
        return "de la période du <b>" . date("d-m-Y", strtotime($date_min)) . "</b> au <b>" . date("d-m-Y", strtotime($date_max)) . "</b>.";
    } elseif (isset($date_min) && is_date($date_min) && !isset($date_max) && !is_date($date_max)) {
        return "à partir du <b>" . date("d-m-Y", strtotime($date_min)) . "</b>.";
    } elseif (!isset($date_min) && !is_date($date_min) && isset($date_max) && is_date($date_max)) {
        return "jusqu'au <b>" . date("d-m-Y", strtotime($date_max)) . "</b>.";
    } else {
        return "depuis le <b>" . date("d-m-Y", strtotime($date_min)) . "</b>.";
    }
}

function script_iframe_detect_screen_size($id_name, $iframe_code = null)
{
    //check if id_name is null
    if ($id_name == null) {
        return;
    }
    //check if iframe_code is empty
    elseif ($iframe_code == null) {
        $iframe_code = "<script>";
    } else {
        $iframe_code .= "<script>";
    }

    $iframe_code .= "document.addEventListener(\"DOMContentLoaded\", function () { function sendHeight() { var h = document.body.scrollHeight; window.parent.postMessage({ height: h }, \"*\") } window.addEventListener(\"message\", function (e) { var t = document.getElementById(\"{$id_name}\"); if (e.data.height) { var newHeight = Math.max(600, Math.min(2000, e.data.height)); t.style.height = newHeight + \"px\";} }, !1); sendHeight(); window.onresize = sendHeight });";
    $iframe_code .= "</script>";

    return $iframe_code;
}
