<?php
/*
   Copyright (c) 2003,2004,2005,2009 Danilo Segan <danilo@kvota.net>.
   Copyright (c) 2005,2006 Steven Armstrong <sa@c-area.ch>

   This file is part of PHP-gettext.

   PHP-gettext is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   PHP-gettext is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with PHP-gettext; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

*/
error_reporting(E_ALL | E_STRICT);

// define constants
define('PROJECT_DIR', realpath('./'));
define('LOCALE_DIR', '/locale');
define('DEFAULT_LOCALE', 'fr_FR');

//require_once('gettext');

$supported_locales = array('en_US','fr_FR');
$encoding = 'UTF-8';

setlocale(LC_ALL, 'fr_FR.UTF8');

$locale = (isset($_GET['lang']))? $_GET['lang'] : DEFAULT_LOCALE;

// gettext setup
//T_setlocale(LC_MESSAGES, $locale);
// Set the text domain as 'messages'
$domain = 'megalis';
bindtextdomain($domain, LOCALE_DIR);

// bind_textdomain_codeset is supported only in PHP 4.2.0+
if (function_exists('bind_textdomain_codeset')){
    bind_textdomain_codeset($domain, $encoding);
}

textdomain($domain);

// Affichage d'un message de test
echo gettext("Bienvenue dans mon application PHP");
echo _("Megalis Bretagne");

?>
