<?php
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);

$out = "[{";

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
require_once('model.php');

$sql = "SELECT l.nom_lieu, l.code, count(l.nom_lieu) nombre
        FROM `marche` m
        INNER JOIN lieu l ON l.id_lieu = m.id_lieu_execution
        GROUP BY (l.nom_lieu)";

try {
  $result = $connect->query($sql);

  while ($r = mysqli_fetch_assoc($result)) {
    $out .= '"' . hsc($r['nom_lieu']) . '": [' . hsc($r['nombre']) . '],';
  }
} catch (Exception $e) {
  // echo 'Erreur : ' . $e->getMessage();
} finally {
  $connect->close();
  $out = substr($out, 0, -1);
  $out = $out . "}]";
  echo ($out);
}
?>