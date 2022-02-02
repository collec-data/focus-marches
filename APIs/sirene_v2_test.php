<?php

include('getTokenSirene.php');
include('getSiret.php');
include('getSiren.php');
include('getAdresse.php');

/* -------------------------------
  Variables
  -------------------------------*/
// Elements de connexion : retourne array( access_token, token_type)
list($access_token, $token_type) = getTokenSirene();
$interval = 2; // en secondes. Max 30 requêtes / minute.

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

$host = 'localhost';
$user = '';
$pass = '';
$name = 'marches_publics';
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
LIMIT   2";

try
{
  $result = $connect->query(  $sql );
  echo "Nombre d'acheteurs à checker : " . mysqli_num_rows($result);
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
LIMIT   2";

try
{
  $result = $connect->query(  $sql );
  echo "Nombre d'acheteurs à checker : " . mysqli_num_rows($result);
  getSirene($connect, $access_token, $token_type, $result, $interval);
}
catch (Exception $e)
{
  print_r($e);
}

function getSirene ($connect, $access_token, $token_type, $liste, $interval)
{
  $i = 1;
  $is_siret = true; // size 9 (siren) + 5 (nic) = 14 (siret) ex: 01724095300038
  $is_siren = false; // normalement on a que des siret, les siren étant des cas isolés. Ex : 015651540

  while ($siret = mysqli_fetch_row ($liste))
  {
    echo "\n\nGetSirene #$i (sleep $interval s...)";
    sleep($interval);

    // Test de l'identifiant : SIRET 14 chiffres, SIREN 9 chiffres, sinon identifiant invalide
    if (strlen($siret[0]) === 9)
    {
      $is_siren = true;
      $is_siret = false;
      $etab  = getSiren($siret[0], $access_token, $token_type);
      echo "\nGet SIREN : " . $siret[0];
    }
    elseif (strlen($siret[0]) === 14)
    {
      $is_siren = false;
      $is_siret = true;
      $etab  = getSiret($siret[0], $access_token, $token_type);
      echo "\nGet SIRET : " . $siret[0];
    }
    else
    {
      echo "\nL'identifiant ne semble ni un siret ni un siret : " . $siret[0] . ", on zappe;";
      $identifiants_invalides[] = $siret[0];
      continue;
    }


    // Données de l'établissement
    // $etab  = getSiret($siret[0], $access_token, $token_type);

    if ($etab === 0)
    {
      echo "\nProblème avec le code. Continue.";
      continue;
    }

    // Parsing de la réponse
    if ($is_siret)
    {
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
        $ad = $etab->etablissement->adresseEtablissement;
      }
      catch (Exception $e)
      {
        $ad = "";
      }
    }
    else
    {
      $ee = "";
      $ad = "";
    }

    try
    {
      if ($is_siret)
      {
        $ul = $etab->etablissement->uniteLegale;
      }
      if ($is_siren)
      {
        $ul = $etab->uniteLegale;
      }
    }
    catch (Exception $e)
    {
      $ul = "";
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


  $sql = "INSERT INTO `sirene` (
    `id_sirene`, `statut`, `date`, `siren`,
    `nic`, `siret`, `dateCreationEtablissement`, `trancheEffectifsEtablissement`,
    `anneeEffectifsEtablissement`, `activitePrincipaleRegistreMetiersEtablissement`, `etatAdministratifUniteLegale`, `statutDiffusionUniteLegale`, `dateCreationUniteLegale`, `categorieJuridiqueUniteLegale`, `denominationUniteLegale`, `sigleUniteLegale`,
    `activitePrincipaleUniteLegale`, `nomenclatureActivitePrincipaleUniteLegale`, `caractereEmployeurUniteLegale`, `trancheEffectifsUniteLegale`,
    `anneeEffectifsUniteLegale`, `nicSiegeUniteLegale`, `categorieEntreprise`, `anneeCategorieEntreprise`,
    `complementAdresseEtablissement`, `numeroVoieEtablissement`, `indiceRepetitionEtablissement`, `typeVoieEtablissement`,
    `libelleVoieEtablissement`, `codePostalEtablissement`,
    `libelleCommuneEtablissement`, `codeCommuneEtablissement`, `codeCedexEtablissement`, `libelleCedexEtablissement`,
    `codePaysEtrangerEtablissement`, `libellePaysEtrangerEtablissement`, `latitude`, `longitude`)";
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
      '" . $longitude . "'
    );";

    echo "\n" . parse($ul, 'denominationUniteLegale');

    try
    {
      $result = $connect->query(  $sql );
      echo $sql . "\n\n";
    }
    catch (Exception $e)
    {
      print_r($e);
      echo $sql . "\n\n";
    }

    // https://api-adresse.data.gouv.fr/search/?q=RUE+DU+CHAMP+DES+CANNES&citycode=21054&autocomplete=1

    $i++;
  } // while sur la liste
} // getSirene

mysqli_close($connect);

echo "\n\n Identifiants invalides : \n";
print_r($identifiants_invalides);

echo "\n\n\n-------\nFIN";
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
