<?php
header('Content-Type: text/plain; charset=utf-8');

// if (!isset($_GET)) return $out;
// if (!isset($_GET['i'])) return $out;
// if (!is_numeric($_GET['i'])) return $out;

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');

$sql = "SELECT l.nom_lieu, l.code, count(l.nom_lieu) nombre
FROM `marche` m
INNER JOIN lieu l ON l.id_lieu = m.id_lieu_execution
GROUP BY (l.nom_lieu)";

try
{
  $result = $connect->query( $sql );

  $depts = "";
  $nombre = "";

  while( $r = mysqli_fetch_assoc( $result ) )
  {
    $depts .= '"' . $r['nom_lieu']. '",';
    $nombre .= $r['nombre']. ',';
  }
}
catch ( Exception $e )
{
  echo 'Erreur : ' . $e->getMessage();
}
finally
{
  $connect->close();
  // if ($out != "[") $out = substr($out, 0, -1);
  // $out .= "]";
  $depts = substr($depts, 0, -1);
  $nombre = substr($nombre, 0, -1);

  echo ( $depts . "\n" . $nombre );
    // echo  $sql;
}

  ?>
