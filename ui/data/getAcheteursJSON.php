<?php
// header('Content-Type: text/plain; charset=utf-8');
header('Content-Type: application/json; charset=u/tf-8');
// header('Content-Type: text/html; charset=utf-8');

// TODO: protexer

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

try
{
  $sql = "SELECT a.nom_acheteur nom, sum(m.`montant`) montant
  FROM `marche` m
  INNER JOIN acheteur a ON m.id_acheteur = a.id_acheteur
  GROUP BY a.nom_acheteur
  ORDER BY nom ASC ";

  $result = $connect->query($sql);
  // No data
  if (mysqli_num_rows($result) === 0)
  {
    return null;
  }

  // data
  $out = '[';

  while($r = mysqli_fetch_assoc($result))
  {
    $out .= '{"id": "'    . strtolower(str_replace(" ", "_", $r['nom'])) . '", ';
    $out .= '"nom": "'     . clean($r['nom']) . '",';
    $out .= '"montant": "' . $r['montant'] . '"},';
  }
  $out = substr($out, 0, -1);
  $out .="]"; 
  echo $out;

  mysqli_free_result($result);
}
catch ( Exception $e )
{
  echo 'Erreur : ' . $e->getMessage();
}
?>
