<?php

include('getTokenSirene.php');
include('getSiret.php');
include('getSiren.php');
include('getAdresse.php');
include('getInfogreffe.php');

/* -------------------------------
  Variables
  -------------------------------*/

//on augmente le timeout pour gérer bcp d'insert
ini_set('max_execution_time', '500');

// Elements de connexion : retourne array( access_token, token_type)
list($access_token, $token_type) = getTokenSirene();
$interval = 2; // en secondes. Max 30 requêtes / minute.
$identifiants_invalides = []; // pour repporting à la fin

function parse ($parent, $element)
{
  try
  {
    if (isset($parent->$element))
    {
      return $parent->$element;
    }
    return "-";
  }
  catch (exception $e)
  {
    return "-";
  }
}


/* -------------------------------
BDD - connexion
-------------------------------*/

// paramètres par défaut (confif pour attaquer la bdd docker)
$host = 'database';
$user = 'user';
$pass = 'password';
$name = 'marches_publics';

// serveur easyphp sous windows
if ($_SERVER['SERVER_NAME'] == 'localhost' or $_SERVER['SERVER_NAME'] == '127.0.0.1' )
{
  $host = 'localhost';
  $user = 'root';
  $pass = '';
  $name = 'marches_publics';
}




$connect  = new mysqli($host, $user, $pass, $name);
$connect->query("SET NAMES 'utf8'");

// check
if ( $connect->connect_error )
{
  die("Erreur de connexion à la BDD : " . $connect->connect_error);
}


/* -------------------------------
    Acheteurs à checker
  -------------------------------*/
$sql = "SELECT  `id_acheteur`
FROM    `acheteur`
WHERE   `id_acheteur`
NOT IN  (SELECT `id_sirene` FROM `sirene`)
LIMIT   500";

try
{
  $result = $connect->query(  $sql );
  echo "<br>Nombre d'acheteurs à checker : " . mysqli_num_rows($result);
  getSirene($connect, $access_token, $token_type, $result, $interval);
}
catch (Exception $e)
{
  print_r($e);
}



/* -------------------------------
  Titulaires à checker
  -------------------------------*/
$sql = "SELECT  `id_titulaire`
FROM    `titulaire`
WHERE   `id_titulaire`
NOT IN  (SELECT `id_sirene` FROM `sirene`)
LIMIT   500";


try
{
  $result = $connect->query(  $sql );
  echo "<br><br><br>Nombre de titulaires à checker : " . mysqli_num_rows($result);
  getSirene($connect, $access_token, $token_type, $result, $interval);
}
catch (Exception $e)
{
  print_r($e);
}

