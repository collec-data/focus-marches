<?php
// header('Content-Type: text/plain; charset=utf-8');
// header('Content-Type: application/json; charset=u/tf-8');
header('Content-Type: text/html; charset=utf-8');

$out = "<ul>";

if (!isset($_POST)) return $out;

// TODO: protexer

if (!isset($_POST['entite'])) return $out;

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
require_once('connect.php');
$connect->set_charset("utf8"); // nexesario pra real_escape_string

// BUG: id_acheteur contiens des faux ids ou repetés ou créés repetés
$sql = 'SELECT DISTINCT id_titulaire, denomination_sociale
        FROM `titulaire` m
        WHERE denomination_sociale LIKE "%' . $_POST['entite'] . '%"';

// echo $sql;die;

try
{
  $result = $connect->query( $sql );
  while ( $r = mysqli_fetch_assoc( $result ) )
  {
    // $out .= '{"id":"' . $r['id_acheteur'] . '",' .
    //         '"libelle":"' . htmlspecialchars($r['nom_acheteur'], ENT_QUOTES) . '"},';
    $out .= '<li class="' . $r['id_titulaire'] . '">' .
            htmlspecialchars($r['denomination_sociale'], ENT_QUOTES) . '</li>';
  }
  $out .="</ul>";
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
