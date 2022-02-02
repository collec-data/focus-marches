<?php
header('Content-Type: application/json; charset=utf-8');

$out = "[{";

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

  while( $r = mysqli_fetch_assoc( $result ) )
  {
    $out .= '"' . $r['nom_lieu']. '": [' . $r['nombre'] . '],';
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
  $out = substr($out, 0, -1);
  $out = $out . "}]";
  echo ( $out );
}
?>