function getSirene ($connect, $access_token, $token_type, $liste, $interval)
{
  $i = 1;
  echo "<br><br> DEBUT getSirene";
  while ($siret = mysqli_fetch_row ($liste))
  {
    echo "<br>GetSirene #$i (sleep $interval s...)";
    //sleep($interval);

    /*
    Test de l'identifiant :
    --------------------------
    SIRET 14 chiffres, SIREN 9 chiffres, sinon identifiant invalide
    size 9 (siren) + 5 (nic) = 14 (siret) ex: 01724095300038
    normalement on a que des siret, les siren étant des cas isolés. Ex : 015651540
    Si SIREN, on peut reconstituer le SIRET du siège
    */

    if (strlen($siret[0]) === 9)
    {
      $etab  = getSiren($siret[0], $access_token, $token_type);
      $nic   = $etab->uniteLegale->periodesUniteLegale[0]->nicSiegeUniteLegale;
      echo "\nSiret reconstitué : " . $siret[0] . " + " . $nic;
      $siret[0] = $siret[0] . $nic;
    }

    if (strlen($siret[0]) !== 14)
    {
      echo "\nL'identifiant ne semble ni un siret ni un siret : " . $siret[0] . ", on zappe;";
      $identifiants_invalides[] = $siret[0];
      continue;
    }

    echo "\nGet siret : " . $siret[0];

    // Données de l'établissement
    $etab  = getSiret($siret[0], $access_token, $token_type);

    if ($etab === 0)
    {
      echo "\nProblème avec le code. Continue.";
      continue;
    }

    // Parsing de la réponse
    try
    {
      $ee = $etab->etablissement;
    }
    catch (Exception $e)
    {
      $ee = "";
    }

    try
    {
      $ul = $etab->etablissement->uniteLegale;
    }
    catch (Exception $e)
    {
      $ul = "";
    }

    try
    {
      $ad = $etab->etablissement->adresseEtablissement;
    }
    catch (Exception $e)
    {
      $ad = "";
    }



  if($etab->header->statut != 200)
  {
    echo "Pas de données";
  }

  // latitude et longitude
  $longitude = "";
  $latitude = "";

  if ($ad)
  {
    switch($ad->typeVoieEtablissement)
    {
      case 'RUE' : $typeVoie = 'rue';
      case 'AV' : $typeVoie = 'avenue';
      case 'PL' : $typeVoie = 'place';
      case 'IMP' : $typeVoie = 'impasse';
      case 'RTE' : $typeVoie = 'route';
      case 'SQ' : $typeVoie = 'square';
      case 'ESP' : $typeVoie = 'esplanade';
      case 'RLE' : $typeVoie = 'ruelle';
      case 'QUAI' : $typeVoie = 'quai';
      default : $typeVoie = 'rue';
    }

    $adresse =  $typeVoie . " ";
    if ($ad->numeroVoieEtablissement != "-") $adresse .= $ad->numeroVoieEtablissement . " ";
    if ($ad->libelleVoieEtablissement != "-") $adresse .= $ad->libelleVoieEtablissement . " ";
    if ($ad->libelleCommuneEtablissement != "-") $adresse .= $ad->libelleCommuneEtablissement;

    list($longitude, $latitude) = getAdresse($adresse , $ad->codeCommuneEtablissement) ;
  }


  //// Données infogreffe
    $ig = "";
  $infogreffe = getInfogreffe(substr($siret[0], 0, 9));

  if (!empty( $infogreffe->records)) {
    try
    {
      $ig = $infogreffe->records[0]->fields;
    }
    catch (Exception $e)
    {
      echo "<br>Infogreffe : ko";
      $ig = "";
    }
  }

  $sql = "INSERT INTO `sirene` (
    `id_sirene`, `statut`, `date`, `siren`,
    `nic`, `siret`, `dateCreationEtablissement`, `trancheEffectifsEtablissement`,
    `anneeEffectifsEtablissement`, `activitePrincipaleRegistreMetiersEtablissement`, `etatAdministratifUniteLegale`, `statutDiffusionUniteLegale`, `dateCreationUniteLegale`, `categorieJuridiqueUniteLegale`, `denominationUniteLegale`, `sigleUniteLegale`,
    `activitePrincipaleUniteLegale`, `nomenclatureActivitePrincipaleUniteLegale`, `caractereEmployeurUniteLegale`, `trancheEffectifsUniteLegale`,
    `anneeEffectifsUniteLegale`, `nicSiegeUniteLegale`, `categorieEntreprise`, `anneeCategorieEntreprise`,
    `complementAdresseEtablissement`, `numeroVoieEtablissement`, `indiceRepetitionEtablissement`, `typeVoieEtablissement`,
    `libelleVoieEtablissement`, `codePostalEtablissement`,
    `libelleCommuneEtablissement`, `codeCommuneEtablissement`, `codeCedexEtablissement`, `libelleCedexEtablissement`,
    `codePaysEtrangerEtablissement`, `libellePaysEtrangerEtablissement`, `latitude`, `longitude`, `millesime_1`, `millesime_2`, `millesime_3`, `ca_1`, `ca_2`, `ca_3`, `resultat_1`, `resultat_2`, `resultat_3`, `effectif_1`, `effectif_2`, `effectif_3`, `fiche_identite`)";

    $sql .= "VALUES (
      " . $siret[0] . ",
      " . parse($etab->header, 'statut') . ",
      '" . date('Y-m-d') . "',
      " . parse($ee, 'siren') . ",

      " . parse($ee, 'nic') . ", " . parse($ee, 'siret') . ",
      '" . parse($ee, 'dateCreationEtablissement') . "',
      '" . parse($ee, 'trancheEffectifsEtablissement') . "',

      '" . parse($ee, 'anneeEffectifsEtablissement') . "',
      '" . parse($ee, 'activitePrincipaleRegistreMetiersEtablissement') . "',
      '" . parse($ee, 'etatAdministratifUniteLegale') . "',
      '" . parse($ee, 'statutDiffusionUniteLegale') . "',

      '" . parse($ul, 'dateCreationUniteLegale') . "',
      '" . parse($ul, 'categorieJuridiqueUniteLegale') . "',
      '" . $connect->real_escape_string(parse($ul, 'denominationUniteLegale')) . "',
      '" . $connect->real_escape_string(parse($ul, 'sigleUniteLegale')) . "',

      '" . parse($ul, 'activitePrincipaleUniteLegale') . "',
      '" . parse($ul, 'nomenclatureActivitePrincipaleUniteLegale') . "',
      '" . parse($ul, 'caractereEmployeurUniteLegale') . "',
      '" . parse($ul, 'trancheEffectifsUniteLegale') . "',

      '" . parse($ul, 'anneeEffectifsUniteLegale') . "',
      '" . parse($ul, 'nicSiegeUniteLegale') . "',
      '" . parse($ul, 'categorieEntreprise') . "',
      '" . parse($ul, 'anneeCategorieEntreprise') . "',

      '" . $connect->real_escape_string(parse($ad, 'complementAdresseEtablissement')) . "',
      '" . parse($ad, 'numeroVoieEtablissement') . "',
      '" . parse($ad, 'indiceRepetitionEtablissement') . "',
      '" . parse($ad, 'typeVoieEtablissement') . "',
      '" . $connect->real_escape_string(parse($ad, 'libelleVoieEtablissement')) . "',
      '" . parse($ad, 'codePostalEtablissement') . "',

      '" . $connect->real_escape_string(parse($ad, 'libelleCommuneEtablissement')) . "',
      '" . parse($ad, 'codeCommuneEtablissement') . "',
      '" . parse($ad, 'codeCedexEtablissement') . "',
      '" . $connect->real_escape_string(parse($ad, 'libelleCedexEtablissement')) . "',

      '" . parse($ad, 'codePaysEtrangerEtablissement') . "',
      '" . $connect->real_escape_string(parse($ad, 'libellePaysEtrangerEtablissement')) . "',
      '" . $latitude . "',
      '" . $longitude . "',

      '" . parse($ig, 'millesime_1') . "',
      '" . parse($ig, 'millesime_2') . "',
      '" . parse($ig, 'millesime_3') . "',
      '" . parse($ig, 'ca_1') . "',
      '" . parse($ig, 'ca_2') . "',
      '" . parse($ig, 'ca_3') . "',
      '" . parse($ig, 'resultat_1') . "',
      '" . parse($ig, 'resultat_2') . "',
      '" . parse($ig, 'resultat_3') . "',
      '" . parse($ig, 'effectif_1') . "',
      '" . parse($ig, 'effectif_2') . "',
      '" . parse($ig, 'effectif_3') . "',
      '" . parse($ig, 'fiche_identite') . "' );";

    echo "\n" . parse($ul, 'denominationUniteLegale');

    try
    {
      $result = $connect->query(  $sql );
      // echo $sql . "\n\n";die;
    }
    catch (Exception $e)
    {
      print_r($e);
      echo "\n" . $sql . "\n\n";
    }

    // https://api-adresse.data.gouv.fr/search/?q=RUE+DU+CHAMP+DES+CANNES&citycode=21054&autocomplete=1

    $i++;
  } // while sur la liste
} // getSirene

