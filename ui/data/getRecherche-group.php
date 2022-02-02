<?php
// header('Content-Type: text/plain; charset=utf-8');
header('Content-Type: application/json; charset=utf-8');

$out = 0;

if (!isset($_GET)) return $out;

// if (!isset($_GET['i'])) return $out;
if (!is_numeric($_GET['lieu'])) return $out;
if (!is_numeric($_GET['forme_prix'])) return $out;
if (!is_numeric($_GET['montant_min'])) return $out;
if (!is_numeric($_GET['montant_max'])) return $out;
if (!is_numeric($_GET['nature'])) return $out;
if (!is_numeric($_GET['acheteur'])) return $out;
if (!is_numeric($_GET['titulaire'])) return $out;



/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

// LEFT, parce qu'il y a des vides
$sql = 'SELECT DISTINCT m.id, m.id_marche, m.date_notification, nom_acheteur, t.id_titulaire id_titulaire, denomination_sociale, m.code_cpv, libelle_cpv, duree_mois,  sum(montant) montant, l.code as code_dept, l.nom_lieu as nom_dept, date_notification, objet, s.latitude, s.longitude, count(m.id_marche) nb_contrats
        FROM `marche` m
        LEFT JOIN acheteur a ON a.id_acheteur = m.id_acheteur
        LEFT JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
        LEFT JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
        LEFT JOIN lieu l ON l.id_lieu = m.id_lieu_execution
        LEFT JOIN cpv c ON c.id_cpv = m.code_cpv
        LEFT JOIN  sirene s ON t.id_titulaire = s.id_sirene
        LEFT JOIN organismes o ON o.codeInsee = s.codeCommuneEtablissement';
/*
code_cpv
----------------------------------------------------*/
$code_cpv = '%';
if ( isset($_GET['code_cpv']) )
{
  $code_cpv = trim($_GET['code_cpv']) . '%';
}
$sql .= "\n" . ' WHERE code_cpv LIKE "' . $code_cpv . '"';

/*
lieu (select avec 0 par défaut)
----------------------------------------------------*/
if ( $_GET['lieu'] > 0 )
{
  $sql .= "\n" . ' AND l.code = ' . $_GET['lieu'] . ' ';
}

/*
objet
----------------------------------------------------*/
if ( isset($_GET['objet']) )
{
  $objets = explode( ",", $_GET['objet']);
  foreach ($objets as $objet):
    $sql .= ' AND  m.objet LIKE "%' . trim($objet) . '%" ';
  endforeach;
}

/*
libelle cpv
----------------------------------------------------*/
if ( isset($_GET['libelle_cpv']) )
{
  $objets = explode( ",", $_GET['libelle_cpv']);
  foreach ($objets as $objet):
    $sql .= ' AND  libelle_cpv LIKE "%' . trim($objet) . '%" ';
  endforeach;
}

/*
montant (0 par défaut)
----------------------------------------------------*/
$sql .= ' AND montant >= ' . $_GET['montant_min'] . ' ';
if ($_GET['montant_max'] > 0 && $_GET['montant_max'] > $_GET['montant_min'])
{
  $sql .= "\n" . ' AND montant <= ' . $_GET['montant_max'] . ' ';
}

/*
durée (0 par défaut)
----------------------------------------------------*/
$sql .= "\n" . ' AND duree_mois >= "' . $_GET['duree_min'] . '"';
if ($_GET['duree_max'] > 0 && $_GET['duree_max'] > $_GET['duree_min'])
{
  $sql .= "\n" . ' AND duree_mois <= "' . $_GET['duree_max'] . '"';
}

/*
date (0 par défaut)
----------------------------------------------------*/
// Pour les test, on ne doit en aucun cas avoir des données avant 2016
if ($_GET['date_min']==0) $date_min = "2016-01-01";
else $date_min = $_GET['date_min'];

$sql .= "\n" . ' AND date_notification >= "' . $date_min . '"';
if ($_GET['date_max'] > 0 && $_GET['date_max'] > $_GET['date_min'])
{
  $sql .= "\n" . ' AND date_notification <= "' . $_GET['date_max'] . '"';
}

/*
forme de prix (0 == tous)
----------------------------------------------------*/
if ($_GET['forme_prix'] > 0)
{
  $sql .= "\n" . ' AND id_forme_prix = "' . $_GET['forme_prix'] . '"';
}

/*
nature (0 == tous)
----------------------------------------------------*/
if ($_GET['nature'] > 0)
{
  $sql .= "\n" . ' AND id_nature = "' . $_GET['nature'] . '"';
}

