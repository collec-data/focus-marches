<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);


// nb mois
if (!isset($_GET['m']))
  echo 0;
if (!is_numeric($_GET['m']))
  echo 0;
$months = $_GET['m'];
if ($months < 1)
  $months = 1;

// id acheteur (optionnel)
$id = "%";
if (isset($_GET['i'])) {
  if (is_numeric($id))
    $id = $_GET['i'];
}


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string


try {

  $stmt = $connect->prepare(
    "SELECT t.id_titulaire id, t.denomination_sociale nom, sum(m.`montant`) montant
          FROM `marche` m
          INNER JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
          INNER JOIN titulaire t ON mt.id_titulaires = t.id_titulaire
          WHERE m.date_notification > '0000-00-00'
          AND m.id_acheteur LIKE ?
          AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL ? MONTH)
          GROUP BY t.denomination_sociale
          ORDER BY nom ASC
  "
  );

  $stmt->bind_param("ss", $id, $months);
  $stmt->execute();
  $result = $stmt->get_result();

  $num_rows = 0;
  $out = '{ "data" :[';

  while ($r = mysqli_fetch_assoc($result)) {
    $num_rows++;
    $out .= '{"details":"<a class=\"button  is-info is-small\" href=\"titulaire.php?i='
      . hsc($r['id']) . '\"><i class=\"fas fa-link\"></i>&nbsp;Page du fournisseur</a>",'
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