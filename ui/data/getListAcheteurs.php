<?php
// header('Content-Type: text/plain; charset=utf-8');
header('Content-Type: application/json; charset=utf-8');
// header('Content-Type: text/html; charset=utf-8');

// TODO: protexer
if (!is_numeric($_GET['m'])) return 0;
$months = $_GET['m'];

if (isset($_GET['i']))
{
  if (!is_numeric($_GET['i'])) return 0;
}

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

try
{
  $sql = "SELECT a.id_acheteur id, a.nom_acheteur nom, sum(m.`montant`) montant
  FROM marche m
  INNER JOIN acheteur a ON m.id_acheteur = a.id_acheteur ";

  if (isset($_GET['i']))
  {
    $sql .= " INNER JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
    INNER JOIN titulaire t ON mt.id_titulaires = t.id_titulaire ";
  }

  $sql .= "WHERE m.date_notification > '0000-00-00' ";

  if ($months > 0)
  {
    $sql .= " AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)";
  }

  if (isset($_GET['i']))
  {
    $sql .= " AND t.id_titulaire = " . $_GET['i'] . " ";
  }

  $sql .= " GROUP BY a.nom_acheteur
  ORDER BY nom ASC ";
 

  $result = $connect->query($sql);
  $num_rows = 0;
  $out = '{ "data" :[';

  while($r = mysqli_fetch_assoc($result))
  {
    $num_rows++;
    $out .= '{"details":"<a class=\"button  is-info is-small\" href=\"acheteur.php?i='
        . $r['id'] . '\"><i class=\"fas fa-link\"></i>&nbsp;Page de l\'acheteur</a>",'
        . '"nom":"'            . clean($r['nom']) . '",'
        . '"montant":"'       . $r['montant'] . '"},';
  }
  $out = substr($out, 0, -1);
  $out .="]}";

  //// No data :`(
  if ($num_rows === 0)
  {
    $out = '{ "data" :[]}';
  }

  echo ( $out );
  // echo $out;

  mysqli_free_result($result);
}
catch ( Exception $e )
{
  echo 'Erreur : ' . $e->getMessage();
}
?>
