<?php

/**
 * Vérifie si une date est valide.
 *
 * @param string $date La date à vérifier.
 * @return bool True si la date est valide, false sinon.
 */
function is_date($date) {
    try {
        new DateTime($date);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

?>