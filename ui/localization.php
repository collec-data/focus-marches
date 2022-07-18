<?php
// Set locale
putenv("LC_ALL=fr_FR.UTF-8");
putenv("LANGUAGE=");
putenv("LANG=fr_FR.UTF-8");
$res = setlocale(LC_ALL, 'fr_FR.UTF-8', 'fr_FR.', 'fr');

//set domain
bindtextdomain("megalis", "./locale");
textdomain("megalis");

//test
echo _("Bourgogne-Franche-Comté");

