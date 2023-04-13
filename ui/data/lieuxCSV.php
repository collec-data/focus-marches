<?php
header('Content-Type: text/plain; charset=utf-8');
error_reporting(0);

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

  $depts = "";
  $nombre = "";

  while ($r = mysqli_fetch_assoc($result)) {
    $depts .= '"' . hsc($r['nom_lieu']) . '",';
    $nombre .= hsc($r['nombre']) . ',';
  }
} catch (Exception $e) {
  // echo 'Erreur : ' . $e->getMessage();
} finally {
  $connect->close();
  $depts = substr($depts, 0, -1);
  $nombre = substr($nombre, 0, -1);

  echo ($depts . "\n" . $nombre);
}

?>