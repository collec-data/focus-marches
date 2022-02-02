<?php
header('Content-Type: text/html; charset=utf-8');

$out = 0;

if (!isset($_POST)) return $out;
if (!isset($_POST['id'])) return $out;
if (!is_numeric($_POST['id'])) return $out;

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
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
        WHERE m.id_marche = ' . $_POST['id'];

// echo $sql;die;
// log queries - sudo chgrp www-data /var/www/html/
// $file = 'queries.txt';
// $log = date("y-m-d H:i:s") . "\n" . $sql . "\n\n";
// file_put_contents($file, $log, FILE_APPEND );


try
{
  $result = $connect->query( $sql );
  $num_rows = $result->num_rows;
  if ($num_rows === 0)
  {
    $out = $sql;
    return $out;
  }

  // $out = "<ul id='detailsMarche'>\n";
  $out = "<table id='detailsMarche' class='table'>\n";
  $out .= "<thead>\n<tr>\n";

  while ( $r = mysqli_fetch_assoc( $result ) )
  {
    $out .= "<th>Marché n°</th><th>" . $r['id_marche'] . "</th>";
    $out .= "</tr>\n<thead>\n";
    $out .= "<tbody>\n";
    // $out .= "<tr><td>CPV</td><td>" . $r['code_cpv'] . ' - ' . $r['libelle_cpv'] . "</td></tr>\n";
    $out .= "<tr><td>CPV</td><td><span class='tag is-warning plus-cpv' title='Chercher tous les marchés qui correspondent à ce code CPV' data-cpv='" . $r['code_cpv'] . "'>" . $r['code_cpv'] . '</span> ' . $r['libelle_cpv'] . "</td></tr>\n";


    $out .= "<tr><td>Acheteur</td><td>" . htmlspecialchars($r['nom_acheteur'], ENT_QUOTES)
          . " <span class='tag is-info plus-acheteur ' title='Chercher tous les marchés de cet acheteur' data-id-acheteur='" . $r['id_acheteur'] . "'>Tous ses marchés</span></td></tr>\n";
    $out .= "<tr><td>Titulaire<br></td><td>" . htmlspecialchars($r['denomination_sociale'], ENT_QUOTES)
          . " <span class='tag is-info plus-titulaire '  title='Chercher tous les marchés gagnés par ce titulaire' data-id-titulaire='" . $r['id_titulaire'] . "'>Tous ses marchés</span>  <span class='tag' disabled>dashboard entreprise</span>  <span class='tag'>ou sirene</span></td></tr>\n";
    $out .= "<tr><td>Identification du titulaire</td><td>" . htmlspecialchars($r['id_titulaire'], ENT_QUOTES) . " (" . $r['type_identifiant'] . ")</td></tr>\n";
    $out .= "<tr><td>Procédure</td><td>" . $r['nom_procedure'] . "</td></tr>\n";
    $out .= "<tr><td>Nature</td><td>" . $r['nom_nature'] . "</td></tr>\n";
    $out .= "<tr><td>Forme de prix</td><td>" . $r['nom_forme_prix'] . "</td></tr>\n";
    $out .= "<tr><td>Date de notification</td><td>" . $r['date_notification'] . "</td></tr>\n";
    $out .= "<tr><td>Durée</td><td>" . $r['duree_mois'] . " mois</td></tr>\n";
    $out .= "<tr><td>Montant</td><td>" . number_format ($r['montant'], 0, ",", " ") . " €</td></tr>\n";
    $out .= "<tr><td>Lieu d'éxécution</td><td>" . $r['code_dept'] . " - " . $r['nom_dept'] . "</td></tr>\n";
    $out .= "<tr><td>Objet</td><td>" . htmlspecialchars($r['objet'], ENT_QUOTES) . "</td></tr>\n" ;
  }
  $out .="</tbody>\n</table>";
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
