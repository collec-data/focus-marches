<?php
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST)) return "[{}]";
if (!isset($_POST['id'])) return "[{}]";
// if (!is_numeric($_POST['id'])) return "[{}]";

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

// LEFT, parce qu'il y a des vides
$sql = 'SELECT m.id_marche, objet, m.id_acheteur, t.id_titulaire, nom_acheteur,
        denomination_sociale, m.code_cpv, libelle_cpv, duree_mois,
        montant, date_notification, id_titulaire, type_identifiant,
        nom_procedure, nom_nature, nom_forme_prix, l.code as code_dept, l.nom_lieu as nom_dept
        FROM `marche` m
        LEFT JOIN acheteur a ON a.id_acheteur = m.id_acheteur
        LEFT JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
        LEFT JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
        LEFT JOIN cpv c ON c.id_cpv = m.code_cpv
        LEFT JOIN lieu l ON l.id_lieu = m.id_lieu_execution
        LEFT JOIN procedure_marche p ON p.id_procedure = m.id_procedure
        LEFT JOIN nature n ON n.id_nature = m.id_nature
        LEFT JOIN forme_prix f ON f.id_forme_prix = m.id_forme_prix
        WHERE m.id_marche = "' . $_POST['id'] . '"';

// echo $sql;die;
// log queries - sudo chgrp www-data /var/www/html/
// $file = 'queries.txt';
// $log = date("y-m_d H:i:s") . "\n" . $sql . "\n\n";
// file_put_contents($file, $log, FILE_APPEND );

try
{
  $result = $connect->query( $sql );

  while ( $r = mysqli_fetch_assoc( $result ) )
  {
    $out = '{"m_id":"'               . $r['id_marche'] . '",';
    $out .= '"m_cpv_code":'         . $r['code_cpv'] . ",";
    $out .= '"m_cpv_libelle":"'     . $r['libelle_cpv'] . '",';
    $out .= '"m_acheteur":"'        . clean($r['nom_acheteur']) . '",';
    $out .= '"m_acheteur_siret":"SIRET - ' . $r['id_acheteur'] . '",';
    $out .= '"m_acheteur_btn":'     . $r['id_acheteur'] . ',';
    $out .= '"m_titulaire":"'       . clean($r['denomination_sociale']) . '",';
    $out .= '"m_titulaire_btn":'    . $r['id_titulaire'] . ',';
    $out .= '"m_titulaire_siret":"'  . $r['type_identifiant'] . ' - ' . $r['id_titulaire'] . '",';
    $out .= '"m_procedure": "'      . $r['nom_procedure'] . '",';
    $out .= '"m_nature": "'         . $r['nom_nature'] . '",';
    $out .= '"m_forme_prix": "'     . $r['nom_forme_prix'] . '",';
    $out .= '"m_date_notification": "' . $r['date_notification'] . '",';
    $out .= '"m_duree": "'          . $r['duree_mois'] . ' mois",';
    $out .= '"m_montant": "'        . number_format ($r['montant'], 0, ",", " ") . ' €",';
    $out .= '"m_lieu": "'           . $r['code_dept'] . " - " . $r['nom_dept'] . '",';
    // $o = str_replace(['"', "\r", "\n"], [" ","","<br>"], $r['objet']);
    // $o = preg_replace('/\t+/', '',  $o);
    $out .= '"m_objet": "'          . clean($r['objet']) . '"}';
     // '"m_objet": "'          . $o . '"}';
  }
}
catch ( Exception $e )
{
  echo 'Erreur : ' . $e->getMessage();
}
finally
{
  $connect->close();
  echo ( $out );
}
?>
