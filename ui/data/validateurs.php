<?php

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

/**
 * Vérifie si une date est valide.
 *
 * @param string $date La date à vérifier.
 * @return bool True si la date est valide, false sinon.
 */
function is_date($date) {
    try {
        return validateDate($date, 'Y-m-d');
    } catch (Exception $e) {
        return false;
    }
}

?>