/*
procédure (0 == tous)
----------------------------------------------------*/
if ($_GET['procedure'] > 0)
{
  $sql .= "\n" . ' AND id_procedure = "' . $_GET['procedure'] . '"';
}

/*
acheteur
----------------------------------------------------*/
if ( $_GET['acheteur'] > 0 )
{
  $sql .= "\n" . ' AND m.id_acheteur = "' . $_GET['acheteur'] . '"';
}

/*
titulaire
----------------------------------------------------*/
if ( $_GET['titulaire'] > 0 )
{
  $sql .= "\n" . ' AND t.id_titulaire = "' . $_GET['titulaire'] . '"';
}

/*
group
----------------------------------------------------*/
$sql .= "\n" . ' GROUP BY t.id_titulaire ';



// echo $sql;die;
// log queries - sudo chgrp www-data /var/www/html/
// $file = 'queries.txt';
// $log = date("y-m-d H:i:s") . "\n" . $_SERVER['QUERY_STRING'] . "\n\n" . $sql . "\n\n";
// file_put_contents($file, $log, FILE_APPEND );

/* clean
supprime les espaces et caractères qui risquent de casser le JSON */
function clean($str)
{
  try
  {
    $str = str_replace(["\n", "\r"], " ", $str);
    return htmlspecialchars($str, ENT_QUOTES);
  }
  catch (Exception $e)
  {
    return $e;
  }
}
// print_r($sql);die;
try
{
  $result = $connect->query( $sql );
  $num_rows = 0;
  $out = '{ "data" :[';

    while ( $r = mysqli_fetch_assoc( $result ) )
    {
      $num_rows++;
      // \t e \n xeneran erros no JSON
      // $objet = str_replace(["\n", "\t"], [" ", ""], $r['objet']);

      // catégorie cpv
      $categorie = "Services";
      $color = 'rgb(93, 164, 214)';

      if ($r['code_cpv'] < "50000000")
      {
        $categorie = "Travaux";
        $color = 'rgb(255, 144, 14)';
      }

      if ($r['code_cpv'] < "45000000")
      {
        $categorie = "Fournitures";
        $color = 'rgb(44, 160, 101)';
      }

      // $date_notification = substr($r['date_notification'], -2) . "-"
      //                    . substr($r['date_notification'], 5, 2) . "-"
      //                    . substr($r['date_notification'], 0, 4);
      // setlocale(LC_TIME, 'fr', 'fr_FR', 'fr_FR.ISO8859-1');
      // $date_notification =strftime("%d %b %Y", strtotime($r['date_notification']));
      // $date_notification = date("d M y", mktime(0, 0, 0, substr($r['date_notification'], -2), substr($r['date_notification'], 5, 2), substr($r['date_notification'], 0, 4)));
      list($y, $m, $d) = explode('-', $r['date_notification']);

      $out .= '{"id":"<a class=\"button  is-info small\" href=\"titulaire.php?i=' . $r['id_titulaire'] . '\">Voir</button>",' .
              // '"Objet":"' . substr(htmlspecialchars($objet, ENT_QUOTES), 0, 50) . '...",' .
              '"idN":"'               . clean($r['id_marche']) . '",' .
              '"acheteur":"'          . clean($r['nom_acheteur']) . '",' .
              '"titulaire":"'         . clean($r['denomination_sociale']) . '",' .
              '"code_cpv":"<span>'    . $r['code_cpv'] . '</span> ' . $r['libelle_cpv'] . '",' .
              '"libelle_cpv":"'       . $r['libelle_cpv'] . '",' .
              '"categorie":"'         . $categorie . '",' .
              '"color":"'             . $color . '",' .
              '"date":"'             . "$d/$m/$y" . ' <span class=\"date\">(' . $r['duree_mois'] . ' mois)</span>",' .
              '"date_notification":"' . $r['date_notification'] . '",' .
              '"code_dept":"'         . $r['code_dept'] . '",' .
              '"nom_dept":"'          . $r['nom_dept'] . '",' .
              '"latitude":"'          . $r['latitude'] . '",' .
              '"longitude":"'          . $r['longitude'] . '",' .
              // '"sql":"'          . $sql . '",' .
              '"montant":"'           . $r['montant'] . '",'.
              '"nb_contrats":"'       . $r['nb_contrats'] . '"},';
    }
    $out = substr($out, 0, -1);
    $out .="]}";

  //// No data :`(
  if ($num_rows === 0)
  {
    $out = '{ "data" :[]}';
  }
  echo $out;
}
catch ( Exception $e )
{
  echo 'Erreur : ' . $e->getMessage();
}
finally
{
  $connect->close();
}
  ?>
