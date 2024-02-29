<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);


$months = filter_input(INPUT_GET, 'm', FILTER_VALIDATE_INT);

if (!$months && !isset($months))
  return 0;
if (!$months)
  return 0;

$id_acheteur = filter_input(INPUT_GET, 'i', FILTER_VALIDATE_INT);
if (!$id_acheteur){
    return 0;
}


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

try {
  $sql = "SELECT a.id_acheteur id, a.nom_acheteur nom, sum(m.`montant`) montant
  FROM marche m
  INNER JOIN acheteur a ON m.id_acheteur = a.id_acheteur ";

  if (isset($id_acheteur)) {
    $sql .= " INNER JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
    INNER JOIN titulaire t ON mt.id_titulaires = t.id_titulaire ";
  }

  $sql .= "WHERE m.date_notification > '0000-00-00' ";

  if ($months > 0) {
    $sql .= " AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)";
  }

  if (isset($id_acheteur)) {
    $sql .= " AND t.id_titulaire = " . $id_acheteur . " ";
  }

  $sql .= " GROUP BY a.nom_acheteur
  ORDER BY nom ASC ";


  $result = $connect->query($sql);
  $num_rows = 0;
  $out = '{ "data" :[';

  while ($r = mysqli_fetch_assoc($result)) {
    $num_rows++;
    $out .= '{"details":"<a class=\"button  is-info is-small\" href=\"acheteur.php?i='
      . $r['id'] . '\"><i class=\"fas fa-link\"></i>&nbsp;Page de l\'acheteur</a>",'
      . '"nom":"' . hsc(clean($r['nom'])) . '",'
      . '"montant":"' . hsc($r['montant']) . '"},';
  }
  $out = substr($out, 0, -1);
  $out .= "]}";

  //// No data :`(
  if ($num_rows === 0) {
    $out = '{ "data" :[]}';
  }

  echo ($out);

  mysqli_free_result($result);
} catch (Exception $e) {
}
?>