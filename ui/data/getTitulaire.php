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
$sql = 'SELECT m.id_marche, objet, m.id_acheteur, m.id_titulaire, nom_acheteur,
        denomination_sociale, m.code_cpv, libelle_cpv, duree_mois,
        montant, date_notification, id_titulaire, type_identifiant
        FROM `marche` m
        LEFT JOIN acheteur a ON a.id_acheteur = m.id_acheteur
        LEFT JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
        LEFT JOIN titulaire t ON t.id_titulaire = mt.id_titulaires
        LEFT JOIN cpv c ON c.id_cpv = m.code_cpv
        WHERE m.id_marche = ' . $_POST['id'];
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
    $out .= "<tr><td>CPV</td><td>" . $r['code_cpv'] . ' - ' . $r['libelle_cpv'] . "</td></tr>\n";
    $out .= "<tr><td>Acheteur</td><td>" . htmlspecialchars($r['nom_acheteur'], ENT_QUOTES)
          . " <a class='plus-acheteur' data-id-acheteur='" . $r['id_acheteur'] . "'>Tous ses marchés</a></td></tr>\n";
    $out .= "<tr><td>Titulaire</td><td>" . htmlspecialchars($r['denomination_sociale'], ENT_QUOTES)
          . " <a class='plus-titulaire' data-id-titulaire='" . $r['id_titulaire'] . "'>Tous ses marchés</a></td></tr>\n";
    $out .= "<tr><td>Identification du titulaire</td><td>" . htmlspecialchars($r['id_titulaire'], ENT_QUOTES) . " (" . $r['type_identifiant'] . ")</td></tr>\n";
    $out .= "<tr><td>Date de notification</td><td>" . $r['date_notification'] . "</td></tr>\n";
    $out .= "<tr><td>Durée</td><td>" . $r['duree_mois'] . "</td></tr>\n";
    $out .= "<tr><td>Montant</td><td>" . number_format ($r['montant'], 0, ",", " ") . " €</td></tr>\n";
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
