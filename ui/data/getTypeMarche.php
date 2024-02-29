<?php
// header('Content-Type: text/plain; charset=utf-8');
header('Content-Type: application/json; charset=utf-8');
error_reporting(0);


require_once('connect.php');
require_once('model.php');

$out = 0;

$options=array("options"=>array("regexp"=>"/^services|travaux|fournitures/"));
$type_param = filter_var($string, FILTER_VALIDATE_REGEXP,$options);

if (!$type_param || !isset($type_param))
  echo $out;
$type = $type_param;

$types_marches = ['services', 'travaux', 'fournitures'];
if (!in_array($type, $types_marches))
  return $out;


/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
select
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
$connect->set_charset("utf8"); // nexesario pra real_escape_string
$type = '%' . $type . '%';
$stmt = $connect->prepare('
  SELECT m.id_marche, nom_acheteur, code_cpv, libelle_cpv, montant
  FROM `marche` m
  LEFT JOIN acheteur a ON a.id_acheteur = m.id_acheteur
  LEFT JOIN cpv c ON c.id_cpv = m.code_cpv
  WHERE categorie = ?
  ORDER BY montant DESC
');
$stmt->bind_param("s", $type);
$stmt->execute();
$result = $stmt->get_result();

try {
  $num_rows = 0;
  $out = '{ "data" :[';

  while ($r = mysqli_fetch_assoc($result)) {
    $num_rows++;


    $out .= '{"details":"<a class=\"button  is-info is-small\" href=\"marche.php?i='
      . hsc($r['id_marche']) . '\"><i class=\"fas fa-info-circle\"></i>&nbsp;Détails</a>", "acheteur":"' . hsc(clean($r['nom_acheteur']))
      . '","libelle_cpv":"' . hsc(clean($r['libelle_cpv']))
      . '","montant":"' . hsc($r['montant'])
      . ' "},';
  }
  $out = substr($out, 0, -1);
  $out .= "]}";

  //// No data :`(
  if ($num_rows === 0) {
    $out = '{ "data" :[]}';
  }
  echo $out;
} catch (Exception $e) {
} finally {
  $connect->close();
}
?>