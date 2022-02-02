<?php
/* ------------------------------------------------
| XML to BDD
|
| Ouvre et parse le fichier XML des marchés publics
| et insère son contenu en BDD
--------------------------------------------------
*/

/* -------------------------------
Ouvrir le fichier
-------------------------------
marches-reduit.xml contient :
- 8 marchés
- 6 acheteurs
- 10 titulaires

*/
// ini_set("memory_limit", "-1");
$file = 'marches.xml';
$file = '_marches_2019-06-06.xml';

if (file_exists($file))
{
  $xml = simplexml_load_file($file);
}
else
{
  exit('Echec lors de l\'ouverture du fichier xml.');
}



/* -------------------------------
Utilitaires
--------------------------------*/

// Log
$log = false;
$nan = 'nan';

function e($s)
{
  global $log;
  if ($log)
  {
    if (is_array($s))
    {
      foreach ($s as $v) echo $v . " ";
    }
    else
    {
      echo $s;
    }
    echo "<br>";
  }
}

// valeurs par defaut
function val_null ($v)
{
  if ($v == '' || $v == 'nan')
  {
    return 0;
  }
  return $v;
}

function date_zero_null ($v)
{
  if (is_nan_string($v) ||
  $v == '' ||
  $v == 0)
  {
    return '0000-00-00';
  }
  return $v;
}

function is_nan_string ($v)
{
  if (strpos($v, 'nan') !== false)
  {
    return true;
  }
  return false;
}


 /* -------------------------------
Parse
--------------------------------*/
$i = 1;
$m = 0;
$num_rows_marches = 0;

function a ($str, $o)
{
  try
  {
    $o .= '"' . $str . '",';
  }
  catch (Exception $e)
  {
    $o .= '"0",';
  }
  return $o;
}

function b ($str, $o)
{
  try
  {
    $o .= '"' . $str . '"' . "\n";
  }
  catch (Exception $e)
  {
    $o .= '"0"' . "\n";
  }
  return $o;
}

function c ($str)
{
  try { return '"' . str_replace('"', '', $str) . '",'; } catch (Exception $e) { return '"",'; }
}

function d ($str)
{
  try { return '"' . str_replace('"', '', $str) . '"' . "\n<br>"; } catch (Exception $e) { return '""' . "\n"; }
}


$o = '"id","acheteur_id","acheteur_nom","nature","procedure","lieuExecution_code","lieuExecution_typeCode","lieuExecution_nom","formePrix","titulaire","codeCPV","dureeMois","dateNotification","datePublicationDonnees","dateTransmissionDonneesEtalab","montant","objet","modifications"' . "\n";

foreach ($xml as $k=>$marche)
{
  $o .= c($marche->id);
  $o .= c($marche->acheteur->id);
  $o .= c($marche->acheteur->nom);
  $o .= c($marche->nature);
  $o .= c($marche->procedure);
  $o .= c($marche->lieuExecution->code);
  $o .= c($marche->lieuExecution->typeCode);
  $o .= c($marche->lieuExecution->nom);
  $o .= c($marche->formePrix);
  $o .= '"';
  foreach ($marche->titulaires->titulaire as $j=>$w) :
    $o .= $w->denominationSociale . ', ';
    $o .= $w->typeIdentifiant . ', ';
    $o .= $w->denominationSociale   . ', ';
  endforeach;
  $o .= '",';
  $o .= c($marche->codeCPV);
  $o .= c($marche->dureeMois);
  $o .= c($marche->dateNotification);
  $o .= c($marche->datePublicationDonnees);
  $o .= c($marche->dateTransmissionDonneesEtalab);
  $o .= c($marche->montant);
  $o .= c($marche->objet);
  $o .= d($marche->modifications);
}
echo $o
// $a=file_put_contents('export_marches_api.csv', $o);
// echo $a;
// die;

?>
