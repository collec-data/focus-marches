<?php
// Set locale
putenv("LANGUAGE=fr_FR.UTF-8");
putenv("LC_ALL=fr_FR.UTF-8");
putenv("LANG=fr_FR.UTF-8");
$res = setlocale(LC_ALL, 'fr_FR.UTF-8', 'fr_FR.', 'fr');
//$res = setlocale(LANGUAGE, 'fr_FR.UTF-8', 'fr_FR.', 'fr');
//$res = setlocale(LANG, 'fr_FR.UTF-8', 'fr_FR.', 'fr');

//set domain
bindtextdomain("recia", "./locale");
textdomain("recia");

//test
//echo _("Bourgogne-Franche-Comté");