mysqli_close($connect);

//echo "<br><br> Identifiants invalides : <br>";
//print_r($identifiants_invalides);

echo "<br><br><br>-------<br>FIN";
  /*
  CREATE TABLE `sirene` (
  `id_sirene` int(10) UNSIGNED NOT NULL,
  `statut` INT(3) UNSIGNED,
  `date` DATE,
  `siren` INT(9) UNSIGNED,
  `nic` INT(5) UNSIGNED,
  `siret` INT(14) UNSIGNED,
  `dateCreationEtablissement` DATE NULL,
  `trancheEffectifsEtablissement` varchar(10) NULL,
  `anneeEffectifsEtablissement` varchar(10) NULL,
  `activitePrincipaleRegistreMetiersEtablissement` varchar(10) NULL,

  `etatAdministratifUniteLegale` varchar(10) NULL,
  `statutDiffusionUniteLegale` varchar(10) NULL,
  `dateCreationUniteLegale` DATE NULL,
  `categorieJuridiqueUniteLegale` varchar(10) NULL,
  `denominationUniteLegale` varchar(10) NULL,
  `sigleUniteLegale` varchar(10) NULL,
  `activitePrincipaleUniteLegale` varchar(10) NULL,
  `nomenclatureActivitePrincipaleUniteLegale` varchar(10) NULL,
  `caractereEmployeurUniteLegale` varchar(10) NULL,
  `trancheEffectifsUniteLegale` varchar(10) NULL,
  `anneeEffectifsUniteLegale` varchar(10) NULL,
  `nicSiegeUniteLegale` varchar(10) NULL,
  `categorieEntreprise` varchar(10) NULL,
  `anneeCategorieEntreprise` varchar(4) NULL,

  `complementAdresseEtablissement` varchar(20) NULL,
  `numeroVoieEtablissement` varchar(10) NULL,
  `indiceRepetitionEtablissement` varchar(10) NULL,
  `typeVoieEtablissement` varchar(10) NULL,
  `libelleCommuneEtablissement` varchar(100) NULL,
  `codeCommuneEtablissement` varchar(10) NULL,
  `codeCedexEtablissement` varchar(10) NULL,
  `libelleCedexEtablissement` varchar(10) NULL,
  `codePaysEtrangerEtablissement` varchar(10) NULL,
  `libellePaysEtrangerEtablissement` varchar(10) NULL

  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `sirene` ADD PRIMARY KEY (`id_sirene`);

  ALTER TABLE `sirene` MODIFY `id_sirene` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;



  */

  ?>
