<?php
// header('Content-Type: text/plain; charset=utf-8');
header('Content-Type: application/json; charset=utf-8');
require_once('connect.php');
require_once('model.php');

$out = 0;

if (!isset($_GET)) echo $out;

$types_marches = ['services', 'travaux', 'fournitures'];
if ( ! in_array($_GET['type'], $types_marches)) return $out;

$type = $_GET['type'];

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
$connect->set_charset("utf8"); // nexesario pra real_escape_string

// LEFT, parce qu'il y a des vides
$sql = 'SELECT m.id_marche, nom_acheteur, code_cpv, libelle_cpv, montant
        FROM `marche` m
        LEFT JOIN acheteur a ON a.id_acheteur = m.id_acheteur
        LEFT JOIN cpv c ON c.id_cpv = m.code_cpv
        WHERE categorie = "' . $type . '"
        ORDER BY montant DESC ';


try
{
  $result = $connect->query( $sql );
  $num_rows = 0;
  $out = '{ "data" :[';

    while ( $r = mysqli_fetch_assoc( $result ) )
    {
      $num_rows++;


      $out .= '{"details":"<a class=\"button  is-info is-small\" href=\"marche.php?i='
          . $r['id_marche'] . '\"><i class=\"fas fa-info-circle\"></i>&nbsp;Détails</a>", "acheteur":"'.clean($r['nom_acheteur'])
        .'","libelle_cpv":"'.clean($r['libelle_cpv'])
        .'","montant":"'.$r['montant']
        // .'","titulaire":"'.clean($r['nom_entreprise'])
        .' "},';
    }
    $out = substr($out, 0, -1);
    $out .="]}";

  //// No data :`(
  if ($num_rows === 0)
  {
    $out = '{ "data" :[]}';
  }
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
