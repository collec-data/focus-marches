<?php
// header('Content-Type: text/plain; charset=utf-8');
header('Content-Type: application/json; charset=utf-8');
// header('Content-Type: text/html; charset=utf-8');

// TODO: protexer
// nb mois
if (!is_numeric($_GET['m'])) echo 0;
if (isset($_GET['i']))
{
  if (!is_numeric($_GET['i'])) echo 0;
  $id = $_GET['i'];
}
if (isset($_GET['d']))
{
  $date_min = $_GET['d'];
}
$months = $_GET['m'];

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

/*
SELECT *
  FROM marche m
  WHERE m.id_acheteur = 2458044065646
  AND date_notification > '0000-00-00'

*/
try
{
  $sql = "SELECT t.id_titulaire id, t.denomination_sociale nom, sum(m.`montant`) montant
          FROM `marche` m
          INNER JOIN marche_titulaires mt ON m.id_marche = mt.id_marche
          INNER JOIN titulaire t ON mt.id_titulaires = t.id_titulaire
          WHERE m.date_notification > '0000-00-00' ";

          if (isset($id))
          {
            $sql .= " AND m.id_acheteur = $id ";
          }

          if ($months > 0)
          {
            $sql .= " AND m.date_notification > DATE_SUB(CURRENT_DATE(), INTERVAL $months MONTH)";
          }

          $sql .= "
          GROUP BY t.denomination_sociale
          ORDER BY nom ASC ";
// echo $sql;
  $result = $connect->query($sql);
  $num_rows = 0;
  $out = '{ "data" :[';


  while($r = mysqli_fetch_assoc($result))
  {
    $num_rows++;
    $out .= '{"details":"<a class=\"button  is-info is-small\" href=\"titulaire.php?i='
        . $r['id'] . '\"><i class=\"fas fa-link\"></i>&nbsp;Page du fournisseur</a>",'
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
  echo ($out);

  mysqli_free_result($result);
}
catch ( Exception $e )
{
  echo 'Erreur : ' . $e->getMessage();
}
?>
